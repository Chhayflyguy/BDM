<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('messages.edit_user') }}
        </h2>
    </x-slot>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        @if ($user->email === 'bdm@gmail.com')
            <div class="mb-4 p-4 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded-lg">
                {{ __('messages.admin_edit_warning') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('messages.name') }}
                </label>
                <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('messages.email') }}
                </label>
                <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" 
                    {{ $user->email === 'bdm@gmail.com' ? 'disabled' : 'required' }}
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 
                           {{ $user->email === 'bdm@gmail.com' ? 'bg-gray-100' : '' }}">
                @if ($user->email === 'bdm@gmail.com')
                    <p class="mt-1 text-xs text-gray-500">{{ __('messages.admin_email_locked') }}</p>
                @endif
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Role -->
            <div class="mb-4">
                <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('messages.role') }}
                </label>
                <select id="role" name="role" 
                    {{ $user->email === 'bdm@gmail.com' ? 'disabled' : 'required' }}
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500
                           {{ $user->email === 'bdm@gmail.com' ? 'bg-gray-100' : '' }}">
                    <option value="staff" {{ old('role', $user->role) === 'staff' ? 'selected' : '' }}>
                        {{ __('messages.staff') }}
                    </option>
                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>
                        {{ __('messages.admin') }}
                    </option>
                </select>
                @if ($user->email === 'bdm@gmail.com')
                    <p class="mt-1 text-xs text-gray-500">{{ __('messages.admin_role_locked') }}</p>
                @endif
                @error('role')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password (Optional) -->
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('messages.new_password') }} ({{ __('messages.optional') }})
                </label>
                <input id="password" type="password" name="password"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <p class="mt-1 text-xs text-gray-500">{{ __('messages.leave_blank_to_keep') }}</p>
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('messages.confirm_password') }}
                </label>
                <input id="password_confirmation" type="password" name="password_confirmation"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

                <div class="flex items-center justify-end mt-6 space-x-3">
                    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('messages.cancel') }}
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ __('messages.update_user') }}
                    </button>
                </div>
        </form>
    </div>
    </div>
</x-app-layout>
