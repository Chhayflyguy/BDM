<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">{{ __('messages.event_management') }}</h2>
                <p class="text-sm text-gray-600 mt-1">{{ __('messages.manage_promotional_events') }}</p>
            </div>
            <a href="{{ route('admin.events.create') }}" class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-wide shadow-lg hover:shadow-xl transition-all duration-200" style="background: linear-gradient(to right, rgb(61, 109, 222), rgb(52, 95, 210));" onmouseover="this.style.background='linear-gradient(to right, rgb(52, 95, 210), rgb(45, 85, 195))';" onmouseout="this.style.background='linear-gradient(to right, rgb(61, 109, 222), rgb(52, 95, 210))';">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('messages.create_event') }}
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-4">
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                {{ __('messages.back_to_admin_dashboard') }}
            </a>
        </div>
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-400 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
            <div class="px-6 py-4" style="background: linear-gradient(to right, rgb(61, 109, 222), rgb(52, 95, 210));">
                <h3 class="text-lg font-semibold text-white">{{ __('messages.events_list') }}</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead style="background: linear-gradient(to right, rgba(61, 109, 222, 0.1), rgba(61, 109, 222, 0.15));">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('messages.event_title') }}</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('messages.event_status') }}</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('messages.event_dates') }}</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($events as $event)
                        <tr class="transition-colors duration-150" onmouseover="this.style.backgroundColor='rgba(61, 109, 222, 0.05)';" onmouseout="this.style.backgroundColor='';">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-16 w-16 rounded-lg overflow-hidden shadow-md">
                                        @if($event->media_path && $event->media_type === 'image')
                                            <img src="{{ asset('storage/' . $event->media_path) }}" alt="{{ $event->title }}" class="h-16 w-16 object-cover">
                                        @elseif($event->media_path && $event->media_type === 'video')
                                            <div class="h-16 w-16 flex items-center justify-center bg-gray-900">
                                                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" />
                                                </svg>
                                            </div>
                                        @else
                                            <div class="h-16 w-16 flex items-center justify-center" style="background: linear-gradient(to bottom right, rgb(61, 109, 222), rgb(45, 85, 195));">
                                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-semibold text-gray-900">{{ $event->title }}</div>
                                        @if($event->description)
                                            <div class="text-xs text-gray-500 mt-1">{{ Str::limit($event->description, 60) }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $event->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $event->status === 'active' ? __('messages.active') : __('messages.inactive') }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    @if($event->start_date)
                                        <div>{{ __('messages.start') }}: {{ $event->start_date->format('M d, Y') }}</div>
                                    @endif
                                    @if($event->end_date)
                                        <div class="text-gray-500 mt-1">{{ __('messages.end') }}: {{ $event->end_date->format('M d, Y') }}</div>
                                    @endif
                                    @if(!$event->start_date && !$event->end_date)
                                        <span class="text-gray-400">{{ __('messages.no_dates_set') }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-3">
                                    <a href="{{ route('admin.events.edit', $event) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg transition-colors duration-200" style="background-color: rgba(61, 109, 222, 0.1); color: rgb(61, 109, 222);" onmouseover="this.style.backgroundColor='rgba(61, 109, 222, 0.2)';" onmouseout="this.style.backgroundColor='rgba(61, 109, 222, 0.1)';">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        {{ __('messages.edit') }}
                                    </a>
                                    <form action="{{ route('admin.events.destroy', $event) }}" method="POST" class="inline-block" onsubmit="return confirm({{ json_encode(__('messages.are_you_sure_delete_event')) }});">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition-colors duration-200">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            {{ __('messages.delete') }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-gray-500 text-lg font-medium mb-2">{{ __('messages.no_events_found') }}</p>
                                    <p class="text-gray-400 text-sm mb-4">{{ __('messages.get_started_create_first_event') }}</p>
                                    <a href="{{ route('admin.events.create') }}" class="inline-flex items-center px-4 py-2 text-white rounded-lg transition-colors" style="background-color: rgb(61, 109, 222);" onmouseover="this.style.backgroundColor='rgb(52, 95, 210)';" onmouseout="this.style.backgroundColor='rgb(61, 109, 222)';">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        {{ __('messages.add_your_first_event') }}
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($events->hasPages())
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    {{ $events->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
