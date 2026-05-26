<?php

namespace App\Policies;

use App\Models\Donation;
use App\Models\User;

class DonationPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Donation $donation): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isAdmin() && $user->area_id) {
            return (int) $donation->target_area_id === (int) $user->area_id
                || (int) $donation->donor_area_id === (int) $user->area_id;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Donation $donation): bool
    {
        return $this->view($user, $donation);
    }

    /**
     * Determine whether the user can create delivery bonds/receipts.
     * Only super admins can do this.
     */
    public function createDeliveryBond(User $user): bool
    {
        return $user->isSuperAdmin();
    }
}
