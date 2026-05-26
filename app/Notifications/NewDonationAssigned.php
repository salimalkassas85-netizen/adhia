<?php

namespace App\Notifications;

use App\Models\Donation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewDonationAssigned extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Donation $donation,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $donation = $this->donation->loadMissing('allocations.beneficiaryRequest');
        $beneficiary = $donation->allocations->first()?->beneficiaryRequest;

        return [
            'title' => 'مساهمة جديدة لمحتاج',
            'body' => "مساهمة جديدة ({$donation->code}) وصلت لمحتاج في منطقتك. افتح صفحة المحتاج لعرض كل المتبرعين والاستلام منهم بدون لخبطة.",
            'url' => $beneficiary
                ? route('admin.beneficiary-requests.show', $beneficiary).'#donation-'.$donation->id
                : route('admin.donations.index'),
            'type' => 'donation',
            'donation_id' => $donation->id,
            'beneficiary_request_id' => $beneficiary?->id,
        ];
    }
}
