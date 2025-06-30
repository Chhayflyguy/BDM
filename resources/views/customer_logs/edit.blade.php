<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Log for ') }} {{ $customerLog->customer->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('customer_logs.update', $customerLog) }}">
                        @csrf
                        @method('PATCH')
                        
                        <div class="mb-4">
                            <p class="text-gray-600">You can only edit the notes and next meeting date for an active log. To change payment info, you must complete the log.</p>
                        </div>
                        
                        <!-- Next Meeting -->
                        <div class="mt-4">
                            <x-input-label for="next_meeting" :value="__('Next Meeting Date (Optional)')" />
                            <x-text-input id="next_meeting" class="block mt-1 w-full" type="date" name="next_meeting" :value="old('next_meeting', $customerLog->next_meeting?->format('Y-m-d'))" />
                        </div>

                        <!-- Notes -->
                        <div class="mt-4">
                            <x-input-label for="notes" :value="__('Notes (Optional)')" />
                            <textarea id="notes" name="notes" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes', $customerLog->notes) }}</textarea>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button>
                                {{ __('Update Log') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
