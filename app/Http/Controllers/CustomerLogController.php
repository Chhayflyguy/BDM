<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerLog;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Exports\CustomerLogsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
class CustomerLogController extends Controller
{
    use AuthorizesRequests;
    public function index(): View
    {
        $groupedLogs = CustomerLog::with('customer') // Eager load customer relationship
            ->where('user_id', Auth::id())
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

        return view('customer_logs.index', compact('groupedLogs', 'dailyTotals', 'months', 'years'));
    }

    public function create(): View
    {
        $customers = Customer::where('user_id', Auth::id())->orderBy('name')->get();
        return view('customer_logs.create', compact('customers'));
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
    
    public function destroy(CustomerLog $customerLog): RedirectResponse
    {
        $this->authorize('delete', $customerLog);
        $customerLog->delete();
        return redirect()->route('dashboard')->with('success', 'Customer log deleted successfully!');
    }
    
    public function showCompletionForm(CustomerLog $customerLog): View
    {
        $this->authorize('update', $customerLog);
        $employees = Employee::where('user_id', Auth::id())->orderBy('name')->get(); // NEW
        return view('customer_logs.complete', compact('customerLog', 'employees')); // MODIFIED
    }
    
    public function markAsComplete(Request $request, CustomerLog $customerLog): RedirectResponse
    {
        $this->authorize('update', $customerLog);

        $validated = $request->validate([
            'product_purchased' => 'nullable|string|max:255',
            'product_price' => 'nullable|numeric|min:0',
            'employee_id' => 'nullable|exists:employees,id', 
            'massage_price' => 'nullable|numeric|min:0',
            'payment_method' => 'required|string|max:50', 
            'next_booking_date' => 'nullable|date|after:today', // NEW
        ]);

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
            // Deduct from balance
            $customer->vip_card_balance -= $totalCost;
            $customer->save();
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