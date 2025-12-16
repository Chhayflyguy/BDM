<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Models\SecurityQuestion;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     * Registration is disabled - only admins can create users.
     */
    public function create(): View
    {
        abort(403, __('messages.registration_disabled'));
    }

    /**
     * Handle an incoming registration request.
     * Registration is disabled - only admins can create users.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        abort(403, __('messages.registration_disabled'));
    }
}
