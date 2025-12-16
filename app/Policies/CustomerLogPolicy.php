<?php

namespace App\Policies;

use App\Models\CustomerLog;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CustomerLogPolicy
{
    /**
     * Determine whether the user can update the model.
     * Only admins can edit completed logs.
     * All users can edit active logs.
     */
    public function update(User $user, CustomerLog $customerLog): bool
    {
        // If the log is completed, only admin can edit
        if ($customerLog->status === 'completed') {
            return $user->isAdmin();
        }
        
        // Active logs can be edited by all users
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     * All users can delete active logs only.
     */
    public function delete(User $user, CustomerLog $customerLog): bool
    {
        // Only allow deletion of active logs
        return $customerLog->status === 'active';
    }
}