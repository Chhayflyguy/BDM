<?php

namespace App\Exports;

use App\Models\DailyExpense;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class DailyExpensesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $year;
    protected $month;

    public function __construct(int $year, int $month)
    {
        $this->year = $year;
        $this->month = $month;
    }

    public function collection()
    {
        return DailyExpense::where('user_id', Auth::user()->id)
            ->whereYear('expense_date', $this->year)
            ->whereMonth('expense_date', $this->month)
            ->get();
    }

    public function headings(): array
    {
        return ['Date', 'Item/Service', 'Purpose', 'Amount'];
    }

    public function map($expense): array
    {
        return [
            $expense->expense_date->format('Y-m-d'),
            $expense->item_name,
            $expense->purpose,
            $expense->amount,
        ];
    }
}