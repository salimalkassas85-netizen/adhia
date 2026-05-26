<?php

namespace App\Policies;

use App\Models\BeneficiaryRequest;
use App\Models\User;

class BeneficiaryRequestPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, BeneficiaryRequest $beneficiaryRequest): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isAdmin() && $user->area_id) {
            return (int) $beneficiaryRequest->area_id === (int) $user->area_id;
        }

        return (int) $beneficiaryRequest->assigned_agent_id === (int) $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BeneficiaryRequest $beneficiaryRequest): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isAdmin() && $user->area_id) {
            return (int) $beneficiaryRequest->area_id === (int) $user->area_id;
        }

        return (int) $beneficiaryRequest->assigned_agent_id === (int) $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BeneficiaryRequest $beneficiaryRequest): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, BeneficiaryRequest $beneficiaryRequest): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, BeneficiaryRequest $beneficiaryRequest): bool
    {
        return false;
    }
}
