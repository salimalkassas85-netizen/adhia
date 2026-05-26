<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignBeneficiaryRequest;
use App\Http\Requests\UpdateBeneficiaryStatusRequest;
use App\Models\BeneficiaryRequest;
use App\Services\BeneficiaryRequestService;
use App\Support\AdminAreaScope;
use Illuminate\Http\Request;

class BeneficiaryRequestController extends Controller
{
    public function index(Request $request, AdminAreaScope $scope)
    {
        $requests = $scope->requests()
            ->with(['area', 'assignedAgent'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->query('status')))
            ->latest();

        return view('admin.beneficiary-requests.index', [
            'requests' => $requests->paginate(20)->withQueryString(),
            'selectedStatus' => $request->query('status'),
        ]);
    }

    public function show(BeneficiaryRequest $beneficiaryRequest, AdminAreaScope $scope)
    {
        abort_unless($scope->canAccessRequest($beneficiaryRequest), 403);

        return view('admin.beneficiary-requests.show', [
            'request' => $beneficiaryRequest->load(['area', 'assignedAgent', 'statusLogs.user']),
            'agents' => $scope->agents()->orderBy('name')->get(),
        ]);
    }

    public function approve(BeneficiaryRequest $beneficiaryRequest, BeneficiaryRequestService $service, AdminAreaScope $scope)
    {
        abort_unless($scope->canAccessRequest($beneficiaryRequest), 403);
        $service->approve($beneficiaryRequest);

        return back()->with('status', 'تم اعتماد طلب هدية العيد.');
    }

    public function assign(AssignBeneficiaryRequest $request, BeneficiaryRequest $beneficiaryRequest, BeneficiaryRequestService $service, AdminAreaScope $scope)
    {
        abort_unless($scope->canAccessRequest($beneficiaryRequest), 403);

        $agent = $scope->agents()->findOrFail($request->integer('assigned_agent_id'));
        $service->assign($beneficiaryRequest, $agent, $request->validated('admin_notes'));

        return back()->with('status', 'تم إسناد الطلب إلى فريق التوزيع.');
    }

    public function status(UpdateBeneficiaryStatusRequest $request, BeneficiaryRequest $beneficiaryRequest, BeneficiaryRequestService $service, AdminAreaScope $scope)
    {
        abort_unless($scope->canAccessRequest($beneficiaryRequest), 403);
        $service->setStatus($beneficiaryRequest, $request->validated('status'), $request->validated('note'));

        return back()->with('status', 'تم تحديث الحالة.');
    }
}
