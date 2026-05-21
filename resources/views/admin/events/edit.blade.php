<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.edit_event') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4">
                <a href="{{ route('admin.events.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    {{ __('messages.back_to_events') }}
                </a>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.events.update', $event) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div>
                            <x-input-label for="title" :value="__('messages.event_title')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $event->title)" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>
                        <div class="mt-4">
                            <x-input-label for="description" :value="__('messages.event_description')" />
                            <textarea id="description" name="description" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" rows="4">{{ old('description', $event->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>
                        <div class="mt-4">
                            <x-input-label for="status" :value="__('messages.event_status')" />
                            <select id="status" name="status" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="active" {{ old('status', $event->status) === 'active' ? 'selected' : '' }}>{{ __('messages.active') }}</option>
                                <option value="inactive" {{ old('status', $event->status) === 'inactive' ? 'selected' : '' }}>{{ __('messages.inactive') }}</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="start_date" :value="__('messages.start_date')" />
                                <x-text-input id="start_date" class="block mt-1 w-full" type="datetime-local" name="start_date" :value="old('start_date', $event->start_date?->format('Y-m-d\TH:i'))" />
                                <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="end_date" :value="__('messages.end_date')" />
                                <x-text-input id="end_date" class="block mt-1 w-full" type="datetime-local" name="end_date" :value="old('end_date', $event->end_date?->format('Y-m-d\TH:i'))" />
                                <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                            </div>
                        </div>
                        @if($event->media_path)
                            <div class="mt-4">
                                <x-input-label :value="__('messages.current_media')" />
                                <div class="mt-2 p-4 border border-gray-200 rounded-lg">
                                    @if($event->media_type === 'image')
                                        <img src="{{ asset('storage/' . $event->media_path) }}" alt="{{ $event->title }}" class="max-w-md rounded-lg">
                                    @elseif($event->media_type === 'video')
                                        <video controls class="max-w-md rounded-lg">
                                            <source src="{{ asset('storage/' . $event->media_path) }}">
                                            Your browser does not support the video tag.
                                        </video>
                                    @endif
                                </div>
                            </div>
                        @endif
                        <div class="mt-4">
                            <x-input-label for="media" :value="$event->media_path ? __('messages.change_event_media') : __('messages.event_media')" />
                            <input id="media" name="media" type="file" accept="image/*,video/*" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                            <p class="mt-1 text-sm text-gray-500">
                                {{ $event->media_path ? __('messages.leave_blank_to_keep_current') : __('messages.upload_media_max_10mb') }}
                            </p>
                            <x-input-error :messages="$errors->get('media')" class="mt-2" />
                        </div>
                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('admin.events.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">{{ __('messages.cancel') }}</a>
                            <x-primary-button>{{ __('messages.update_event') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
