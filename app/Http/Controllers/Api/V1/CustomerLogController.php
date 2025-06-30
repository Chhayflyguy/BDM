<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CustomerLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Return logs for the authenticated user
        return CustomerLog::where('user_id', Auth::id())->paginate();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_gid' => 'nullable|string|max:255',
            // ... add other validation rules as needed
        ]);

        $validated['user_id'] = Auth::id();
        $log = CustomerLog::create($validated);

        return response()->json($log, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(CustomerLog $customerLog)
    {
        // Make sure the user can only view their own logs
        if (Auth::id() !== $customerLog->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return $customerLog;
    }
}
