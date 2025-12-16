<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerLog;
use App\Models\Employee;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Exports\CustomerLogsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Notifications\VipPaymentMade;         // NEW: Import the notification class
use Illuminate\Support\Facades\Notification;
class CustomerLogController extends Controller
{
    use AuthorizesRequests;
    public function index(): View
    {
        // Get statistics for dashboard (shared across all users)
        $stats = [
            'total_customers' => Customer::count(),
            'total_employees' => Employee::count(),
            'active_logs' => CustomerLog::where('status', 'active')->count(),
            'completed_logs' => CustomerLog::where('status', 'completed')->count(),
            'today_income' => CustomerLog::where('status', 'completed')
                ->whereDate('completed_at', today())
                ->where(function ($query) {
                    $query->where('is_vip_top_up', true)
                          ->orWhere('payment_method', '!=', 'VIP Card');
                })
                ->sum('payment_amount'),
            'this_month_income' => CustomerLog::where('status', 'completed')
                ->whereYear('completed_at', now()->year)
                ->whereMonth('completed_at', now()->month)
                ->where(function ($query) {
                    $query->where('is_vip_top_up', true)
                          ->orWhere('payment_method', '!=', 'VIP Card');
                })
                ->sum('payment_amount'),
            'pending_bookings' => Customer::whereNotNull('next_booking_date')
                ->whereDate('next_booking_date', '>=', today())
                ->count(),
        ];
        
        $groupedLogs = CustomerLog::with(['customer', 'user']) // Eager load customer and user relationships
            ->latest()
            ->get()
            ->groupBy(function ($log) {
                return $log->created_at->timezone(config('app.timezone'))->format('Y-m-d');
            });
        
        $dailyTotals = [];
        foreach ($groupedLogs as $date => $logs) {
            $dailyTotals[$date] = [
                'total_payment' => $logs->where('payment_method', '!=', 'VIP Card')->sum('payment_amount'),
                'products_count' => $logs->whereNotNull('product_purchased')->count(),
            ];
        }
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = date('F', mktime(0, 0, 0, $i, 10));
        }
        $years = range(date('Y'), date('Y') - 5);

