<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Daily Expenses') }}</h2>
            <a href="{{ route('daily_expenses.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">{{ __('Add New Expense') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded-lg shadow-sm mb-8">
                <div class="flex justify-between items-end">
                    <!-- Filter Form -->
                    <form action="{{ route('daily_expenses.index') }}" method="GET" class="flex items-end space-x-4">
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
                        <x-primary-button>Filter</x-primary-button>
                    </form>
                    <!-- Export Form -->
                    <form action="{{ route('daily_expenses.export') }}" method="POST">
                        @csrf
                        <input type="hidden" name="month" value="{{ $currentMonth }}">
                        <input type="hidden" name="year" value="{{ $currentYear }}">
                        <x-secondary-button type="submit">Export to Excel</x-secondary-button>
                    </form>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-sm mb-8 text-center">
                <h3 class="text-lg font-medium text-gray-500">Total Expenses for {{ date('F', mktime(0,0,0,$currentMonth, 1)) }} {{ $currentYear }}</h3>
                <p class="text-3xl font-bold text-red-600">${{ number_format($monthlyTotal, 2) }}</p>
            </div>

            @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 border rounded-md shadow-sm">{{ session('success') }}</div>
            @endif

            @forelse ($groupedExpenses as $date => $expenses)
            <div class="mt-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4">{{ \Carbon\Carbon::parse($date)->format('F j, Y') }}</h3>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($expenses as $expense)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $expense->item_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $expense->purpose }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-500 font-semibold">${{ number_format($expense->amount, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-8">
                <div class="p-6 text-gray-900 text-center">No expenses recorded for this month.</div>
            </div>
            @endforelse
        </div>
    </div>
</x-app-layout>