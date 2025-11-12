<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit Employee: ') }} {{ $employee->name }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4">
                <a href="{{ route('employees.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    {{ __('messages.back_to_employees') }}
                </a>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('employees.update', $employee) }}">
                        @csrf
                        @method('PATCH')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="name" :value="__('messages.employee_name')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $employee->name)" required autofocus />
                            </div>
                            <div>
                                <x-input-label for="phone" :value="__('messages.phone_number')" />
                                <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone', $employee->phone)" />
                            </div>
                            <div>
                                <x-input-label for="email" :value="__('messages.email_optional')" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $employee->email)" />
                            </div>
                            <div>
                                <x-input-label for="gender" :value="__('messages.gender')" />
                                <select name="gender" id="gender" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">Select...</option>
                                    <option value="Male" @selected(old('gender', $employee->gender) == 'Male')>Male</option>
                                    <option value="Female" @selected(old('gender', $employee->gender) == 'Female')>Female</option>
                                    <option value="Other" @selected(old('gender', $employee->gender) == 'Other')>Other</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label for="address" :value="__('messages.address_optional')" />
                                <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address', $employee->address)" />
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label for="experience" :value="__('messages.experience_optional')" />
                                <textarea id="experience" name="experience" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('experience', $employee->experience) }}</textarea>
                            </div>
                        </div>
                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('employees.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">{{ __('messages.cancel') }}</a>
                            <x-primary-button>{{ __('messages.update_employee') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>