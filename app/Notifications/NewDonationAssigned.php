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
        return [
            'title' => 'مساهمة جديدة',
            'body' => "مساهمة جديدة ({$this->donation->code}) في منطقتك. المطلوب الاستلام من المتبرع والتسليم للحالة المختارة.",
            'url' => route('admin.donations.show', $this->donation),
            'type' => 'donation',
            'donation_id' => $this->donation->id,
        ];
    }
}
