<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('messages.daily_customer_logs') }}
            </h2>
            <a href="{{ route('customer_logs.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('messages.add_new_log') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow-sm mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('messages.export_monthly_report') }}</h3>
                <form action="{{ route('customer_logs.export') }}" method="POST" class="flex items-end space-x-4">
                    @csrf
                    <div>
                        <label for="month" class="block text-sm font-medium text-gray-700">{{ __('messages.month') }}</label>
                        <select id="month" name="month" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            @foreach($months as $num => $name)
                            <option value="{{ $num }}" {{ $num == date('m') ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="year" class="block text-sm font-medium text-gray-700">{{ __('messages.year') }}</label>
                        <select id="year" name="year" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            @foreach($years as $year)
                            <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <x-primary-button>{{ __('messages.export_to_excel') }}</x-primary-button>
                </form>
            </div>

            @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 border border-green-200 rounded-md shadow-sm">
                {{ session('success') }}
            </div>
            @endif

            @forelse ($groupedLogs as $date => $logs)
            <div class="mt-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4">{{ \Carbon\Carbon::parse($date)->format('F j, Y') }}</h3>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.customer') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.product') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.masseuse') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.payment') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.method') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.status') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($logs as $log)
                                <tr class="{{ $log->payment_method === 'VIP Card' ? 'bg-blue-50' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        @if($log->customer)
                                        <a href="{{ route('customers.show', $log->customer) }}" class="text-indigo-600 hover:text-indigo-900">{{ $log->customer->name }}</a>
                                        <div class="text-xs text-gray-500">ID: {{ $log->customer->customer_gid }}</div>
                                        @else
                                        <span class="text-red-500">{{ __('messages.customer_deleted') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $log->product_purchased ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $log->masseuse_name ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        ${{ number_format($log->payment_amount, 2) }}
                                        {{-- Add the remark for VIP Card payments --}}
                                        @if($log->payment_method === 'VIP Card')
                                        <span class="text-xs text-blue-600">(from VIP)</span>
                                        @endif
                                    </td>
                                    {{-- This is the new column --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $log->payment_method ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if ($log->status === 'completed')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">{{ __('messages.completed') }}</span>
                                        @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ __('messages.active') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        @if ($log->status === 'active')
                                        <a href="{{ route('customer_logs.complete.form', $log) }}" class="text-green-600 hover:text-green-900 mr-4 font-bold">{{ __('messages.complete') }}</a>
                                        <a href="{{ route('customer_logs.edit', $log) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">{{ __('messages.edit') }}</a>
                                        <form action="{{ route('customer_logs.destroy', $log) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this log?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">{{ __('messages.delete') }}</button>
                                        </form>
                                        @else
                                        <span class="text-gray-400">{{ __('messages.closed') }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-100">
                                <tr>
                                    <td colspan="3" class="px-6 py-3 text-right text-sm font-bold text-gray-600">Total for {{ \Carbon\Carbon::parse($date)->format('M d') }}:</td>
                                    <td class="px-6 py-3 text-left text-sm font-bold text-gray-700">${{ number_format($dailyTotals[$date]['total_payment'], 2) }}</td>
                                    <td colspan="3" class="px-6 py-3 text-left text-sm font-bold text-gray-700">{{ $dailyTotals[$date]['products_count'] }} Transactions</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-8">
                <div class="p-6 text-gray-900 text-center">No customer logs found.</div>
            </div>
            @endforelse
        </div>
    </div>
</x-app-layout>