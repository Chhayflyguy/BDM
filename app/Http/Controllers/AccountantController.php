<?php

namespace App\Http\Controllers;

use App\Models\CustomerLog;
use App\Models\DailyExpense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Exports\AccountantReportExport;
use Maatwebsite\Excel\Facades\Excel;

class AccountantController extends Controller
{
    public function index(Request $request)
    {
        $currentMonth = $request->input('month', now()->month);
        $currentYear = $request->input('year', now()->year);

        
        $totalIncome = CustomerLog::where('status', 'completed')
            ->whereYear('completed_at', $currentYear)
            ->whereMonth('completed_at', $currentMonth)
            ->where(function ($query) {
                $query->where('is_vip_top_up', true) // Include all top-ups
                      ->orWhere('payment_method', '!=', 'VIP Card'); // Include payments not made by VIP Card
            })
            ->sum('payment_amount');

        $incomeByPaymentMethod = CustomerLog::where('status', 'completed')
            ->whereYear('completed_at', $currentYear)
            ->whereMonth('completed_at', $currentMonth)
            ->where('payment_method', '!=', 'VIP Card') // Exclude VIP Card payments
            ->groupBy('payment_method')
            ->select('payment_method', DB::raw('SUM(payment_amount) as total'))
            ->pluck('total', 'payment_method');

        $totalExpenses = DailyExpense::whereYear('expense_date', $currentYear)
            ->whereMonth('expense_date', $currentMonth)
            ->sum('amount');

        $totalSalaries = CustomerLog::where('status', 'completed')
            ->whereYear('completed_at', $currentYear)
            ->whereMonth('completed_at', $currentMonth)
            ->sum('employee_commission');

        $netProfit = $totalIncome - $totalExpenses - $totalSalaries;

        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = date('F', mktime(0, 0, 0, $i, 10));
        }
        $years = range(date('Y'), date('Y') - 5);

        return view('accountant.index', compact(
            'totalIncome', 'incomeByPaymentMethod', 'totalExpenses', 'totalSalaries', 'netProfit', 
            'months', 'years', 'currentMonth', 'currentYear'
        ));
    }

    //export
    public function export(Request $request)
    {
        $request->validate(['month' => 'required', 'year' => 'required']);
        
        // We reuse the logic from the index method to get the data
        $data = $this->getAccountantData($request->month, $request->year);
        
        $reportData = [
            ['Gross Income', $data['totalIncome']],
            ['Total Daily Expenses', $data['totalExpenses']],
            ['Total Employee Payroll', $data['totalSalaries']],
            ['Net Profit', $data['netProfit']],
        ];

        $fileName = 'Accountant-Report-' . $request->year . '-' . $request->month . '.xlsx';
        return Excel::download(new AccountantReportExport($reportData), $fileName);
    }
    private function getAccountantData($month, $year)
    {
        $data = [];
        $data['totalIncome'] = CustomerLog::where('status', 'completed')->whereYear('completed_at', $year)->whereMonth('completed_at', $month)->where(function ($q) { $q->where('is_vip_top_up', true)->orWhere('payment_method', '!=', 'VIP Card'); })->sum('payment_amount');
        $data['totalExpenses'] = DailyExpense::whereYear('expense_date', $year)->whereMonth('expense_date', $month)->sum('amount');
        $data['totalSalaries'] = CustomerLog::where('status', 'completed')->whereYear('completed_at', $year)->whereMonth('completed_at', $month)->sum('employee_commission');
        $data['netProfit'] = $data['totalIncome'] - $data['totalExpenses'] - $data['totalSalaries'];
        return $data;
    }
}
