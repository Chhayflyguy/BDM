<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('All Customers') }}</h2>
            <a href="{{ route('customers.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">{{ __('Add New Customer') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow-sm mb-8">
                <div class="flex justify-between items-end">
                    <!-- Filter and Search Form -->
                    <form action="{{ route('customers.index') }}" method="GET" class="flex items-end space-x-4">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                            <x-text-input id="search" name="search" type="text" class="mt-1 block" placeholder="Name, ID, phone..." :value="request('search')" />
                        </div>
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
                    <form action="{{ route('customers.export') }}" method="POST">
                        @csrf
                        <input type="hidden" name="month" value="{{ $currentMonth }}">
                        <input type="hidden" name="year" value="{{ $currentYear }}">
                        <x-secondary-button type="submit">Export to Excel</x-secondary-button>
                    </form>
                </div>
            </div>
            
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 border rounded-md shadow-sm">{{ session('success') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">VIP ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gender</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Age</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Next Booking</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($customers as $customer)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $customer->customer_gid }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $customer->vip_card_id ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <a href="{{ route('customers.show', $customer) }}" class="text-indigo-600 hover:text-indigo-900">
                                        {{ $customer->name }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $customer->phone ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $customer->gender ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $customer->age ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($customer->next_booking_date)
                                    {{ \Carbon\Carbon::parse($customer->next_booking_date)->format('M d, Y') }}
                                    @else
                                    N/A
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No new customers found for this month.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4">
                    {{ $customers->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>