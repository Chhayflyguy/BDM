<?php

namespace App\Exports;

use App\Models\CustomerLog;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomerLogsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $year;
    protected $month;

    public function __construct(int $year, int $month)
    {
        $this->year = $year;
        $this->month = $month;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return CustomerLog::whereYear('created_at', $this->year)
            ->whereMonth('created_at', $this->month)
            ->where('user_id', Auth::id()) // <-- CHANGE THIS LINE
            ->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Date Created',
            'Customer ID',
            'Customer Name',
            'Customer Phone',
            'Consultant',
            'Product Purchased',
            'Product Price',
            'Masseuse Name',
            'Massage Price',
            'Total Payment',
            'Status',
            'Date Completed',
            'Notes',
        ];
    }

    /**
     * @var CustomerLog $log
     * @return array
     */
    public function map($log): array
    {
        return [
            $log->created_at->format('Y-m-d'),
            $log->customer_gid,
            $log->customer_name,
            $log->customer_phone,
            $log->user->name, // Consultant Name
            $log->product_purchased,
            $log->product_price,
            $log->masseuse_name,
            $log->massage_price,
            $log->payment_amount,
            $log->status,
            $log->completed_at ? $log->completed_at->format('Y-m-d H:i') : 'N/A',
            $log->notes,
        ];
    }
}