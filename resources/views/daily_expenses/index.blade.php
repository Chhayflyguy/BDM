<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">{{ __('messages.daily_expenses') }}</h2>
                <p class="text-sm text-gray-600 mt-1">{{ __('Track and manage your daily business expenses') }}</p>
            </div>
            <a href="{{ route('daily_expenses.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-lg hover:shadow-xl transition ease-in-out duration-150">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('messages.add_new_expense') }}
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Filter Card -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-6 border border-gray-100">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                <form action="{{ route('daily_expenses.index') }}" method="GET" class="flex flex-wrap items-end gap-4 flex-1">
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        {{ __('messages.filter') }}
                    </x-primary-button>
                </form>
                <form action="{{ route('daily_expenses.export') }}" method="POST">
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

        <!-- Monthly Total Card -->
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-6 mb-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-red-100 uppercase tracking-wide mb-1">{{ __('messages.total_expenses_for') }}</p>
                    <p class="text-2xl font-semibold mb-1">{{ date('F', mktime(0,0,0,$currentMonth, 1)) }} {{ $currentYear }}</p>
                    <p class="text-4xl font-bold">${{ number_format($monthlyTotal, 2) }}</p>
                </div>
                <div class="p-4 bg-white/20 rounded-lg">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-400 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <!-- Expenses by Date -->
        @forelse ($groupedExpenses as $date => $expenses)
            <div class="mb-6">
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                    <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">
                                <svg class="w-5 h-5 inline mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ \Carbon\Carbon::parse($date)->format('F j, Y') }}
                            </h3>
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                                {{ $expenses->count() }} {{ __('items') }}
                            </span>
                        </div>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @foreach ($expenses as $expense)
                            <div class="px-6 py-4 hover:bg-gray-50 transition duration-150">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center">
                                            <div class="p-2 bg-orange-100 rounded-lg mr-4">
                                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $expense->item_name }}</p>
                                                @if($expense->purpose)
                                                    <p class="text-xs text-gray-500 mt-0.5">{{ $expense->purpose }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-lg font-bold text-red-600">${{ number_format($expense->amount, 2) }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <div class="px-6 py-3 bg-gray-50">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-700">{{ __('Daily Total') }}:</span>
                                <span class="text-lg font-bold text-red-600">${{ number_format($expenses->sum('amount'), 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                <div class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('messages.no_expenses_recorded_for_this_month') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Get started by adding your first expense') }}</p>
                    <div class="mt-6">
                        <a href="{{ route('daily_expenses.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            {{ __('messages.add_new_expense') }}
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</x-app-layout>