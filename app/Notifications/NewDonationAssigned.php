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
            'body' => "تم إسناد مساهمة جديدة ({$this->donation->code}) لمنطقتك.",
            'url' => route('admin.donations.show', $this->donation),
            'type' => 'donation',
            'donation_id' => $this->donation->id,
        ];
    }
}
