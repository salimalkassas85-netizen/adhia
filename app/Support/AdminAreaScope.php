<?php

namespace App\Support;

use App\Models\BeneficiaryRequest;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class AdminAreaScope
{
    public function areaId(?User $user = null): ?int
    {
        $user ??= auth()->user();

        return $user?->isAdmin() ? $user->area_id : null;
    }

    public function isGlobal(?User $user = null): bool
    {
        $user ??= auth()->user();

        return $user?->isAdmin() && $user->area_id === null;
    }

    public function requests(?User $user = null): Builder
    {
        $query = BeneficiaryRequest::query();
        $areaId = $this->areaId($user);

        return $areaId ? $query->where('area_id', $areaId) : $query;
    }

    public function donations(?User $user = null): Builder
    {
        $query = Donation::query();
        $areaId = $this->areaId($user);

        if (! $areaId) {
            return $query;
        }

        return $query->where(function (Builder $query) use ($areaId): void {
            $query->where('target_area_id', $areaId)
                ->orWhere('donor_area_id', $areaId);
        });
    }

    public function agents(?User $user = null): Builder
    {
        $query = User::where('role', 'agent');
        $areaId = $this->areaId($user);

        return $areaId ? $query->where('area_id', $areaId) : $query;
    }

    public function admins(?User $user = null): Builder
    {
        $query = User::where('role', 'admin');
        $areaId = $this->areaId($user);

        return $areaId ? $query->where('area_id', $areaId) : $query;
    }

    public function canAccessRequest(BeneficiaryRequest $request, ?User $user = null): bool
    {
        $areaId = $this->areaId($user);

        return $areaId === null || (int) $request->area_id === (int) $areaId;
    }

    public function canAccessDonation(Donation $donation, ?User $user = null): bool
    {
        $areaId = $this->areaId($user);

        return $areaId === null
            || (int) $donation->target_area_id === (int) $areaId
            || (int) $donation->donor_area_id === (int) $areaId;
    }
}
