<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\SecurityQuestion;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index()
    {
        $users = User::with('createdBy')->latest()->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:admin,staff'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', __('messages.user_created_successfully'));
    }

    /**
     * Show the form for editing a user
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        // Prevent editing seeded admin's email and role
        $isSeedAdmin = $user->email === 'bdm@gmail.com';

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:admin,staff'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        // Prevent changing seeded admin's role and email
        if ($isSeedAdmin) {
            unset($validated['role']);
            unset($validated['email']);
        }

        $user->name = $validated['name'];
        
        if (!$isSeedAdmin) {
            $user->email = $validated['email'];
            $user->role = $validated['role'];
        }

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()
            ->route('admin.users.index')
            ->with('success', __('messages.user_updated_successfully'));
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        // Prevent deletion of seeded admin
        if ($user->email === 'bdm@gmail.com') {
            return redirect()
                ->route('admin.users.index')
                ->with('error', __('messages.cannot_delete_admin'));
        }

        // Prevent self-deletion
        if ($user->id === Auth::id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', __('messages.cannot_delete_yourself'));
        }

        // Preserve all data created by this user by setting user_id to NULL
        // This ensures customers, employees, logs, expenses, etc. remain in the system
        \DB::table('customers')->where('user_id', $user->id)->update(['user_id' => null]);
        \DB::table('employees')->where('user_id', $user->id)->update(['user_id' => null]);
        \DB::table('customer_logs')->where('user_id', $user->id)->update(['user_id' => null]);
        \DB::table('daily_expenses')->where('user_id', $user->id)->update(['user_id' => null]);
        \DB::table('users')->where('created_by', $user->id)->update(['created_by' => null]);

        // Now delete the user
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', __('messages.user_deleted_successfully'));
    }
}
