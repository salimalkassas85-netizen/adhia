<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBeneficiaryRequest;
use App\Models\Area;
use App\Models\BeneficiaryRequest;
use App\Services\BeneficiaryRequestService;

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

    public function cases(Area $area)
    {
        $cases = BeneficiaryRequest::where('area_id', $area->id)
            ->whereIn('status', ['pending', 'approved'])
            ->whereDoesntHave('allocations')
            ->withCount('allocations as received_donations_count')
            ->orderBy('received_donations_count')
            ->oldest()
            ->get()
            ->map(function ($case) {
                return [
                    'id' => $case->id,
                    'family_members_count' => $case->family_members_count ?? 'غير محدد',
                    'has_children' => $case->has_children,
                    'has_elderly' => $case->has_elderly,
                    'social_status' => $case->social_status ? \App\Support\ArabicLabels::socialStatus($case->social_status) : 'غير محدد',
                    'received_donations_count' => $case->received_donations_count,
                ];
            });

        return response()->json($cases);
    }
}
