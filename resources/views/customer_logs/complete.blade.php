<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Complete Log for: ') }} <span class="font-bold">{{ $customerLog->customer->name }}</span>
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('customer_logs.complete.submit', $customerLog) }}">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="product_purchased" :value="__('Product Purchased (Optional)')" />
                                <x-text-input id="product_purchased" class="block mt-1 w-full" type="text" name="product_purchased" :value="old('product_purchased')" />
                            </div>
                            <div>
                                <x-input-label for="product_price" :value="__('Product Price (Optional)')" />
                                <x-text-input id="product_price" class="block mt-1 w-full" type="number" name="product_price" :value="old('product_price')" step="0.01" />
                            </div>
                            <div>
                                <x-input-label for="employee_id" :value="__('Massage By (Optional)')" />
                                <select name="employee_id" id="employee_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">-- Select Employee --</option>
                                    @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" @selected(old('employee_id')==$employee->id)>{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="massage_price" :value="__('Massage Price (Optional)')" />
                                <x-text-input id="massage_price" class="block mt-1 w-full" type="number" name="massage_price" :value="old('massage_price')" step="0.01" />
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label for="payment_method" :value="__('Payment Method')" />
                                <select name="payment_method" id="payment_method" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                                    <option value="Cash" @selected(old('payment_method')=='Cash' )>Cash</option>
                                    <option value="ABA" @selected(old('payment_method')=='ABA' )>ABA</option>
                                    <option value="AC" @selected(old('payment_method')=='AC' )>ACLEDA</option>
                                    <option value="VIP Card" @selected(old('payment_method')=='VIP Card' )>VIP Card</option> <!-- NEW -->
                                    <option value="Other Bank" @selected(old('payment_method')=='Other Bank' )>Other Bank</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-4">
                            <x-input-label for="next_booking_date" :value="__('Next Massage Booking Date (Optional)')" />
                            <x-text-input id="next_booking_date" class="block mt-1 w-full" type="date" name="next_booking_date" :value="old('next_booking_date')" />
                        </div>
                        @if ($errors->any())
                        <div class="mt-4 text-sm text-red-600">{{ $errors->first() }}</div>
                        @endif

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">{{ __('Cancel') }}</a>
                            <x-primary-button>{{ __('Mark as Complete') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>