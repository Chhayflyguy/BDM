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
    private $vipPackages = [
        'vip' => ['price' => 250, 'offer' => 50, 'validity_months' => 6, 'name' => 'VIP Card'],
        'silver' => ['price' => 500, 'offer' => 150, 'validity_months' => 6, 'name' => 'Silver Card'],
        'golden' => ['price' => 1000, 'offer' => 500, 'validity_months' => 12, 'name' => 'Golden Card'],
        'diamond' => ['price' => 2000, 'offer' => 1000, 'validity_years' => 12, 'name' => 'Diamond Card'],
    ];

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
            'vip_package' => ['nullable', 'string', Rule::in(array_keys($this->vipPackages))],
            'vip_card_number' => ['nullable', 'string', 'max:255'], // We'll validate the number part
            'gender' => 'nullable|in:Male,Female,Other',
            'age' => 'nullable|integer|min:0',
            'height' => 'nullable|string|max:10',
            'weight' => 'nullable|string|max:10',
            'health_conditions' => 'nullable|array',
            'problem_areas' => 'nullable|array',
        ]);

        $fullVipCardId = null;
        if (!empty($validated['vip_package']) && !empty($validated['vip_card_number'])) {
            $prefix = strtoupper(substr($validated['vip_package'], 0, 1)); // V, S, G, D
            $fullVipCardId = $prefix . $validated['vip_card_number'];

            // Now validate the uniqueness of the full ID
            $request->validate([
                'vip_card_id_full' => Rule::unique('customers', 'vip_card_id')
            ], ['vip_card_id_full.unique' => 'This VIP Card ID is already taken.']);
            
            $validated['vip_card_id'] = $fullVipCardId;
        }
        
        do {
            $customerGid = random_int(100000, 999999);
        } while (Customer::where('customer_gid', $customerGid)->exists());

        $validated['user_id'] = Auth::id();
        $validated['customer_gid'] = $customerGid;

        if (!empty($validated['vip_package'])) {
            $package = $this->vipPackages[$validated['vip_package']];
            $validated['vip_card_type'] = $package['name'];
            $validated['vip_card_balance'] = $package['price'] + $package['offer'];
            $validated['vip_card_expires_at'] = isset($package['validity_years']) 
                ? now()->addYears($package['validity_years']) 
                : now()->addMonths($package['validity_months']);
        }

        $customer = Customer::create($validated);

        if (!empty($validated['vip_package'])) {
            $package = $this->vipPackages[$validated['vip_package']];
            CustomerLog::create([
                'user_id' => Auth::id(), 'customer_id' => $customer->id,
                'payment_method' => 'VIP Top-Up', 'payment_amount' => $package['price'],
                'status' => 'completed', 'is_vip_top_up' => true, 'completed_at' => now()
            ]);
            Notification::route('telegram', env('TELEGRAM_CHAT_ID'))->notify(new NewCustomerCreated($customer));
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
            'vip_card_expires_at' => 'nullable|date',
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
            'vip_package' => ['required', 'string', Rule::in(array_keys($this->vipPackages))],
        ]);

        // Add to customer's balance
        $package = $this->vipPackages[$validated['vip_package']];
        
        $customer->vip_card_type = $package['name'];
        $customer->vip_card_balance += $package['price'] + $package['offer'];
        $customer->vip_card_expires_at = isset($package['validity_years']) 
            ? now()->addYears($package['validity_years']) 
            : now()->addMonths($package['validity_months']);
        $customer->save();

        CustomerLog::create([
            'user_id' => Auth::id(), 'customer_id' => $customer->id,
            'payment_method' => 'VIP Top-Up', 'payment_amount' => $package['price'],
            'status' => 'completed', 'is_vip_top_up' => true, 'completed_at' => now()
        ]);

        Notification::route('telegram', env('TELEGRAM_CHAT_ID'))->notify(new VipBalanceTopUp($customer, $package['name']));

        return redirect()->route('customers.show', $customer)->with('success', 'Balance updated successfully!');
    }
}