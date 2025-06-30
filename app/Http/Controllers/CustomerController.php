<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerLog; // NEW
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule; // NEW
use App\Notifications\NewCustomerCreated; // NEW
use App\Notifications\VipBalanceTopUp;   // NEW
use Illuminate\Support\Facades\Notification; // NEW

class CustomerController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        $query = Customer::where('user_id', Auth::id()); // Start query for the logged-in user

        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            // FIX: Ensure search respects the user_id constraint
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('customer_gid', 'like', $searchTerm)
                  ->orWhere('phone', 'like', $searchTerm)
                  ->orWhere('vip_card_id', 'like', $searchTerm);
            });
        }

        $customers = $query->latest()->paginate(25); // Apply ordering and pagination at the end
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'vip_card_id' => ['nullable', 'string', 'max:255', Rule::unique('customers', 'vip_card_id')],
            'vip_card_balance' => 'nullable|numeric|min:0',
            'vip_card_expires_at' => 'nullable|date',
            'gender' => 'nullable|in:Male,Female,Other',
            'age' => 'nullable|integer|min:0',
            'height' => 'nullable|string|max:10',
            'weight' => 'nullable|string|max:10',
            'health_conditions' => 'nullable|array',
            'problem_areas' => 'nullable|array',
        ]);
        do {
            $customerGid = random_int(100000, 999999);
        } while (Customer::where('customer_gid', $customerGid)->exists());

        $validated['user_id'] = Auth::id();
        $validated['customer_gid'] = $customerGid;

        $customer = Customer::create($validated);

        Notification::route('telegram', env('TELEGRAM_CHAT_ID'))->notify(new NewCustomerCreated($customer));

        if (!empty($validated['vip_card_balance']) && $validated['vip_card_balance'] > 0) {
            CustomerLog::create([
                'user_id' => Auth::id(),
                'customer_id' => $customer->id,
                'payment_method' => 'VIP Top-Up',
                'payment_amount' => $validated['vip_card_balance'],
                'status' => 'completed',
                'is_vip_top_up' => true,
                'completed_at' => now()
            ]);
        }

         return redirect()->route('customers.index')->with('success', 'New customer created successfully!');
    }

    /**
     * NEW: Display the specified customer's details and log history.
     */
    public function show(Customer $customer)
    {
        $this->authorize('view', $customer);
        // FIX: Eager load logs in descending order
        $customer->load(['logs' => function ($query) {
            $query->latest();
        }]);
        return view('customers.show', compact('customer'));
    }

    /**
     * NEW: Show the form for editing the specified customer.
     */
    public function edit(Customer $customer)
    {
        $this->authorize('update', $customer);
        return view('customers.edit', compact('customer'));
    }

    /**
     * NEW: Update the specified customer in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $this->authorize('update', $customer);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'vip_card_id' => ['nullable', 'string', 'max:255', Rule::unique('customers', 'vip_card_id')->ignore($customer->id)],
            'gender' => 'nullable|in:Male,Female,Other',
            'age' => 'nullable|integer|min:0',
            'height' => 'nullable|string|max:10',
            'weight' => 'nullable|string|max:10',
            'health_conditions' => 'nullable|array',
            'problem_areas' => 'nullable|array',
        ]);

        $customer->update($validated);

        return redirect()->route('customers.show', $customer)->with('success', 'Customer profile updated successfully!');
    }

    public function topUpVipBalance(Request $request, Customer $customer)
    {
        $this->authorize('update', $customer);

        $validated = $request->validate([
            'top_up_amount' => 'required|numeric|min:1',
            'vip_card_expires_at' => 'nullable|date',
        ]);

        // Add to customer's balance
        $customer->vip_card_balance += $validated['top_up_amount'];

        if (!empty($validated['vip_card_expires_at'])) {
            $customer->vip_card_expires_at = $validated['vip_card_expires_at'];
        }
        $customer->save();

        // Create a log for this transaction for accounting
        CustomerLog::create([
            'user_id' => Auth::id(),
            'customer_id' => $customer->id,
            'payment_method' => 'VIP Top-Up',
            'payment_amount' => $validated['top_up_amount'],
            'status' => 'completed',
            'is_vip_top_up' => true,
            'completed_at' => now()
        ]);
        
        Notification::route('telegram', env('TELEGRAM_CHAT_ID'))->notify(new VipBalanceTopUp($customer, $validated['top_up_amount']));

          return redirect()->route('customers.show', $customer)->with('success', 'Balance updated successfully!');
    }
}