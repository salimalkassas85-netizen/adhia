<?php

namespace App\Services;

use App\Models\Allocation;
use App\Models\BeneficiaryRequest;
use App\Models\Donation;

class DonationService
{
    public function __construct(
        private readonly CodeGenerator $codes,
        private readonly StatusLogService $logs,
        private readonly AdminAssignmentService $adminAssignments,
    ) {}

    public function create(array $data): Donation
    {
        $data['code'] = $this->codes->unique('DON', Donation::class);
        $data['donation_scope'] = 'own_area';
        $data['target_area_id'] = $data['donor_area_id'] ?? null;

        $donation = Donation::create($data);
        $this->adminAssignments->assignDonation($donation);

        if (! empty($data['selected_case_id'])) {
            $areaId = $donation->target_area_id ?: $donation->donor_area_id;

            Allocation::create([
                'donation_id' => $donation->id,
                'beneficiary_request_id' => $data['selected_case_id'],
                'area_id' => $areaId,
                'status' => 'assigned',
            ]);

            $beneficiaryRequest = BeneficiaryRequest::find($data['selected_case_id']);
            $beneficiaryRequest?->forceFill([
                'assigned_admin_id' => $donation->assigned_admin_id ?: $beneficiaryRequest->assigned_admin_id,
                'assigned_at' => now(),
            ])->save();

            $this->logs->log($donation, null, 'pending', 'تم إنشاء طلب توصيل واحد: استلام من المتبرع وتسليم للحالة المختارة.');
        }

        return $donation;
    }

    public function setStatus(Donation $donation, string $status, ?string $note = null): Donation
    {
        $from = $donation->status;
        $donation->forceFill(['status' => $status])->save();
        $this->logs->log($donation, $from, $status, $note);
        $this->syncLinkedRequestsStatus($donation, $status);

        return $donation;
    }

    public function allocate(Donation $donation, int $beneficiaryRequestId, int $areaId): Allocation
    {
        if ($donation->allocations()->exists()) {
            abort(422, 'تم ربط هذه المساهمة بحالة بالفعل ولا يمكن اختيار أكثر من محتاج.');
        }

        $allocation = Allocation::create([
            'donation_id' => $donation->id,
            'beneficiary_request_id' => $beneficiaryRequestId,
            'area_id' => $areaId,
            'status' => 'assigned',
        ]);

        $allocation->beneficiaryRequest?->forceFill([
            'assigned_admin_id' => $donation->assigned_admin_id ?: auth()->id(),
            'assigned_at' => now(),
        ])->save();

        $this->logs->log($donation, $donation->status, $donation->status, 'تم ربط المساهمة بطلب هدية واحد لتصبح طلب توصيل واحد.');

        return $allocation;
    }

    private function syncLinkedRequestsStatus(Donation $donation, string $status): void
    {
        $donation->loadMissing('allocations.beneficiaryRequest');

        foreach ($donation->allocations as $allocation) {
            $allocationStatus = match ($status) {
                'received' => 'in_distribution',
                'completed' => 'delivered',
                default => 'assigned',
            };

            $allocation->forceFill(['status' => $allocationStatus])->save();

            $beneficiaryRequest = $allocation->beneficiaryRequest;
            if (! $beneficiaryRequest) {
                continue;
            }

            $beneficiaryRequest->forceFill([
                'status' => $status === 'completed' ? 'delivered' : 'pending',
                'delivered_at' => $status === 'completed' ? now() : $beneficiaryRequest->delivered_at,
            ])->save();
        }
    }
}
