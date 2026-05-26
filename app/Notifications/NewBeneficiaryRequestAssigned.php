<?php

namespace App\Notifications;

use App\Models\BeneficiaryRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewBeneficiaryRequestAssigned extends Notification
{
    use Queueable;

    public function __construct(
        public readonly BeneficiaryRequest $beneficiaryRequest,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'طلب هدية جديد',
            'body' => "طلب هدية جديد ({$this->beneficiaryRequest->code}) في منطقتك. عند ربطه بمساهمة ستكون مسؤولًا عن الاستلام والتسليم.",
            'url' => route('admin.beneficiary-requests.show', $this->beneficiaryRequest),
            'type' => 'beneficiary_request',
            'request_id' => $this->beneficiaryRequest->id,
        ];
    }
}
