<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('messages.activity_logs') }}
        </h2>
    </x-slot>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('messages.user') }}
                </label>
                <select name="user_id" id="user_id" class="w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">{{ __('messages.all_users') }}</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="action" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('messages.action') }}
                </label>
                <select name="action" id="action" class="w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">{{ __('messages.all_actions') }}</option>
                    <option value="create" {{ request('action') === 'create' ? 'selected' : '' }}>{{ __('messages.create') }}</option>
                    <option value="update" {{ request('action') === 'update' ? 'selected' : '' }}>{{ __('messages.update') }}</option>
                    <option value="delete" {{ request('action') === 'delete' ? 'selected' : '' }}>{{ __('messages.delete') }}</option>
                </select>
            </div>

            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('messages.date_from') }}
                </label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                    class="w-full rounded-md border-gray-300 shadow-sm">
            </div>

            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('messages.date_to') }}
                </label>
                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                    class="w-full rounded-md border-gray-300 shadow-sm">
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                    {{ __('messages.filter') }}
                </button>
            </div>
        </form>
    </div>

    <!-- Activity Logs Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('messages.user') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('messages.action') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('messages.description') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('messages.timestamp') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($activityLogs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $log->user?->name ?? __('messages.deleted_user') }}</div>
                                <div class="text-xs text-gray-500">{{ $log->user?->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $log->action === 'create' ? 'bg-green-100 text-green-800' : 
                                       ($log->action === 'update' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($log->action) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $log->description }}</div>
                                <div class="text-xs text-gray-500">{{ $log->subject_type_name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $log->created_at->format('Y-m-d H:i:s') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                {{ __('messages.no_activity_logs') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $activityLogs->appends(request()->query())->links() }}
    </div>
</div>
</x-app-layout>
