<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="mb-4">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    {{ __('Back to Dashboard') }}
                </a>
            </div>

            {{-- Profile Card Section --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="flex items-center space-x-4">
                    {{-- Avatar --}}
                    @if (Auth::user()->avatar)
                    {{-- The image from Google is already square, so it works fine --}}
                    <img class="h-16 w-16 rounded-full object-cover avatar-spin-on-hover" src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}">
                    @else
                    {{-- We apply our new, more specific class to the placeholder span --}}
                    <span class="bg-gray-500 avatar-spin-on-hover avatar-circle">
                        <span class="text-xl font-medium leading-none text-white">
                            @php
                            $nameParts = explode(' ', Auth::user()->name);
                            $initials = count($nameParts) > 1
                            ? strtoupper(substr($nameParts[0], 0, 1) . substr(end($nameParts), 0, 1))
                            : strtoupper(substr(Auth::user()->name, 0, 2));
                            @endphp
                            {{ $initials }}
                        </span>
                    </span>
                    @endif

                    {{-- Name and Email --}}
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">
                            {{ Auth::user()->name }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-600">
                            {{ Auth::user()->email }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.set-security-questions-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>