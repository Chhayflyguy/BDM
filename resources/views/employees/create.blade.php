<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Add New Employee') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('employees.store') }}">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="name" :value="__('Employee Name')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                            </div>
                            <div>
                                <x-input-label for="phone" :value="__('Phone Number')" />
                                <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" />
                            </div>
                            <div>
                                <x-input-label for="email" :value="__('Email (Optional)')" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" />
                            </div>
                            <div>
                                <x-input-label for="gender" :value="__('Gender')" />
                                <select name="gender" id="gender" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">Select...</option>
                                    <option value="Male" @selected(old('gender') == 'Male')>Male</option>
                                    <option value="Female" @selected(old('gender') == 'Female')>Female</option>
                                    <option value="Other" @selected(old('gender') == 'Other')>Other</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label for="address" :value="__('Address (Optional)')" />
                                <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address')" />
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label for="experience" :value="__('Experience / Notes (Optional)')" />
                                <textarea id="experience" name="experience" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('experience') }}</textarea>
                            </div>
                        </div>
                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('employees.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">{{ __('Cancel') }}</a>
                            <x-primary-button>{{ __('Save Employee') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>