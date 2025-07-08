<?php

namespace App\Http\Controllers;

use App\Models\DailyExpense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Exports\DailyExpensesExport;
use Maatwebsite\Excel\Facades\Excel;

class DailyExpenseController extends Controller
{
    public function index(Request $request)
    {
        $currentMonth = $request->input('month', now()->month);
        $currentYear = $request->input('year', now()->year);

        $expensesQuery = DailyExpense::where('user_id', Auth::id())
            ->whereYear('expense_date', $currentYear)
            ->whereMonth('expense_date', $currentMonth)
            ->latest('expense_date');

        $groupedExpenses = $expensesQuery->get()->groupBy(function ($expense) {
            return $expense->expense_date->format('Y-m-d');
        });

        $monthlyTotal = $expensesQuery->sum('amount');
            
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = date('F', mktime(0, 0, 0, $i, 10));
        }
        $years = range(date('Y'), date('Y') - 5);
            
        return view('daily_expenses.index', compact(
            'groupedExpenses', 'monthlyTotal', 
            'months', 'years', 'currentMonth', 'currentYear'
        ));
    }

    public function create()
    {
        return view('daily_expenses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'purpose' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
        ]);

        $validated['user_id'] = Auth::id();

        DailyExpense::create($validated);

        return redirect()->route('daily_expenses.index')->with('success', 'Expense recorded successfully!');
    }

    //export
    public function export(Request $request)
    {
        $request->validate(['month' => 'required', 'year' => 'required']);
        $fileName = 'Expenses-' . $request->year . '-' . $request->month . '.xlsx';
        return Excel::download(new DailyExpensesExport($request->year, $request->month), $fileName);
    }
}