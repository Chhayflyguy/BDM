<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.add_new_customer_profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('customers.store') }}">
                        @csrf

                        <!-- Personal Info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="name" :value="__('messages.customer_name')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                            </div>
                            <div>
                                <x-input-label for="phone" :value="__('messages.phone_number')" />
                                <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" />
                            </div>
                        </div>

                        <!-- VIP Package Section -->
                        <div class="mt-6 border-t pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('messages.balance_package_optional') }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="vip_package" :value="__('messages.select_package')" />
                                    <select name="vip_package" id="vip_package" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                        <option value="">{{ __('messages.none') }}</option>
                                        <option value="vip">{{ __('messages.vip_card_250_300') }}</option>
                                        <option value="silver">{{ __('messages.silver_card_500_650') }}</option>
                                        <option value="golden">{{ __('messages.golden_card_1000_1500') }}</option>
                                        <option value="diamond">{{ __('messages.diamond_card_2000_3000') }}</option>
                                    </select>
                                </div>
                                <div>
                                    <x-input-label for="vip_card_number" :value="__('messages.balance_card_id')" />
                                    <div class="flex mt-1">
                                        <span id="vip_id_prefix" class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm"></span>
                                        <x-text-input id="vip_card_number" class="block w-full rounded-l-none" type="text" name="vip_card_number" :value="old('vip_card_number')" placeholder="Enter ID Number" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Other Details -->
                        <div class="mt-6 border-t pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('messages.additional_details') }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <div>
                                    <x-input-label for="gender" :value="__('messages.gender')" />
                                    <select name="gender" id="gender" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                        <option value="">{{ __('messages.select') }}</option>
                                        <option value="Male" @selected(old('gender')=='Male' )>{{ __('messages.male') }}</option>
                                        <option value="Female" @selected(old('gender')=='Female' )>{{ __('messages.female') }}</option>
                                        <option value="Other" @selected(old('gender')=='Other' )>{{ __('messages.other') }}</option>
                                    </select>
                                </div>
                                <div>
                                    <x-input-label for="age" :value="__('messages.age')" />
                                    <x-text-input id="age" class="block mt-1 w-full" type="number" name="age" :value="old('age')" />
                                </div>
                                <div>
                                    <x-input-label for="height" :value="__('messages.height_optional')" />
                                    <x-text-input id="height" class="block mt-1 w-full" type="text" name="height" :value="old('height')" />
                                </div>
                                <div>
                                    <x-input-label for="weight" :value="__('messages.weight_optional')" />
                                    <x-text-input id="weight" class="block mt-1 w-full" type="text" name="weight" :value="old('weight')" />
                                </div>
                            </div>
                            <!-- Health Conditions -->
                            <div class="mt-6">
                                <x-input-label :value="__('messages.health_conditions_optional')" />
                                <div class="mt-2 grid grid-cols-2 gap-4">
                                    @foreach(['High Blood Pressure', 'Diabetes', 'Heart Disease', 'Surgery'] as $condition)
                                    <label class="flex items-center">
                                        <input type="checkbox" name="health_conditions[]" value="{{ $condition }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span class="ms-2 text-sm text-gray-600">{{ $condition }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Problem Areas -->
                            <div class="mt-6">
                                <x-input-label :value="__('messages.problem_areas_optional')" />
                                <div class="mt-2 grid grid-cols-2 gap-4">
                                    @foreach(['Head Part', 'Shoulder Part', 'Waist Part', 'Leg Part'] as $area)
                                    <label class="flex items-center">
                                        <input type="checkbox" name="problem_areas[]" value="{{ $area }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span class="ms-2 text-sm text-gray-600">{{ $area }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('customers.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">{{ __('messages.cancel') }}</a>
                            <x-primary-button>{{ __('messages.save_customer') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const packageSelect = document.getElementById('vip_package');
            const prefixSpan = document.getElementById('vip_id_prefix');

            function updatePrefix() {
                const selectedPackage = packageSelect.value;
                let prefix = '';

                if (selectedPackage) {
                    prefix = selectedPackage.charAt(0).toUpperCase();
                }

                prefixSpan.textContent = prefix;
                prefixSpan.style.display = prefix ? 'inline-flex' : 'none';
            }

            // Initial call to set prefix on page load
            updatePrefix();

            // Listen for changes
            packageSelect.addEventListener('change', updatePrefix);
        }); 
    </script>
</x-app-layout>