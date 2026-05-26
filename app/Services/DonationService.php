<?php

namespace App\Services;

use App\Models\Allocation;
use App\Models\Donation;

class DonationService
{
    public function __construct(
        private readonly CodeGenerator $codes,
        private readonly StatusLogService $logs,
    ) {}

    public function create(array $data): Donation
    {
        $data['code'] = $this->codes->unique('DON', Donation::class);

        return Donation::create($data);
    }

    public function setStatus(Donation $donation, string $status, ?string $note = null): Donation
    {
        $from = $donation->status;
        $donation->forceFill(['status' => $status])->save();
        $this->logs->log($donation, $from, $status, $note);

        return $donation;
    }

    public function allocate(Donation $donation, int $beneficiaryRequestId, int $areaId): Allocation
    {
        $allocation = Allocation::create([
            'donation_id' => $donation->id,
            'beneficiary_request_id' => $beneficiaryRequestId,
            'area_id' => $areaId,
            'status' => 'assigned',
        ]);

        $this->setStatus($donation, 'allocated', 'تم تخصيص التبرع لمنطقة توزيع.');

        return $allocation;
    }
}
