<?php

namespace App\Services;

use App\Models\BeneficiaryRequest;
use App\Models\User;
use Illuminate\Support\Arr;

class BeneficiaryRequestService
{
    public function __construct(
        private readonly CodeGenerator $codes,
        private readonly StatusLogService $logs,
        private readonly AdminAssignmentService $adminAssignments,
    ) {}

    public function create(array $data): BeneficiaryRequest
    {
        $data['code'] = $this->codes->unique('GIFT', BeneficiaryRequest::class);
        $data['has_children'] = (bool) ($data['has_children'] ?? false);
        $data['has_elderly'] = (bool) ($data['has_elderly'] ?? false);

        $request = BeneficiaryRequest::create($data);
        $this->adminAssignments->assignRequest($request);

        return $request;
    }

    public function approve(BeneficiaryRequest $request, ?string $note = null): BeneficiaryRequest
    {
        return $this->setStatus($request, 'approved', $note, ['approved_at' => now()]);
    }

    public function assign(BeneficiaryRequest $request, User $agent, ?string $note = null): BeneficiaryRequest
    {
        $from = $request->status;

        $request->forceFill([
            'assigned_agent_id' => $agent->id,
            'status' => 'approved',
            'assigned_at' => now(),
            'admin_notes' => $note ?: $request->admin_notes,
        ])->save();

        $this->logs->log($request, $from, 'approved', $note);

        return $request;
    }

    public function setStatus(BeneficiaryRequest $request, string $status, ?string $note = null, array $extra = []): BeneficiaryRequest
    {
        $from = $request->status;
        $payload = array_merge(['status' => $status], $extra);

        if ($status === 'delivered') {
            $payload['delivered_at'] = now();
        }

        if (Arr::has($payload, 'agent_notes') === false && $note && auth()->user()?->isAgent()) {
            $payload['agent_notes'] = $note;
        }

        $request->forceFill($payload)->save();
        $this->logs->log($request, $from, $status, $note);

        return $request;
    }
}
