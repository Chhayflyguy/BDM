<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\CustomerLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exports\PayrollExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PayrollController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display payroll overview
     */
    public function index(Request $request)
    {
        $currentMonth = $request->input('month', now()->month);
        $currentYear = $request->input('year', now()->year);

        $employees = Employee::where('user_id', Auth::id())
            ->with(['completedLogs' => function ($query) use ($currentYear, $currentMonth) {
                $query->whereYear('completed_at', $currentYear)
                      ->whereMonth('completed_at', $currentMonth)
                      ->where('status', 'completed');
            }])
            ->get();

        $payrolls = $employees->map(function ($employee) {
            $logs = $employee->completedLogs;
            $zeroCommissionCount = $logs->where('employee_commission', 0)->count();
            
            return [
                'employee_id' => $employee->id,
                'name' => $employee->name,
                'employee_gid' => $employee->employee_gid,
                'services_count' => $logs->count(),
                'total_commission' => $logs->sum('employee_commission'),
                'zero_commission_count' => $zeroCommissionCount,
                'has_zero_commissions' => $zeroCommissionCount > 0,
            ];
        });

        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = date('F', mktime(0, 0, 0, $i, 10));
        }
        $years = range(date('Y'), date('Y') - 5);
        
        return view('payroll.index', compact(
            'payrolls', 'months', 'years', 'currentMonth', 'currentYear'
        ));
    }

    /**
     * Show individual employee's transaction history (customer logs)
     */
    public function show(Employee $employee, Request $request)
    {
        $this->authorize('view', $employee);

        $currentMonth = $request->input('month', now()->month);
        $currentYear = $request->input('year', now()->year);

        // Get all completed logs for the period
        $customerLogs = $employee->completedLogs()
            ->whereYear('completed_at', $currentYear)
            ->whereMonth('completed_at', $currentMonth)
            ->where('status', 'completed')
            ->with('customer')
            ->orderBy('completed_at', 'desc')
            ->get();

        $totalCommission = $customerLogs->sum('employee_commission');
        $zeroCommissionCount = $customerLogs->where('employee_commission', 0)->count();

        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = date('F', mktime(0, 0, 0, $i, 10));
        }
        $years = range(date('Y'), date('Y') - 5);

        return view('payroll.show', compact(
            'employee', 'customerLogs', 'totalCommission', 'zeroCommissionCount',
            'months', 'years', 'currentMonth', 'currentYear'
        ));
    }

    /**
     * Update commission for a specific customer log
     */
    public function updateCommission(Request $request, CustomerLog $customerLog)
    {
        // Authorize - user must own this customer log
        if ($customerLog->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'employee_commission' => 'required|numeric|min:0|max:9999.99',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Update commission
        $customerLog->employee_commission = $validated['employee_commission'];
        
        // Update notes if provided
        if ($request->filled('notes')) {
            // Append to existing notes or create new
            $existingNotes = $customerLog->notes ? $customerLog->notes . "\n" : '';
            $customerLog->notes = $existingNotes . '[Commission Updated] ' . $validated['notes'];
        }
        
        $customerLog->save();

        return back()->with('success', 'Commission updated successfully!');
    }

    /**
     * Export payroll data to Excel
     */
    public function export(Request $request)
    {
        $request->validate(['month' => 'required', 'year' => 'required']);
        $fileName = 'Payroll-' . $request->year . '-' . $request->month . '.xlsx';
        return Excel::download(new PayrollExport($request->year, $request->month), $fileName);
    }
}