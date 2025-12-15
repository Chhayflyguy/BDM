<?php

namespace App\Policies;

use App\Models\CustomerLog;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CustomerLogPolicy
{
    /**
     * Determine whether the user can update the model.
     * Allow if the user is the owner (allows editing both active and completed logs).
     */
    public function update(User $user, CustomerLog $customerLog): bool
    {
        return $user->id === $customerLog->user_id;
    }

    /**
     * MODIFIED: Determine whether the user can delete the model.
     * Only allow if the user is the owner AND the log is still 'active'.
     */
    public function delete(User $user, CustomerLog $customerLog): bool
    {
        return $user->id === $customerLog->user_id && $customerLog->status === 'active';
    }
}