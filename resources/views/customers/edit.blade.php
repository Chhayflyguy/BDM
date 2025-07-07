<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Customer Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('customers.update', $customer) }}">
                        @csrf
                        @method('PATCH')

                        <div>
                            <x-input-label for="name" :value="__('Customer Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $customer->name)" required autofocus />
                        </div>
                        <div class="mt-4">
                            <x-input-label for="phone" :value="__('Phone Number')" />
                            <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone', $customer->phone)" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                            <div>
                                <x-input-label for="gender" :value="__('Gender')" />
                                <select name="gender" id="gender" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="Male" @selected(old('gender', $customer->gender) == 'Male')>Male</option>
                                    <option value="Female" @selected(old('gender', $customer->gender) == 'Female')>Female</option>
                                    <option value="Other" @selected(old('gender', $customer->gender) == 'Other')>Other</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="age" :value="__('Age')" />
                                <x-text-input id="age" class="block mt-1 w-full" type="number" name="age" :value="old('age', $customer->age)" />
                            </div>
                            <div>
                                <x-input-label for="height" :value="__('Height')" />
                                <x-text-input id="height" class="block mt-1 w-full" type="text" name="height" :value="old('height', $customer->height)" />
                            </div>
                            <div>
                                <x-input-label for="weight" :value="__('Weight')" />
                                <x-text-input id="weight" class="block mt-1 w-full" type="text" name="weight" :value="old('weight', $customer->weight)" />
                            </div>
                        </div>

                        <div class="mt-6">
                            <x-input-label :value="__('Health Conditions')" />
                            <div class="mt-2 grid grid-cols-2 gap-4">
                                @foreach(['High Blood Pressure', 'Diabetes', 'Heart Disease', 'Surgery'] as $condition)
                                <label class="flex items-center">
                                    <input type="checkbox" name="health_conditions[]" value="{{ $condition }}" @checked(in_array($condition, old('health_conditions', $customer->health_conditions ?? []))) class="rounded border-gray-300">
                                    <span class="ms-2 text-sm text-gray-600">{{ $condition }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="mt-6">
                            <x-input-label :value="__('Problem Areas')" />
                            <div class="mt-2 grid grid-cols-2 gap-4">
                                @foreach(['Head Part', 'Shoulder Part', 'Waist Part', 'Leg Part'] as $area)
                                <label class="flex items-center">
                                    <input type="checkbox" name="problem_areas[]" value="{{ $area }}" @checked(in_array($area, old('problem_areas', $customer->problem_areas ?? []))) class="rounded border-gray-300">
                                    <span class="ms-2 text-sm text-gray-600">{{ $area }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-6 border-t pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">VIP Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="vip_card_id" :value="__('Balance Card ID')" />
                                    <x-text-input id="vip_card_id" class="block mt-1 w-full" type="text" name="vip_card_id" :value="old('vip_card_id', $customer->vip_card_id)" />
                                </div>

                                {{-- THIS IS THE NEW FIELD TO ADD --}}
                                <div>
                                    <x-input-label for="vip_card_expires_at" :value="__('Balance Card Expires At')" />
                                    <x-text-input id="vip_card_expires_at" class="block mt-1 w-full" type="date" name="vip_card_expires_at" :value="old('vip_card_expires_at', $customer->vip_card_expires_at?->format('Y-m-d'))" />
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('customers.show', $customer) }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button>
                                {{ __('Update Profile') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>