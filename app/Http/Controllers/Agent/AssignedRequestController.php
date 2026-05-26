<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateBeneficiaryStatusRequest;
use App\Models\BeneficiaryRequest;
use App\Services\BeneficiaryRequestService;

class AssignedRequestController extends Controller
{
    public function index()
    {
        return view('agent.requests.index', [
            'requests' => BeneficiaryRequest::with('area')
                ->where('assigned_agent_id', auth()->id())
                ->latest()
                ->paginate(20),
        ]);
    }

    public function show(BeneficiaryRequest $beneficiaryRequest)
    {
        $this->authorize('view', $beneficiaryRequest);

        return view('agent.requests.show', ['request' => $beneficiaryRequest->load('area')]);
    }

    public function status(UpdateBeneficiaryStatusRequest $request, BeneficiaryRequest $beneficiaryRequest, BeneficiaryRequestService $service)
    {
        $this->authorize('update', $beneficiaryRequest);

        $allowed = ['gift_received_by_agent', 'on_the_way', 'delivered', 'failed'];
        abort_unless(in_array($request->validated('status'), $allowed, true), 422);

        $service->setStatus($beneficiaryRequest, $request->validated('status'), $request->validated('note'));

        return back()->with('status', 'تم تحديث حالة التوصيل.');
    }
}
