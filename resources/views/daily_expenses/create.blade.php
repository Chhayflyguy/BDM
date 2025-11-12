<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.record_a_new_expense') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4">
                <a href="{{ route('daily_expenses.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    {{ __('messages.back_to_expenses') }}
                </a>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('daily_expenses.store') }}">
                        @csrf
                        
                        <div>
                            <x-input-label for="item_name" :value="__('messages.expense_item_or_service')" />
                            <x-text-input id="item_name" class="block mt-1 w-full" type="text" name="item_name" :value="old('item_name')" required autofocus />
                        </div>
                        <div class="mt-4">
                            <x-input-label for="purpose" :value="__('messages.purpose_optional')" />
                            <x-text-input id="purpose" class="block mt-1 w-full" type="text" name="purpose" :value="old('purpose')" />
                        </div>
                        <div class="mt-4">
                            <x-input-label for="amount" :value="__('messages.amount_optional')" />
                            <x-text-input id="amount" class="block mt-1 w-full" type="number" name="amount" :value="old('amount')" step="0.01" required />
                        </div>
                        <div class="mt-4">
                            <x-input-label for="expense_date" :value="__('messages.date_of_expense')" />
                            <x-text-input id="expense_date" class="block mt-1 w-full" type="date" name="expense_date" :value="old('expense_date', now()->format('Y-m-d'))" required />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('daily_expenses.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">{{ __('messages.cancel') }}</a>
                            <x-primary-button>{{ __('messages.save_expense') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>