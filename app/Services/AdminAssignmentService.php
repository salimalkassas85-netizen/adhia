<?php

namespace App\Services;

use App\Models\AdminNotification;
use App\Models\Area;
use App\Models\BeneficiaryRequest;
use App\Models\Donation;
use App\Models\User;

class AdminAssignmentService
{
    public function assignRequest(BeneficiaryRequest $request): ?User
    {
        $admin = $this->areaAdmin($request->area_id);

        if (! $admin) {
            return null;
        }

        $request->forceFill(['assigned_admin_id' => $admin->id])->save();
        $this->notify($admin, 'طلب هدية جديد', 'تم إسناد طلب هدية جديد لمنطقتك.', route('admin.beneficiary-requests.show', $request));

        return $admin;
    }

    public function assignDonation(Donation $donation): ?User
    {
        $areaId = $this->resolveDonationArea($donation);

        if (! $areaId) {
            return null;
        }

        $admin = $this->areaAdmin($areaId);

        if (! $admin) {
            return null;
        }

        $donation->forceFill([
            'target_area_id' => $donation->target_area_id ?: $areaId,
            'assigned_admin_id' => $admin->id,
        ])->save();

        $this->notify($admin, 'مساهمة جديدة', 'تم إسناد مساهمة جديدة لمنطقتك.', route('admin.donations.show', $donation));

        return $admin;
    }

    public function areaAdmin(int $areaId): ?User
    {
        return User::where('role', 'admin')->where('area_id', $areaId)->oldest()->first();
    }

    private function resolveDonationArea(Donation $donation): ?int
    {
        if ($donation->donation_scope === 'own_area') {
            return $donation->donor_area_id;
        }

        if ($donation->donation_scope === 'selected_area') {
            return $donation->target_area_id;
        }

        if ($donation->target_area_id) {
            return $donation->target_area_id;
        }

        return Area::query()
            ->withCount(['beneficiaryRequests as waiting_requests_count' => fn ($query) => $query->where('status', 'pending')])
            ->orderByDesc('waiting_requests_count')
            ->oldest()
            ->value('id');
    }

    private function notify(User $admin, string $title, string $body, string $url): AdminNotification
    {
        return AdminNotification::create([
            'user_id' => $admin->id,
            'title' => $title,
            'body' => $body,
            'url' => $url,
        ]);
    }
}
