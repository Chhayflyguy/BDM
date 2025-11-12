<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.edit_log_for') }} {{ $customerLog->customer->name }}
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
                    <form method="POST" action="{{ route('customer_logs.update', $customerLog) }}">
                        @csrf
                        @method('PATCH')
                        
                        <div class="mb-4">
                            <p class="text-gray-600">{{ __('messages.you_can_only_edit_the_notes_and_next_meeting_date_for_an_active_log') }}</p>
                        </div>
                        
                        <!-- Next Meeting -->
                        <div class="mt-4">
                            <x-input-label for="next_meeting" :value="__('messages.next_meeting_date_optional')" />
                            <x-text-input id="next_meeting" class="block mt-1 w-full" type="date" name="next_meeting" :value="old('next_meeting', $customerLog->next_meeting?->format('Y-m-d'))" />
                        </div>

                        <!-- Notes -->
                        <div class="mt-4">
                            <x-input-label for="notes" :value="__('messages.notes_optional')" />
                            <textarea id="notes" name="notes" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes', $customerLog->notes) }}</textarea>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                                {{ __('messages.cancel') }}
                            </a>
                            <x-primary-button>
                                {{ __('messages.update_log') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
