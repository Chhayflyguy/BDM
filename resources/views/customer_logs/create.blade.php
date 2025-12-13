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

                        <!-- Customer Search & Select -->
                        <div>
                            <x-input-label for="customer_search" :value="__('messages.search_customer')" />
                            <div class="relative mt-1">
                                <input type="text" 
                                       id="customer_search" 
                                       class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                       placeholder="{{ __('messages.search_by_name_id_phone') }}"
                                       autocomplete="off">
                                <div id="search_results" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg mt-1 max-h-60 overflow-y-auto hidden">
                                </div>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">{{ __('messages.search_to_find_all_customers') }}</p>
                        </div>

                        <!-- Customer Selection Dropdown (Default: Today's Customers) -->
                        <div class="mt-4">
                            <x-input-label for="customer_id" :value="__('messages.select_customer')" />
                            <select name="customer_id" id="customer_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">-- {{ __('messages.please_select_a_customer') }} --</option>
                                @if($todayCustomers->count() > 0)
                                    <optgroup label="{{ __('messages.todays_new_customers') }}">
                                        @foreach($todayCustomers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->name }} (ID: {{ $customer->customer_gid }})</option>
                                        @endforeach
                                    </optgroup>
                                @else
                                    <option value="" disabled>-- {{ __('messages.no_new_customers_added_today') }} --</option>
                                @endif
                            </select>
                            <x-input-error :messages="$errors->get('customer_id')" class="mt-2" />
                        </div>

                        <!-- Selected Customer Display -->
                        <div id="selected_customer_info" class="mt-4 p-3 bg-indigo-50 rounded-md hidden">
                            <p class="text-sm font-medium text-indigo-800">{{ __('messages.selected_customer') }}:</p>
                            <p id="selected_customer_name" class="text-indigo-700"></p>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('customer_search');
            const searchResults = document.getElementById('search_results');
            const customerSelect = document.getElementById('customer_id');
            const selectedInfo = document.getElementById('selected_customer_info');
            const selectedName = document.getElementById('selected_customer_name');
            
            let searchTimeout = null;

            // Search functionality
            searchInput.addEventListener('input', function() {
                const query = this.value.trim();
                
                // Clear previous timeout
                if (searchTimeout) {
                    clearTimeout(searchTimeout);
                }

                if (query.length < 1) {
                    searchResults.classList.add('hidden');
                    return;
                }

                // Debounce search
                searchTimeout = setTimeout(function() {
                    fetch(`{{ route('customers.search') }}?query=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            searchResults.innerHTML = '';
                            
                            if (data.length === 0) {
                                searchResults.innerHTML = '<div class="p-3 text-gray-500 text-sm">{{ __("messages.no_customers_found") }}</div>';
                            } else {
                                data.forEach(customer => {
                                    const div = document.createElement('div');
                                    div.className = 'p-3 hover:bg-indigo-50 cursor-pointer border-b border-gray-100 last:border-b-0';
                                    div.innerHTML = `
                                        <div class="font-medium text-gray-900">${customer.name}</div>
                                        <div class="text-sm text-gray-500">ID: ${customer.customer_gid} ${customer.phone ? '| {{ __("messages.phone") }}: ' + customer.phone : ''}</div>
                                    `;
                                    div.addEventListener('click', function() {
                                        selectCustomer(customer);
                                    });
                                    searchResults.appendChild(div);
                                });
                            }
                            
                            searchResults.classList.remove('hidden');
                        })
                        .catch(error => {
                            console.error('Search error:', error);
                        });
                }, 300);
            });

            // Hide search results when clicking outside
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                    searchResults.classList.add('hidden');
                }
            });

            // Select customer from search results
            function selectCustomer(customer) {
                // Check if option exists in dropdown
                let optionExists = false;
                for (let option of customerSelect.options) {
                    if (option.value == customer.id) {
                        optionExists = true;
                        break;
                    }
                }

                // Add option if it doesn't exist
                if (!optionExists) {
                    const searchedGroup = customerSelect.querySelector('optgroup[label="{{ __("messages.searched_customers") }}"]');
                    if (searchedGroup) {
                        const newOption = document.createElement('option');
                        newOption.value = customer.id;
                        newOption.textContent = `${customer.name} (ID: ${customer.customer_gid})`;
                        searchedGroup.appendChild(newOption);
                    } else {
                        const optgroup = document.createElement('optgroup');
                        optgroup.label = '{{ __("messages.searched_customers") }}';
                        const newOption = document.createElement('option');
                        newOption.value = customer.id;
                        newOption.textContent = `${customer.name} (ID: ${customer.customer_gid})`;
                        optgroup.appendChild(newOption);
                        customerSelect.appendChild(optgroup);
                    }
                }

                // Select the customer
                customerSelect.value = customer.id;
                
                // Show selected customer info
                selectedName.textContent = `${customer.name} (ID: ${customer.customer_gid})`;
                selectedInfo.classList.remove('hidden');
                
                // Clear search
                searchInput.value = '';
                searchResults.classList.add('hidden');
            }

            // Update selected customer info when dropdown changes
            customerSelect.addEventListener('change', function() {
                if (this.value) {
                    const selectedOption = this.options[this.selectedIndex];
                    selectedName.textContent = selectedOption.textContent;
                    selectedInfo.classList.remove('hidden');
                } else {
                    selectedInfo.classList.add('hidden');
                }
            });
        });
    </script>
</x-app-layout>