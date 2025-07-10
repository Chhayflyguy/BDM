<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('messages.customer_profile') }}: <span class="font-bold">{{ $customer->name }}</span>
            </h2>
            <a href="{{ route('customers.edit', $customer) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('messages.edit_profile') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 border border-green-200 rounded-md shadow-sm">
                {{ session('success') }}
            </div>
            @endif

            <div class="bg-white p-6 rounded-lg shadow-sm">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div><span class="font-medium text-gray-500">Customer ID:</span> {{ $customer->customer_gid }}</div>
                    <div><span class="font-medium text-gray-500">Phone:</span> {{ $customer->phone ?? 'N/A' }}</div>
                    <div><span class="font-medium text-gray-500">Gender:</span> {{ $customer->gender ?? 'N/A' }}</div>
                    <div><span class="font-medium text-gray-500">Age:</span> {{ $customer->age ?? 'N/A' }}</div>
                    <div><span class="font-medium text-gray-500">Height:</span> {{ $customer->height ?? 'N/A' }}</div>
                    <div><span class="font-medium text-gray-500">Weight:</span> {{ $customer->weight ?? 'N/A' }}</div>
                    <div><span class="font-medium text-gray-500">Balance Card ID:</span> {{ $customer->vip_card_id ?? 'N/A' }}</div>
                    <div>
                        @php
                        // Use the customer's card type for the label, or default to "VIP Balance"
                        $balanceLabel = $customer->vip_card_type ? $customer->vip_card_type . ' Balance' : 'VIP Balance';
                        @endphp
                        <span class="font-medium text-gray-500">{{ $balanceLabel }}:</span>
                        <span class="font-bold text-blue-600">${{ number_format($customer->vip_card_balance, 2) }}</span>
                    </div>
                    <div><span class="font-medium text-gray-500">Balance Expired:</span> {{ $customer->vip_card_expires_at?->format('F j, Y') ?? 'N/A' }}</div>
                    <div>
                        <span class="font-medium text-gray-500">Next Booking:</span>
                        @if($customer->next_booking_date)
                        @php
                        $bookingDate = $customer->next_booking_date;
                        $isCompleted = (bool)$customer->booking_completed_at;
                        $isExpired = !$isCompleted && $bookingDate->isPast();

                        $colorClass = '';
                        if ($isCompleted) $colorClass = 'text-green-600';
                        elseif ($isExpired) $colorClass = 'text-red-600';
                        @endphp
                        <span class="font-bold {{ $colorClass }}">
                            {{ $bookingDate->format('F j, Y') }}
                        </span>
                        @else
                        N/A
                        @endif
                    </div>
                </div>
                <hr class="my-6">
                <div>
                    <h4 class="font-medium text-gray-600 mb-2">{{ __('messages.health_conditions') }}</h4>
                    <p class="text-gray-800">{{ $customer->health_conditions ? implode(', ', $customer->health_conditions) : 'None' }}</p>
                </div>
                <div class="mt-4">
                    <h4 class="font-medium text-gray-600 mb-2">{{ __('messages.problem_areas') }}</h4>
                    <p class="text-gray-800">{{ $customer->problem_areas ? implode(', ', $customer->problem_areas) : 'None' }}</p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('messages.top_up_vip_balance') }}</h3>
                <form action="{{ route('customers.top-up', $customer) }}" method="POST">
                    @csrf
                    <div class="flex items-end space-x-4">
                        <div>
                            <x-input-label for="vip_package" :value="__('messages.select_package')" />
                            <select name="vip_package" id="vip_package" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="vip">{{ __('messages.vip_card_250_300') }}</option>
                                <option value="silver">{{ __('messages.silver_card_500_650') }}</option>
                                <option value="golden">{{ __('messages.golden_card_1000_1500') }}</option>
                                <option value="diamond">{{ __('messages.diamond_card_2000_3000') }}</option>
                            </select>
                        </div>
                        <x-primary-button>{{ __('messages.top_up_balance') }}</x-primary-button>
                    </div>
                </form>
            </div>
            <!-- Log History Section -->
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('messages.log_history') }}</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.date') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.product') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.masseuse') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.payment') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.method') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($customer->logs as $log)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $log->created_at->format('Y-m-d') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $log->product_purchased ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $log->masseuse_name ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${{ number_format($log->payment_amount, 2) }}</td>
                                {{-- This is the new column with the special remark --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $log->payment_method }}
                                    @if($log->payment_method === 'VIP Card')
                                    <span class="text-xs text-blue-600">(from VIP)</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if ($log->status === 'completed')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">{{ __('messages.completed') }}</span>
                                    @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ __('messages.active') }}</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">{{ __('messages.no_logs_found_for_this_customer') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>