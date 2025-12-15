<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.edit_log_for') }} <span class="font-bold">{{ $customerLog->customer->name }}</span>
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
                    <form method="POST" action="{{ route('customer_logs.update_completed', $customerLog) }}">
                        @csrf
                        @method('PATCH')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="product_id" :value="__('Select Product (Optional)')" />
                                <select name="product_id" id="product_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">-- No Product / Manual Entry --</option>
                                    @foreach($products as $product)
                                    <option value="{{ $product->id }}" 
                                            data-price="{{ $product->price }}" 
                                            data-name="{{ $product->name }}"
                                            @selected(old('product_id', $customerLog->product_id)==$product->id)>
                                        {{ $product->name }} (Stock: {{ $product->quantity }}) - ${{ number_format($product->price, 2) }}
                                    </option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Select a product to auto-fill name and price</p>
                            </div>
                            <div>
                                <x-input-label for="product_quantity" :value="__('Quantity')" />
                                <x-text-input id="product_quantity" class="block mt-1 w-full" type="number" name="product_quantity" :value="old('product_quantity', $customerLog->product_quantity ?? 1)" min="1" />
                                <p class="text-xs text-gray-500 mt-1">Number of units to purchase</p>
                            </div>
                            <div>
                                <x-input-label for="product_price" :value="__('Total Product Price')" />
                                <x-text-input id="product_price" class="block mt-1 w-full" type="number" name="product_price" :value="old('product_price', $customerLog->product_price)" step="0.01" />
                                <p class="text-xs text-gray-500 mt-1">Auto-calculated: unit price × quantity</p>
                            </div>
                            <!-- Hidden field for manual product name entry if needed -->
                            <input type="hidden" id="product_purchased" name="product_purchased" value="{{ old('product_purchased', $customerLog->product_purchased) }}" />
                            <div>
                                <x-input-label for="employee_id" :value="__('Massage By (Optional)')" />
                                <select name="employee_id" id="employee_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">-- Select Employee --</option>
                                    @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" @selected(old('employee_id', $customerLog->employee_id)==$employee->id)>{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="massage_price" :value="__('Massage Price (Optional)')" />
                                <x-text-input id="massage_price" class="block mt-1 w-full" type="number" name="massage_price" :value="old('massage_price', $customerLog->massage_price)" step="0.01" />
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label for="payment_method" :value="__('Payment Method')" />
                                <select name="payment_method" id="payment_method" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                                    <option value="Cash" @selected(old('payment_method', $customerLog->payment_method)=='Cash')>Cash</option>
                                    <option value="ABA" @selected(old('payment_method', $customerLog->payment_method)=='ABA')>ABA</option>
                                    <option value="AC" @selected(old('payment_method', $customerLog->payment_method)=='AC')>ACLEDA</option>
                                    <option value="VIP Card" @selected(old('payment_method', $customerLog->payment_method)=='VIP Card')>VIP Card</option>
                                    <option value="Other Bank" @selected(old('payment_method', $customerLog->payment_method)=='Other Bank')>Other Bank</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-4">
                            <x-input-label for="next_booking_date" :value="__('Next Massage Booking Date (Optional)')" />
                            <x-text-input id="next_booking_date" class="block mt-1 w-full" type="date" name="next_booking_date" :value="old('next_booking_date', $customerLog->customer->next_booking_date?->format('Y-m-d'))" />
                        </div>
                        @if ($errors->any())
                        <div class="mt-4 text-sm text-red-600">{{ $errors->first() }}</div>
                        @endif

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">{{ __('messages.cancel') }}</a>
                            <x-primary-button>{{ __('messages.update_log') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-populate product price when product is selected or quantity changes
        const productSelect = document.getElementById('product_id');
        const quantityInput = document.getElementById('product_quantity');
        const priceInput = document.getElementById('product_price');
        const productNameInput = document.getElementById('product_purchased');
        
        let unitPrice = 0; // Store unit price for calculation
        
        function updateTotalPrice() {
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            
            if (productSelect.value) {
                // Product selected - calculate total price
                unitPrice = parseFloat(selectedOption.getAttribute('data-price')) || 0;
                const quantity = parseInt(quantityInput.value) || 1;
                const totalPrice = unitPrice * quantity;
                
                priceInput.value = totalPrice.toFixed(2);
                productNameInput.value = selectedOption.getAttribute('data-name');
                quantityInput.removeAttribute('disabled');
            } else {
                // No product selected - clear and allow manual entry
                unitPrice = 0;
                productNameInput.value = '';
                quantityInput.value = 1; // Reset quantity
            }
        }
        
        // Listen for product selection changes
        productSelect.addEventListener('change', updateTotalPrice);
        
        // Listen for quantity changes
        quantityInput.addEventListener('input', function() {
            if (productSelect.value && unitPrice > 0) {
                const quantity = parseInt(this.value) || 1;
                const totalPrice = unitPrice * quantity;
                priceInput.value = totalPrice.toFixed(2);
            }
        });
        
        // Initialize on page load
        const currentProductId = '{{ old('product_id', $customerLog->product_id) }}';
        if (currentProductId) {
            productSelect.value = currentProductId;
            updateTotalPrice();
        }
    </script>
</x-app-layout>
