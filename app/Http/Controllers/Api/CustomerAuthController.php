<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CustomerAuthController extends Controller
{
    /**
     * Customer self-registration via the app.
     * Creates a new Customer row with registered_by = 'self'.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'phone'    => 'required|string|max:20|unique:customers,phone',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Generate unique customer GID
        do {
            $customerGid = (string) random_int(100000, 999999);
        } while (Customer::where('customer_gid', $customerGid)->exists());

        $customer = Customer::create([
            'name'          => $validated['name'],
            'phone'         => $validated['phone'],
            'app_password'  => Hash::make($validated['password']),
            'registered_by' => 'self',
            'user_id'       => null,
            'customer_gid'  => $customerGid,
        ]);

        $token = $customer->createToken('customer-app-token')->plainTextToken;

        return response()->json([
            'message'  => 'Registration successful.',
            'token'    => $token,
            'customer' => [
                'id'            => $customer->id,
                'name'          => $customer->name,
                'phone'         => $customer->phone,
                'customer_gid'  => $customer->customer_gid,
                'registered_by' => $customer->registered_by,
            ],
        ], 201);
    }

    /**
     * Customer login via phone + password.
     */
    public function login(Request $request)
    {
        $request->validate([
            'phone'    => 'required|string',
            'password' => 'required|string',
        ]);

        $customer = Customer::where('phone', $request->phone)->first();

        if (!$customer || !$customer->app_password || !Hash::check($request->password, $customer->app_password)) {
            throw ValidationException::withMessages([
                'phone' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Revoke all previous tokens for a clean login
        $customer->tokens()->delete();

        $token = $customer->createToken('customer-app-token')->plainTextToken;

        return response()->json([
            'message'  => 'Login successful.',
            'token'    => $token,
            'customer' => [
                'id'            => $customer->id,
                'name'          => $customer->name,
                'phone'         => $customer->phone,
                'customer_gid'  => $customer->customer_gid,
                'registered_by' => $customer->registered_by,
            ],
        ], 200);
    }

    /**
     * Logout — revoke the current token.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ], 200);
    }

    /**
     * Return the authenticated customer's profile.
     */
    public function me(Request $request)
    {
        $customer = $request->user();

        return response()->json([
            'customer' => [
                'id'            => $customer->id,
                'name'          => $customer->name,
                'phone'         => $customer->phone,
                'customer_gid'  => $customer->customer_gid,
                'registered_by' => $customer->registered_by,
                'gender'        => $customer->gender,
                'age'           => $customer->age,
            ],
        ], 200);
    }

    /**
     * Update the authenticated customer's name and phone.
     * Password changes must go through the admin (Laravel panel).
     */
    public function updateProfile(Request $request)
    {
        $customer = $request->user();

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:customers,phone,' . $customer->id,
        ]);

        $customer->update([
            'name'  => $validated['name'],
            'phone' => $validated['phone'],
        ]);

        return response()->json([
            'message'  => 'Profile updated successfully.',
            'customer' => [
                'id'    => $customer->id,
                'name'  => $customer->name,
                'phone' => $customer->phone,
            ],
        ], 200);
    }
}
