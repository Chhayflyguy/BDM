<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Accountant Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filter Section -->
            <div class="bg-white p-6 rounded-lg shadow-sm mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Select Reporting Period</h3>
                <div class="flex justify-between items-end">
                    <!-- Filter Form -->
                    <form action="{{ route('accountant.index') }}" method="GET" class="flex items-end space-x-4">
                        <div>
                            <label for="month" class="block text-sm font-medium text-gray-700">Month</label>
                            <select id="month" name="month" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 rounded-md">
                                @foreach($months as $num => $name)
                                <option value="{{ $num }}" @selected($num==$currentMonth)>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="year" class="block text-sm font-medium text-gray-700">Year</label>
                            <select id="year" name="year" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 rounded-md">
                                @foreach($years as $year)
                                <option value="{{ $year }}" @selected($year==$currentYear)>{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                        <x-primary-button>Generate Report</x-primary-button>
                    </form>
                    <!-- Export Form -->
                    <form action="{{ route('accountant.export') }}" method="POST">
                        @csrf
                        <input type="hidden" name="month" value="{{ $currentMonth }}">
                        <input type="hidden" name="year" value="{{ $currentYear }}">
                        <x-secondary-button type="submit">Export to Excel</x-secondary-button>
                    </form>
                </div>
            </div>
            <!-- Report Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Income Breakdown -->
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Income Summary</h3>
                    <div class="space-y-3">
                        @php
                        $paymentTypes = ['VIP Top-Up', 'Cash', 'ABA', 'AC', 'Other Bank']; // Added VIP Top-Up
                        @endphp
                        @foreach($paymentTypes as $type)
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500">{{ $type }}:</span>
                            <span class="font-semibold text-gray-700">${{ number_format($incomeByPaymentMethod[$type] ?? 0, 2) }}</span>
                        </div>
                        @endforeach
                        <hr>
                        <div class="flex justify-between items-center text-md">
                            <span class="font-bold text-gray-600">Total Gross Income:</span>
                            <span class="font-bold text-green-600">${{ number_format($totalIncome, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Expenses & Profit -->
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Profit & Loss</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500">Gross Income:</span>
                            <span class="font-semibold text-green-600">${{ number_format($totalIncome, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500">Less Daily Expenses:</span>
                            <span class="font-semibold text-red-600">-${{ number_format($totalExpenses, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500">Less Employee Payroll:</span>
                            <span class="font-semibold text-red-600">-${{ number_format($totalSalaries, 2) }}</span>
                        </div>
                        <hr>
                        <div class="flex justify-between items-center text-xl">
                            <span class="font-bold text-gray-800">Net Profit:</span>
                            <span class="font-bold {{ $netProfit >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                                ${{ number_format($netProfit, 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>