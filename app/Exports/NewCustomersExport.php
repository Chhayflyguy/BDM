<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class NewCustomersExport implements FromCollection, WithHeadings, WithMapping
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
        return Customer::where('user_id', Auth::user()->id)
            ->whereYear('created_at', $this->year)
            ->whereMonth('created_at', $this->month)
            ->get();
    }

    public function headings(): array
    {
        return ['Date Joined', 'Customer ID', 'Balance Card ID', 'Name', 'Phone', 'Gender', 'Age', 'Height'];
    }

    public function map($customer): array
    {
        return [
            $customer->created_at->format('Y-m-d'),
            $customer->customer_gid,
            $customer->vip_card_id,
            $customer->name,
            $customer->phone,
            $customer->gender,
            $customer->age,
            $customer->height,
        ];
    }
}