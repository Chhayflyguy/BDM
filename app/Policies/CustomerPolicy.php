<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CustomerPolicy
{
    /**
     * Determine whether the user can view the model.
     * All authenticated users can view all customers
     */
    public function view(User $user, Customer $customer): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     * All authenticated users can update all customers
     */
    public function update(User $user, Customer $customer): bool
    {
        return true;
    }
}