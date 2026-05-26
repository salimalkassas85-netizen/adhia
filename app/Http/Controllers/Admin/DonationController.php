<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AllocateDonationRequest;
use App\Http\Requests\UpdateDonationStatusRequest;
use App\Models\Area;
use App\Models\BeneficiaryRequest;
use App\Models\Donation;
use App\Services\DonationService;
use App\Support\AdminAreaScope;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    public function index(Request $request, AdminAreaScope $scope)
    {
        $donations = $scope->donations()
            ->with(['donorArea', 'targetArea', 'assignedAdmin', 'allocations.beneficiaryRequest.area'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->query('status')))
            ->latest();

        return view('admin.donations.index', [
            'donations' => $donations->paginate(20)->withQueryString(),
            'selectedStatus' => $request->query('status'),
        ]);
    }

    public function show(Donation $donation, AdminAreaScope $scope)
    {
        abort_unless($scope->canAccessDonation($donation), 403);

        $areaId = $scope->areaId();

        return view('admin.donations.show', [
            'donation' => $donation->load(['donorArea', 'targetArea', 'assignedAdmin', 'allocations.beneficiaryRequest.area', 'statusLogs.user']),
            'areas' => Area::where('active', true)->when($areaId, fn ($query) => $query->where('id', $areaId))->orderBy('name')->get(),
            'requests' => BeneficiaryRequest::whereIn('status', ['pending', 'approved'])
                ->when($areaId, fn ($query) => $query->where('area_id', $areaId))
                ->whereDoesntHave('allocations')
                ->orderByDesc('created_at')
                ->get(),
        ]);
    }

    public function confirm(Donation $donation, DonationService $service, AdminAreaScope $scope)
    {
        abort_unless($scope->canAccessDonation($donation), 403);
        $service->setStatus($donation, 'received', 'تم الاستلام من المتبرع بواسطة أدمن المنطقة.');

        return back()->with('status', 'تم تسجيل الاستلام من المتبرع.');
    }

    public function receive(Donation $donation, DonationService $service, AdminAreaScope $scope)
    {
        abort_unless($scope->canAccessDonation($donation), 403);
        $service->setStatus($donation, 'received', 'تم الاستلام من المتبرع بواسطة أدمن المنطقة.');

        return back()->with('status', 'تم تسجيل الاستلام من المتبرع.');
    }

    public function allocate(AllocateDonationRequest $request, Donation $donation, DonationService $service, AdminAreaScope $scope)
    {
        abort_unless($scope->canAccessDonation($donation), 403);

        $areaId = $scope->areaId();
        abort_unless($areaId === null || $request->integer('area_id') === (int) $areaId, 403);
        abort_unless($scope->requests()->whereKey($request->integer('beneficiary_request_id'))->exists(), 403);

        $service->allocate($donation, $request->integer('beneficiary_request_id'), $request->integer('area_id'));

        return back()->with('status', 'تم ربط المساهمة بطلب هدية واحد.');
    }

    public function status(UpdateDonationStatusRequest $request, Donation $donation, DonationService $service, AdminAreaScope $scope)
    {
        abort_unless($scope->canAccessDonation($donation), 403);
        $service->setStatus($donation, $request->validated('status'), $request->validated('note'));

        return back()->with('status', 'تم تحديث حالة طلب التوصيل.');
    }

    /**
     * Create delivery bond/receipt - super admin only.
     * Protected by global.admin middleware in routes.
     */
    public function deliveryBond(Request $request, Donation $donation, DonationService $service)
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $service->setStatus($donation, 'completed', 'تم إصدار سند تسليم.');

        return back()->with('status', 'تم إصدار سند التسليم بنجاح.');
    }
}