        return view('customer_logs.index', compact('groupedLogs', 'dailyTotals', 'months', 'years', 'stats'));
    }

    public function create(): View
    {
        // Get all customers created today
        $todayCustomers = Customer::whereDate('created_at', today())
            ->orderBy('name')
            ->get();
        return view('customer_logs.create', compact('todayCustomers'));
    }

    /**
     * Search customers for AJAX requests
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

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'next_meeting' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $customer = Customer::find($validated['customer_id']);
        if ($customer->next_booking_date && now()->isSameDay($customer->next_booking_date)) {
            $customer->booking_completed_at = now();
            $customer->save();
        }

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'active';

        CustomerLog::create($validated);

         return redirect()->route('dashboard')->with('success', 'New customer log created successfully!');
    }

    /**
     * FIX: Re-add edit method for logs.
     */
    public function edit(CustomerLog $customerLog)
    {
        $this->authorize('update', $customerLog);
        
        // If the log is completed, show the completed edit view with all fields
        if ($customerLog->status === 'completed') {
            // Only show active employees
            $employees = Employee::where('working_status', 'Active')
                ->orderBy('name')
                ->get();
            $products = Product::orderBy('name')->get(); // Show all products for editing
            return view('customer_logs.edit_completed', compact('customerLog', 'employees', 'products'));
        }
        
        // For active logs, show the simple edit view
        return view('customer_logs.edit', compact('customerLog'));
    }

    /**
     * FIX: Re-add update method for logs.
     */
    public function update(Request $request, CustomerLog $customerLog)
    {
        $this->authorize('update', $customerLog);

        $validated = $request->validate([
            'next_meeting' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $customerLog->update($validated);

        return redirect()->route('dashboard')->with('success', 'Log updated successfully!');
    }
    
    /**
     * Update a completed log with all fields
     */
    public function updateCompleted(Request $request, CustomerLog $customerLog)
    {
        $this->authorize('update', $customerLog);
        
        // Ensure we're only updating completed logs
        if ($customerLog->status !== 'completed') {
            return redirect()->route('dashboard')->withErrors(['error' => 'This method can only update completed logs.']);
        }

        $validated = $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'product_quantity' => 'nullable|integer|min:1',
            'product_purchased' => 'nullable|string|max:255',
            'product_price' => 'nullable|numeric|min:0',
            'employee_id' => 'nullable|exists:employees,id', 
            'massage_price' => 'nullable|numeric|min:0',
            'payment_method' => 'required|string|max:50', 
            'next_booking_date' => 'nullable|date|after:today',
        ]);

        // Handle product changes and stock adjustment
        $oldProductId = $customerLog->product_id;
        $oldProductQuantity = $customerLog->product_quantity ?? 1;
        
        if (!empty($validated['product_id'])) {
            $product = Product::find($validated['product_id']);
            $newQuantity = $validated['product_quantity'] ?? 1;
            
            // If product changed or quantity changed, adjust stock
            if ($oldProductId != $validated['product_id']) {
                // Different product selected
                // Restore stock of old product if there was one
                if ($oldProductId) {
                    $oldProduct = Product::find($oldProductId);
                    if ($oldProduct) {
                        $oldProduct->increment('quantity', $oldProductQuantity);
                    }
                }
                
                // Deduct stock of new product
                if ($product->quantity < $newQuantity) {
                    return back()->withErrors([
                        'product_quantity' => "Insufficient stock. Only {$product->quantity} units available."
                    ])->withInput();
                }
                $product->decrement('quantity', $newQuantity);
            } else if ($oldProductQuantity != $newQuantity) {
                // Same product but different quantity
                $quantityDiff = $newQuantity - $oldProductQuantity;
                if ($quantityDiff > 0) {
                    // Need more stock
                    if ($product->quantity < $quantityDiff) {
                        return back()->withErrors([
                            'product_quantity' => "Insufficient stock. Only {$product->quantity} additional units available."
                        ])->withInput();
                    }
                    $product->decrement('quantity', $quantityDiff);
                } else {
                    // Return stock
                    $product->increment('quantity', abs($quantityDiff));
                }
            }
            
            // Auto-populate product info from database
            $validated['product_purchased'] = $product->name;
            $validated['product_price'] = $product->price * $newQuantity;
            $validated['product_quantity'] = $newQuantity;
        } else {
            // No product selected, restore old product stock if there was one
            if ($oldProductId) {
                $oldProduct = Product::find($oldProductId);
                if ($oldProduct) {
                    $oldProduct->increment('quantity', $oldProductQuantity);
                }
            }
            // Clear product fields
            $validated['product_id'] = null;
            $validated['product_quantity'] = null;
        }

        $totalCost = ($validated['product_price'] ?? 0) + ($validated['massage_price'] ?? 0);

        if ($totalCost <= 0) {
            return back()->withErrors(['amount' => 'The total cost must be greater than zero.'])->withInput();
        }

        // Handle payment method changes for VIP Card
        $oldPaymentMethod = $customerLog->payment_method;
        $oldPaymentAmount = $customerLog->payment_amount;
        $customer = $customerLog->customer;
        
        // If changing FROM VIP Card to something else, refund the VIP card
        if ($oldPaymentMethod === 'VIP Card' && $validated['payment_method'] !== 'VIP Card') {
            $customer->vip_card_balance += $oldPaymentAmount;
        }
        
        // If changing TO VIP Card, deduct from balance
        if ($validated['payment_method'] === 'VIP Card') {
            if ($oldPaymentMethod === 'VIP Card') {
                // Was already VIP Card, adjust for the difference
                $difference = $totalCost - $oldPaymentAmount;
                if ($customer->vip_card_balance < $difference) {
                    return back()->withErrors(['balance' => 'Insufficient VIP card balance for this change.'])->withInput();
                }
                $customer->vip_card_balance -= $difference;
            } else {
                // Switching to VIP Card
                if ($customer->vip_card_balance < $totalCost) {
                    return back()->withErrors(['balance' => 'Insufficient VIP card balance.'])->withInput();
                }
                $customer->vip_card_balance -= $totalCost;
            }
        }
        $customer->save();
        
        // Handle next booking date
        if (!empty($validated['next_booking_date'])) {
            $customer->next_booking_date = $validated['next_booking_date'];
            $customer->booking_completed_at = null;
            $customer->save();
        }

        // Handle employee commission
        $validated['employee_commission'] = 0;
        if (!empty($validated['employee_id']) && !empty($validated['massage_price'])) {
            $employee = Employee::find($validated['employee_id']);
            $validated['masseuse_name'] = $employee->name;
            $price = $validated['massage_price'];
            if ($price >= 8 && $price <= 10) $validated['employee_commission'] = 3;
            elseif ($price >= 15 && $price <= 18) $validated['employee_commission'] = 6;
            elseif ($price >= 25 && $price <= 30) $validated['employee_commission'] = 8;
        }

        $validated['payment_amount'] = $totalCost;

        $customerLog->update($validated);
        
        return redirect()->route('dashboard')->with('success', 'Completed log updated successfully!');
    }
    
    public function destroy(CustomerLog $customerLog): RedirectResponse
    {
        $this->authorize('delete', $customerLog);
        $customerLog->delete();
        return redirect()->route('dashboard')->with('success', 'Customer log deleted successfully!');
    }
    
    public function showCompletionForm(CustomerLog $customerLog): View
    {
        $this->authorize('update', $customerLog);
        // Only show active employees
        $employees = Employee::where('working_status', 'Active')
            ->orderBy('name')
            ->get();
        $products = Product::where('quantity', '>', 0)->orderBy('name')->get(); // NEW: Load available products
        return view('customer_logs.complete', compact('customerLog', 'employees', 'products')); // MODIFIED
    }
    
    public function markAsComplete(Request $request, CustomerLog $customerLog): RedirectResponse
    {
        $this->authorize('update', $customerLog);

        $validated = $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'product_quantity' => 'nullable|integer|min:1', // NEW: Accept quantity
            'product_purchased' => 'nullable|string|max:255',
            'product_price' => 'nullable|numeric|min:0',
            'employee_id' => 'nullable|exists:employees,id', 
            'massage_price' => 'nullable|numeric|min:0',
            'payment_method' => 'required|string|max:50', 
            'next_booking_date' => 'nullable|date|after:today',
        ]);

        // NEW: Handle product selection and stock deduction
        if (!empty($validated['product_id'])) {
            $product = Product::find($validated['product_id']);
            $quantity = $validated['product_quantity'] ?? 1; // Default to 1 if not specified
            
            // Check stock availability for the requested quantity
            if ($product->quantity < $quantity) {
                return back()->withErrors([
                    'product_quantity' => "Insufficient stock. Only {$product->quantity} units available."
                ])->withInput();
            }
            
            // Auto-populate product info from database
            $validated['product_purchased'] = $product->name;
            $validated['product_price'] = $product->price * $quantity; // Total price for quantity
            $validated['product_quantity'] = $quantity; // Store the quantity
            
            // Decrement stock by quantity
            $product->decrement('quantity', $quantity);
        }

        $totalCost = ($validated['product_price'] ?? 0) + ($validated['massage_price'] ?? 0);

        if ($totalCost <= 0) {
            return back()->withErrors(['amount' => 'The total cost must be greater than zero.'])->withInput();
        }

        // Handle VIP Card Payment
        if ($validated['payment_method'] === 'VIP Card') {
            $customer = $customerLog->customer;
            if ($customer->vip_card_balance < $totalCost) {
                return back()->withErrors(['balance' => 'Insufficient VIP card balance.'])->withInput();
            }
            $customer->vip_card_balance -= $totalCost;
            $customer->save();
            
            // Trigger notification after saving
            Notification::route('telegram', env('TELEGRAM_CHAT_ID'))->notify(new VipPaymentMade($customerLog));
        }
        
        $customer = $customerLog->customer;
        if (!empty($validated['next_booking_date'])) {
            $customer->next_booking_date = $validated['next_booking_date'];
            $customer->booking_completed_at = null; // Reset completion status for the new booking
            $customer->save();
        }

        $validated['employee_commission'] = 0;
        if (!empty($validated['employee_id']) && !empty($validated['massage_price'])) {
            $employee = Employee::find($validated['employee_id']);
            $validated['masseuse_name'] = $employee->name;
            $price = $validated['massage_price'];
            if ($price >= 8 && $price <= 10) $validated['employee_commission'] = 3;
            elseif ($price >= 15 && $price <= 18) $validated['employee_commission'] = 6;
            elseif ($price >= 25 && $price <= 30) $validated['employee_commission'] = 8;
        }

        $validated['status'] = 'completed';
        $validated['completed_at'] = now();
        $validated['payment_amount'] = $totalCost;

        $customerLog->update($validated);
        
        return redirect()->route('dashboard')->with('success', 'Log for customer ' . $customerLog->customer->name . ' has been completed!');
    }

    public function export(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer',
        ]);
        $monthName = date('F', mktime(0, 0, 0, $request->month, 10));
        $fileName = 'CustomerLogs-' . $request->year . '-' . $monthName . '.xlsx';
        return Excel::download(new CustomerLogsExport($request->year, $request->month), $fileName);
    }
}