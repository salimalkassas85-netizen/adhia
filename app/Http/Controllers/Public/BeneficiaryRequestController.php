<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBeneficiaryRequest;
use App\Models\Area;
use App\Models\BeneficiaryRequest;
use App\Services\BeneficiaryRequestService;
use Illuminate\Http\Request;

class BeneficiaryRequestController extends Controller
{
    public function create()
    {
        return view('public.request-gift', [
            'areas' => Area::where('active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(StoreBeneficiaryRequest $request, BeneficiaryRequestService $service)
    {
        $giftRequest = $service->create($request->validated());

        return redirect()->route('public.request.success', $giftRequest->code);
    }

    public function success(string $code)
    {
        $giftRequest = BeneficiaryRequest::with('allocations.donation.assignedAdmin')->where('code', $code)->firstOrFail();
        $linkedDonation = $giftRequest->allocations->first()?->donation;

        return view('public.request-success', [
            'code' => $giftRequest->code,
            'status' => $linkedDonation?->status ?? $giftRequest->status,
            'linkedDonation' => $linkedDonation,
        ]);
    }

    public function statusForm()
    {
        return view('public.request-status-form');
    }

    public function statusLookup(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50'],
        ], [
            'code.required' => 'من فضلك أدخل رقم الطلب.',
            'code.string' => 'رقم الطلب غير صحيح.',
            'code.max' => 'رقم الطلب غير صحيح.',
        ]);

        $code = trim($validated['code']);

        $exists = BeneficiaryRequest::where('code', $code)->exists();

        if (! $exists) {
            return back()
                ->withInput()
                ->withErrors(['code' => 'رقم الطلب غير موجود. من فضلك تأكد من الرقم وحاول مرة أخرى.']);
        }

        return redirect()->route('public.request.status.show', $code);
    }

    public function statusShow(string $code)
    {
        $giftRequest = BeneficiaryRequest::query()
            ->with(['area', 'allocations.donation' => fn ($query) => $query->latest()])
            ->where('code', $code)
            ->first();

        if (! $giftRequest) {
            return redirect()
                ->route('public.request.status.form')
                ->withErrors(['code' => 'رقم الطلب غير موجود. من فضلك تأكد من الرقم وحاول مرة أخرى.']);
        }

        $donations = $giftRequest->allocations
            ->pluck('donation')
            ->filter()
            ->reject(fn ($donation) => $donation->status === 'cancelled')
            ->sortByDesc('created_at')
            ->values();

        return view('public.request-status', [
            'giftRequest' => $giftRequest,
            'donations' => $donations,
        ]);
    }

    public function cases(Area $area)
    {
        $cases = BeneficiaryRequest::query()
            ->where('area_id', $area->id)
            ->withCount([
                'donations as allocated_donations_count' => fn ($query) => $query->where('donations.status', '!=', 'cancelled'),
            ])
            ->withSum([
                'donations as total_money_received' => fn ($query) => $query->where('donations.status', '!=', 'cancelled'),
            ], 'amount')
            ->withSum([
                'donations as total_meat_kg_received' => fn ($query) => $query->where('donations.status', '!=', 'cancelled'),
            ], 'meat_kg')
            ->orderBy('total_money_received')
            ->orderBy('total_meat_kg_received')
            ->oldest()
            ->get()
            ->map(function ($case) {
                return [
                    'id' => $case->id,
                    'family_members_count' => $case->family_members_count ?? 'غير محدد',
                    'has_children' => $case->has_children,
                    'has_elderly' => $case->has_elderly,
                    'social_status' => $case->social_status ? \App\Support\ArabicLabels::socialStatus($case->social_status) : 'غير محدد',
                    'allocated_donations_count' => (int) $case->allocated_donations_count,
                    'received_donations_count' => (int) $case->allocated_donations_count,
                    'total_money_received' => (float) ($case->total_money_received ?? 0),
                    'total_meat_kg_received' => (float) ($case->total_meat_kg_received ?? 0),
                ];
            });

        return response()->json($cases);
    }
}
