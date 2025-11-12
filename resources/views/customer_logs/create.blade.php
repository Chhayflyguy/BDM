<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.add_new_log_for_existing_customer') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    {{ __('messages.back_to_dashboard') }}
                </a>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        {{ __('messages.if_customer_not_in_list') }} <a href="{{ route('customers.create') }}" class="text-indigo-600 hover:underline">{{ __('messages.add_new_customer_profile') }}</a>.
                    </div>
                    <form method="POST" action="{{ route('customer_logs.store') }}">
                        @csrf   

                        <!-- Customer -->
                        <div>
                            <x-input-label for="customer_id" :value="__('Select Customer')" />
                            <select name="customer_id" id="customer_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">-- Please select a customer --</option>
                                @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }} (ID: {{ $customer->customer_gid }})</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('customer_id')" class="mt-2" />
                        </div>
                        <!-- Next Meeting -->
                        <div class="mt-4">
                            <x-input-label for="next_meeting" :value="__('messages.date_optional')" />
                            <x-text-input id="next_meeting" class="block mt-1 w-full" type="date" name="next_meeting" :value="old('next_meeting')" />
                        </div>

                        <!-- Notes -->
                        <div class="mt-4">
                            <x-input-label for="notes" :value="__('Initial Notes (Optional)')" />
                            <textarea id="notes" name="notes" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes') }}</textarea>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                                {{ __('messages.cancel') }}
                            </a>
                            <x-primary-button>
                                {{ __('messages.create_log') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>