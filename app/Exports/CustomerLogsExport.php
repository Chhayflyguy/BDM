<?php

namespace App\Exports;

use App\Models\CustomerLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class CustomerLogsExport implements FromCollection, WithHeadings, WithMapping
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
        return CustomerLog::with(['customer', 'employee'])
            ->where('user_id', Auth::user()->id)
            ->whereYear('completed_at', $this->year)
            ->whereMonth('completed_at', $this->month)
            ->where('status', 'completed')
            ->get();
    }

    public function headings(): array
    {
        // A comprehensive list of all possible columns
        return [
            'Date',
            'Log Type',
            'Customer ID',
            'Customer Name',
            'Phone',
            'Masseuse Name',
            'Massage Price',
            'Product Name',
            'Product Price',
            'Payment Method',
            'Total Payment',
            'Card Balance ID',
            'Card Package',
        ];
    }

    public function map($log): array
    {
        // Conditional mapping based on log type
        if ($log->is_vip_top_up) {
            return [
                $log->completed_at->format('Y-m-d'),
                'VIP Top-Up',
                $log->customer->customer_gid ?? 'N/A',
                $log->customer->name ?? 'N/A',
                $log->customer->phone ?? 'N/A',
                null, // Masseuse Name
                null, // Massage Price
                null, // Product Name
                null, // Product Price
                'VIP Top-Up', // Payment Method
                $log->payment_amount, // Total Payment (the top-up amount)
                $log->customer->vip_card_id ?? 'N/A',
                $log->customer->vip_card_type ?? 'N/A',
            ];
        } else {
            return [
                $log->completed_at->format('Y-m-d'),
                'Service/Sale',
                $log->customer->customer_gid ?? 'N/A',
                $log->customer->name ?? 'N/A',
                $log->customer->phone ?? 'N/A',
                $log->masseuse_name,
                $log->massage_price,
                $log->product_purchased,
                $log->product_price,
                $log->payment_method,
                $log->payment_amount,
                null, // Card Balance ID
                null, // Card Package
            ];
        }
    }
}