<?php

namespace App\Http\Controllers;

use App\Models\CustomerLog;
use App\Models\DailyExpense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class AccountantController extends Controller
{
    public function index(Request $request)
    {
        $currentMonth = $request->input('month', now()->month);
        $currentYear = $request->input('year', now()->year);

        
        $totalIncome = CustomerLog::where('user_id', Auth::id())
            ->where('status', 'completed')
            ->whereYear('completed_at', $currentYear)
            ->whereMonth('completed_at', $currentMonth)
            ->where(function ($query) {
                $query->where('is_vip_top_up', true) // Include all top-ups
                      ->orWhere('payment_method', '!=', 'VIP Card'); // Include payments not made by VIP Card
            })
            ->sum('payment_amount');

        $incomeByPaymentMethod = CustomerLog::where('user_id', Auth::id())
            ->where('status', 'completed')
            ->whereYear('completed_at', $currentYear)
            ->whereMonth('completed_at', $currentMonth)
            ->where('payment_method', '!=', 'VIP Card') // Exclude VIP Card payments
            ->groupBy('payment_method')
            ->select('payment_method', DB::raw('SUM(payment_amount) as total'))
            ->pluck('total', 'payment_method');

        $totalExpenses = DailyExpense::where('user_id', Auth::id())
            ->whereYear('expense_date', $currentYear)
            ->whereMonth('expense_date', $currentMonth)
            ->sum('amount');

        $totalSalaries = CustomerLog::where('user_id', Auth::id())
            ->where('status', 'completed')
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
}
