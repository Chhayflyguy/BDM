<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class PayrollExport implements FromCollection, WithHeadings, WithMapping
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
        return Employee::where('user_id', Auth::user()->id)
            ->with(['completedLogs' => function ($query) {
                $query->whereYear('completed_at', $this->year)
                      ->whereMonth('completed_at', $this->month);
            }])
            ->get();
    }

    public function headings(): array
    {
        return ['Employee ID', 'Name', 'Services Rendered', 'Total Salary'];
    }

    public function map($employee): array
    {
        return [
            $employee->employee_gid,
            $employee->name,
            $employee->completedLogs->count(),
            $employee->completedLogs->sum('employee_commission'),
        ];
    }
}