<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exports\PayrollExport;
use Maatwebsite\Excel\Facades\Excel;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $currentMonth = $request->input('month', now()->month);
        $currentYear = $request->input('year', now()->year);

        $employees = Employee::where('user_id', Auth::id())
            ->with(['completedLogs' => function ($query) use ($currentYear, $currentMonth) {
                $query->whereYear('completed_at', $currentYear)
                      ->whereMonth('completed_at', $currentMonth);
            }])
            ->get();

        $payrolls = $employees->map(function ($employee) {
            return [
                'name' => $employee->name,
                'employee_gid' => $employee->employee_gid,
                'services_count' => $employee->completedLogs->count(),
                'total_salary' => $employee->completedLogs->sum('employee_commission'),
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
    //export
    public function export(Request $request)
    {
        $request->validate(['month' => 'required', 'year' => 'required']);
        $fileName = 'Payroll-' . $request->year . '-' . $request->month . '.xlsx';
        return Excel::download(new PayrollExport($request->year, $request->month), $fileName);
    }
}