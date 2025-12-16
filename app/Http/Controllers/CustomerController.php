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
use App\Exports\NewCustomersExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

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
        $currentMonth = $request->input('month', now()->month);
        $currentYear = $request->input('year', now()->year);

        $query = Customer::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth);

        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                    ->orWhere('customer_gid', 'like', $searchTerm)
                    ->orWhere('phone', 'like', $searchTerm)
                    ->orWhere('vip_card_id', 'like', $searchTerm);
            });
        }

        $customers = $query->latest()->paginate(25);

        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = date('F', mktime(0, 0, 0, $i, 10));
        }
        $years = range(date('Y'), date('Y') - 5);

        return view('customers.index', compact(
            'customers',
            'months',
            'years',
            'currentMonth',
            'currentYear'
        ));
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
            'vip_card_number' => ['nullable', 'string', 'max:255'],
            'gender' => 'nullable|in:Male,Female,Other',
            'age' => 'nullable|integer|min:0',
            'height' => 'nullable|string|max:10',
            'weight' => 'nullable|string|max:10',
            'health_conditions' => 'nullable|array',
            'problem_areas' => 'nullable|array',
        ]);

        if (!empty($validated['vip_package']) && !empty($validated['vip_card_number'])) {
            $prefix = strtoupper(substr($validated['vip_package'], 0, 1));
            $fullVipCardId = $prefix . $validated['vip_card_number'];

            $validator = Validator::make(['vip_card_id' => $fullVipCardId], [
                'vip_card_id' => Rule::unique('customers', 'vip_card_id')
            ], ['vip_card_id.unique' => 'This VIP Card ID is already taken.']);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

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

        // If a package was added, create a log for accounting purposes
        if (!empty($validated['vip_package'])) {
            $package = $this->vipPackages[$validated['vip_package']];
            CustomerLog::create([
                'user_id' => Auth::id(),
                'customer_id' => $customer->id,
                'payment_method' => 'VIP Top-Up',
                'payment_amount' => $package['price'],
                'status' => 'completed',
                'is_vip_top_up' => true,
                'completed_at' => now()
            ]);
        }

        // THIS IS THE FIX: The notification is now outside the conditional block
        // and will be sent for every new customer.
        Notification::route('telegram', env('TELEGRAM_CHAT_ID'))->notify(new NewCustomerCreated($customer));

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
            'user_id' => Auth::id(),
            'customer_id' => $customer->id,
            'payment_method' => 'VIP Top-Up',
            'payment_amount' => $package['price'],
            'status' => 'completed',
            'is_vip_top_up' => true,
            'completed_at' => now()
        ]);

        Notification::route('telegram', env('TELEGRAM_CHAT_ID'))->notify(new VipBalanceTopUp($customer, $package['name']));

        return redirect()->route('customers.show', $customer)->with('success', 'Balance updated successfully!');
    }

    //export
    public function export(Request $request)
    {
        $request->validate(['month' => 'required', 'year' => 'required']);
        $fileName = 'New-Customers-' . $request->year . '-' . $request->month . '.xlsx';
        return Excel::download(new NewCustomersExport($request->year, $request->month), $fileName);
    }

    /**
     * Search customers for AJAX requests (used in add new log dropdown)
     */
    public function searchCustomers(Request $request)
    {
        $query = $request->get('query', '');
        
        $customers = Customer::where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('customer_gid', 'LIKE', "%{$query}%")
                  ->orWhere('phone', 'LIKE', "%{$query}%");
            })
            ->orderBy('name')
            ->limit(50)
            ->get(['id', 'name', 'customer_gid', 'phone', 'created_at']);
        
        return response()->json($customers);
    }
}
