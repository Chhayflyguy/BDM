<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">{{ __('messages.accountant_report') }}</h2>
                <p class="text-sm text-gray-600 mt-1">{{ __('Comprehensive financial overview and analysis') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Filter Card -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('messages.select_reporting_period') }}</h3>
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                <form action="{{ route('accountant.index') }}" method="GET" class="flex flex-wrap items-end gap-4 flex-1">
                    <div class="w-full md:w-auto min-w-[150px]">
                        <label for="month" class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.month') }}</label>
                        <select id="month" name="month" class="block w-full pl-3 pr-10 py-2.5 text-base border-gray-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach($months as $num => $name)
                            <option value="{{ $num }}" @selected($num==$currentMonth)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-full md:w-auto min-w-[120px]">
                        <label for="year" class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.year') }}</label>
                        <select id="year" name="year" class="block w-full pl-3 pr-10 py-2.5 text-base border-gray-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach($years as $year)
                            <option value="{{ $year }}" @selected($year==$currentYear)>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <x-primary-button type="submit">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        {{ __('messages.generate_report') }}
                    </x-primary-button>
                </form>
                <form action="{{ route('accountant.export') }}" method="POST">
                    @csrf
                    <input type="hidden" name="month" value="{{ $currentMonth }}">
                    <input type="hidden" name="year" value="{{ $currentYear }}">
                    <x-secondary-button type="submit" class="w-full md:w-auto">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        {{ __('messages.export_to_excel') }}
                    </x-secondary-button>
                </form>
            </div>
        </div>

        <!-- Income Breakdown Card -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-6 border border-gray-100">
            <div class="flex items-center mb-4">
                <div class="p-3 bg-green-100 rounded-lg mr-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">{{ __('messages.income_summary') }}</h3>
            </div>
            <div class="space-y-3">
                @php
                $paymentTypes = ['VIP Top-Up', 'Cash', 'ABA', 'AC', 'Other Bank'];
                @endphp
                @foreach($paymentTypes as $type)
                <div class="flex justify-between items-center p-3 rounded-lg hover:bg-gray-50 transition duration-150">
                    <div class="flex items-center">
                        <div class="w-2 h-2 rounded-full bg-indigo-500 mr-3"></div>
                        <span class="text-sm font-medium text-gray-700">{{ $type }}:</span>
                    </div>
                    <span class="text-sm font-bold text-gray-900">${{ number_format($incomeByPaymentMethod[$type] ?? 0, 2) }}</span>
                </div>
                @endforeach
                <div class="pt-3 mt-3 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <span class="text-base font-bold text-gray-900">{{ __('messages.total_gross_income') }}:</span>
                        <span class="text-xl font-bold text-green-600">${{ number_format($totalIncome, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profit & Loss Card -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-6 border border-gray-100">
            <div class="flex items-center mb-4">
                <div class="p-3 bg-blue-100 rounded-lg mr-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">{{ __('messages.profit_loss') }}</h3>
            </div>
            <div class="space-y-4">
                <!-- Gross Income -->
                <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                            <span class="text-sm font-medium text-gray-700">{{ __('messages.gross_income') }}:</span>
                        </div>
                        <span class="text-lg font-bold text-green-600">+${{ number_format($totalIncome, 2) }}</span>
                    </div>
                </div>

                <!-- Daily Expenses -->
                <div class="p-4 bg-red-50 rounded-lg border border-red-200">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                            </svg>
                            <span class="text-sm font-medium text-gray-700">{{ __('messages.less_daily_expenses') }}:</span>
                        </div>
                        <span class="text-lg font-bold text-red-600">-${{ number_format($totalExpenses, 2) }}</span>
                    </div>
                </div>

                <!-- Employee Payroll -->
                <div class="p-4 bg-red-50 rounded-lg border border-red-200">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                            </svg>
                            <span class="text-sm font-medium text-gray-700">{{ __('messages.less_employee_payroll') }}:</span>
                        </div>
                        <span class="text-lg font-bold text-red-600">-${{ number_format($totalSalaries, 2) }}</span>
                    </div>
                </div>

                <!-- Net Profit -->
                <div class="pt-4 mt-4 border-t-2 border-gray-300">
                    <div class="flex justify-between items-center p-4 rounded-lg {{ $netProfit >= 0 ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }}">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 {{ $netProfit >= 0 ? 'text-green-600' : 'text-red-600' }} mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <span class="text-base font-bold text-gray-900">{{ __('messages.net_profit') }}:</span>
                        </div>
                        <span class="text-2xl font-bold {{ $netProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            ${{ number_format($netProfit, 2) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-100 uppercase tracking-wide mb-1">{{ __('Gross Income') }}</p>
                        <p class="text-3xl font-bold">${{ number_format($totalIncome, 2) }}</p>
                    </div>
                    <div class="p-3 bg-white/20 rounded-lg">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-red-100 uppercase tracking-wide mb-1">{{ __('Total Expenses') }}</p>
                        <p class="text-3xl font-bold">${{ number_format($totalExpenses + $totalSalaries, 2) }}</p>
                    </div>
                    <div class="p-3 bg-white/20 rounded-lg">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br {{ $netProfit >= 0 ? 'from-blue-500 to-blue-600' : 'from-orange-500 to-orange-600' }} rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium {{ $netProfit >= 0 ? 'text-blue-100' : 'text-orange-100' }} uppercase tracking-wide mb-1">{{ __('Net Profit') }}</p>
                        <p class="text-3xl font-bold">${{ number_format($netProfit, 2) }}</p>
                    </div>
                    <div class="p-3 bg-white/20 rounded-lg">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>