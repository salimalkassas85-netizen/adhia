<?php

namespace App\Services;

use App\Models\Area;
use App\Models\BeneficiaryRequest;
use App\Models\Donation;
use App\Models\User;
use App\Notifications\NewBeneficiaryRequestAssigned;
use App\Notifications\NewDonationAssigned;
use App\Notifications\SuperAdminAlert;

class AdminAssignmentService
{
    public function assignRequest(BeneficiaryRequest $request): ?User
    {
        $admin = $this->areaAdmin($request->area_id);

        if (! $admin) {
            $this->notifySuperAdmins(
                'طلب هدية بدون أدمن منطقة',
                "لا يوجد أدمن لمنطقة الطلب ({$request->code}). يرجى تعيين أدمن للمنطقة.",
                route('admin.beneficiary-requests.show', $request),
            );

            return null;
        }

        $request->forceFill(['assigned_admin_id' => $admin->id])->save();
        $admin->notify(new NewBeneficiaryRequestAssigned($request));

        $this->notifySuperAdmins(
            'طلب هدية جديد',
            "تم استقبال طلب هدية جديد ({$request->code}) وإسناده لأدمن المنطقة.",
            route('admin.beneficiary-requests.show', $request),
        );

        return $admin;
    }

    public function assignDonation(Donation $donation): ?User
    {
        $areaId = $this->resolveDonationArea($donation);

        if (! $areaId) {
            $this->notifySuperAdmins(
                'مساهمة بدون منطقة',
                "لم يتم تحديد منطقة للمساهمة ({$donation->code}). يرجى التخصيص يدويًا.",
                route('admin.donations.show', $donation),
            );

            return null;
        }

        $admin = $this->areaAdmin($areaId);

        if (! $admin) {
            $this->notifySuperAdmins(
                'مساهمة بدون أدمن منطقة',
                "لا يوجد أدمن لمنطقة المساهمة ({$donation->code}). يرجى تعيين أدمن للمنطقة.",
                route('admin.donations.show', $donation),
            );

            return null;
        }

        $donation->forceFill([
            'target_area_id' => $donation->target_area_id ?: $areaId,
            'assigned_admin_id' => $admin->id,
        ])->save();

        $admin->notify(new NewDonationAssigned($donation));

        $this->notifySuperAdmins(
            'مساهمة جديدة',
            "تم استقبال مساهمة جديدة ({$donation->code}) وإسنادها لأدمن المنطقة.",
            route('admin.donations.show', $donation),
        );

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

    private function notifySuperAdmins(string $title, string $body, ?string $url = null): void
    {
        $superAdmins = User::where('role', 'admin')->whereNull('area_id')->get();

        foreach ($superAdmins as $superAdmin) {
            $superAdmin->notify(new SuperAdminAlert($title, $body, $url));
        }
    }
}
