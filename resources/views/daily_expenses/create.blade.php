<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Record a New Expense') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('daily_expenses.store') }}">
                        @csrf
                        
                        <div>
                            <x-input-label for="item_name" :value="__('Expense Item or Service')" />
                            <x-text-input id="item_name" class="block mt-1 w-full" type="text" name="item_name" :value="old('item_name')" required autofocus />
                        </div>
                        <div class="mt-4">
                            <x-input-label for="purpose" :value="__('Purpose (Optional)')" />
                            <x-text-input id="purpose" class="block mt-1 w-full" type="text" name="purpose" :value="old('purpose')" />
                        </div>
                        <div class="mt-4">
                            <x-input-label for="amount" :value="__('Amount ($)')" />
                            <x-text-input id="amount" class="block mt-1 w-full" type="number" name="amount" :value="old('amount')" step="0.01" required />
                        </div>
                        <div class="mt-4">
                            <x-input-label for="expense_date" :value="__('Date of Expense')" />
                            <x-text-input id="expense_date" class="block mt-1 w-full" type="date" name="expense_date" :value="old('expense_date', now()->format('Y-m-d'))" required />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('daily_expenses.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">{{ __('Cancel') }}</a>
                            <x-primary-button>{{ __('Save Expense') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>