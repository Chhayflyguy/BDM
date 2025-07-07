<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add New Customer Profile') }}
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
                                <x-input-label for="name" :value="__('Customer Name')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                            </div>
                            <div>
                                <x-input-label for="phone" :value="__('Phone Number')" />
                                <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" />
                            </div>
                        </div>

                        <!-- VIP Package Section -->
                        <div class="mt-6 border-t pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Balance Package (Optional)</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="vip_package" :value="__('Select Package')" />
                                    <select name="vip_package" id="vip_package" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                        <option value="">None</option>
                                        <option value="vip">VIP Card ($250 get $300)</option>
                                        <option value="silver">Silver Card ($500 get $650)</option>
                                        <option value="golden">Golden Card ($1000 get $1500)</option>
                                        <option value="diamond">Diamond Card ($2000 get $3000)</option>
                                    </select>
                                </div>
                                <div>
                                    <x-input-label for="vip_card_number" :value="__('Balance Card ID')" />
                                    <div class="flex mt-1">
                                        <span id="vip_id_prefix" class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm"></span>
                                        <x-text-input id="vip_card_number" class="block w-full rounded-l-none" type="text" name="vip_card_number" :value="old('vip_card_number')" placeholder="Enter ID Number" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Other Details -->
                        <div class="mt-6 border-t pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Additional Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <div>
                                    <x-input-label for="gender" :value="__('Gender')" />
                                    <select name="gender" id="gender" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                        <option value="">Select...</option>
                                        <option value="Male" @selected(old('gender')=='Male' )>Male</option>
                                        <option value="Female" @selected(old('gender')=='Female' )>Female</option>
                                        <option value="Other" @selected(old('gender')=='Other' )>Other</option>
                                    </select>
                                </div>
                                <div>
                                    <x-input-label for="age" :value="__('Age')" />
                                    <x-text-input id="age" class="block mt-1 w-full" type="number" name="age" :value="old('age')" />
                                </div>
                                <div>
                                    <x-input-label for="height" :value="__('Height (e.g., 175cm)')" />
                                    <x-text-input id="height" class="block mt-1 w-full" type="text" name="height" :value="old('height')" />
                                </div>
                                <div>
                                    <x-input-label for="weight" :value="__('Weight (e.g., 70kg)')" />
                                    <x-text-input id="weight" class="block mt-1 w-full" type="text" name="weight" :value="old('weight')" />
                                </div>
                            </div>
                            <!-- Health Conditions -->
                            <div class="mt-6">
                                <x-input-label :value="__('Health Conditions (select all that apply)')" />
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
                                <x-input-label :value="__('Problem Areas (select all that apply)')" />
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
                            <a href="{{ route('customers.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">{{ __('Cancel') }}</a>
                            <x-primary-button>{{ __('Save Customer') }}</x-primary-button>
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