<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">{{ __('messages.admin_dashboard') }}</h2>
                <p class="text-sm text-gray-600 mt-1">{{ __('messages.welcome_back') }}! {{ __('messages.heres_whats_happening_today') }}.</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Products Card -->
            <div class="bg-white rounded-xl shadow-md p-6 card-hover border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 uppercase tracking-wide mb-1">{{ __('messages.total_products') }}</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_products'] }}</p>
                        <a href="{{ route('admin.products.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 mt-2 inline-flex items-center">
                            {{ __('messages.view_all') }}
                            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                    <div class="p-4 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Services Card -->
            <div class="bg-white rounded-xl shadow-md p-6 card-hover border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 uppercase tracking-wide mb-1">{{ __('messages.total_services') }}</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_services'] }}</p>
                        <a href="{{ route('admin.services.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 mt-2 inline-flex items-center">
                            {{ __('messages.view_all') }}
                            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                    <div class="p-4 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Bookings Card -->
            <div class="bg-white rounded-xl shadow-md p-6 card-hover border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 uppercase tracking-wide mb-1">{{ __('messages.total_bookings') }}</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_bookings'] }}</p>
                        <a href="{{ route('admin.bookings.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 mt-2 inline-flex items-center">
                            {{ __('messages.view_all') }}
                            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                    <div class="p-4 bg-gradient-to-br from-green-500 to-green-600 rounded-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Pending Bookings Card -->
            <div class="bg-white rounded-xl shadow-md p-6 card-hover border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 uppercase tracking-wide mb-1">{{ __('messages.pending') }}</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['pending_bookings'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ __('messages.requires_attention') }}</p>
                    </div>
                    <div class="p-4 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Recent Bookings -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Quick Actions -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                    <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-purple-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('messages.quick_actions') }}</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @if(Auth::user()->isAdmin())
                                <a href="{{ route('admin.users.index') }}" class="group flex items-center p-4 bg-white border border-gray-200 rounded-lg hover:border-indigo-300 hover:shadow-md transition-all duration-200">
                                    <div class="flex-shrink-0 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg p-3 group-hover:scale-110 transition-transform">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <h4 class="text-base font-semibold text-gray-900 group-hover:text-indigo-600">{{ __('messages.user_management') }}</h4>
                                        <p class="text-sm text-gray-600 mt-0.5">{{ __('messages.create_user') }} & {{ __('messages.edit_user') }}</p>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-400 group-hover:text-indigo-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>

                                <a href="{{ route('admin.activity-logs.index') }}" class="group flex items-center p-4 bg-white border border-gray-200 rounded-lg hover:border-purple-300 hover:shadow-md transition-all duration-200">
                                    <div class="flex-shrink-0 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg p-3 group-hover:scale-110 transition-transform">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                        </svg>
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <h4 class="text-base font-semibold text-gray-900 group-hover:text-purple-600">{{ __('messages.activity_logs') }}</h4>
                                        <p class="text-sm text-gray-600 mt-0.5">{{ __('messages.user') }} {{ __('messages.actions') }}</p>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-400 group-hover:text-purple-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            @endif

                            <a href="{{ route('admin.products.index') }}" class="group flex items-center p-4 bg-white border border-gray-200 rounded-lg hover:border-blue-300 hover:shadow-md transition-all duration-200">
                                <div class="flex-shrink-0 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-3 group-hover:scale-110 transition-transform">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                                <div class="ml-4 flex-1">
                                    <h4 class="text-base font-semibold text-gray-900 group-hover:text-blue-600">{{ __('messages.manage_products') }}</h4>
                                    <p class="text-sm text-gray-600 mt-0.5">{{ __('messages.add_edit_or_remove_products') }}</p>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>

                            <a href="{{ route('admin.services.index') }}" class="group flex items-center p-4 bg-white border border-gray-200 rounded-lg hover:border-green-300 hover:shadow-md transition-all duration-200">
                                <div class="flex-shrink-0 bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-3 group-hover:scale-110 transition-transform">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-4 flex-1">
                                    <h4 class="text-base font-semibold text-gray-900 group-hover:text-green-600">{{ __('messages.manage_services') }}</h4>
                                    <p class="text-sm text-gray-600 mt-0.5">{{ __('messages.update_services_prices_images') }}</p>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-green-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>

                            <a href="{{ route('admin.bookings.index') }}" class="group flex items-center p-4 bg-white border border-gray-200 rounded-lg hover:border-orange-300 hover:shadow-md transition-all duration-200">
                                <div class="flex-shrink-0 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg p-3 group-hover:scale-110 transition-transform">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="ml-4 flex-1">
                                    <h4 class="text-base font-semibold text-gray-900 group-hover:text-orange-600">{{ __('messages.manage_bookings') }}</h4>
                                    <p class="text-sm text-gray-600 mt-0.5">{{ __('messages.view_confirm_booking_requests') }}</p>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-orange-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>

                            <a href="{{ route('admin.products.create') }}" class="group flex items-center p-4 bg-white border border-gray-200 rounded-lg hover:border-pink-300 hover:shadow-md transition-all duration-200">
                                <div class="flex-shrink-0 bg-gradient-to-br from-pink-500 to-pink-600 rounded-lg p-3 group-hover:scale-110 transition-transform">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </div>
                                <div class="ml-4 flex-1">
                                    <h4 class="text-base font-semibold text-gray-900 group-hover:text-pink-600">{{ __('messages.add_new_product') }}</h4>
                                    <p class="text-sm text-gray-600 mt-0.5">{{ __('messages.create_new_product_entry') }}</p>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-pink-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Bookings -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-purple-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('messages.recent_bookings') }}</h3>
                </div>
                <div class="p-4">
                    @if(isset($stats['recent_bookings']) && $stats['recent_bookings']->count() > 0)
                        <div class="space-y-3">
                            @foreach($stats['recent_bookings'] as $booking)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $booking->customer->name }}</p>
                                        <p class="text-xs text-gray-500 mt-1">{{ $booking->service->name }}</p>
                                    </div>
                                    <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full
                                        @if($booking->status === 'confirmed') bg-green-100 text-green-800
                                        @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        @if($booking->status === 'confirmed')
                                            {{ __('messages.confirmed') }}
                                        @elseif($booking->status === 'pending')
                                            {{ __('messages.pending') }}
                                        @else
                                            {{ __('messages.cancelled') }}
                                        @endif
                                    </span>
                                </div>
                            @endforeach
                        </div>
                        <a href="{{ route('admin.bookings.index') }}" class="block mt-4 text-center text-sm font-medium text-indigo-600 hover:text-indigo-800">
                            {{ __('messages.view_all_bookings') }} →
                        </a>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">{{ __('messages.no_recent_bookings') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Admin Users Section -->
        <div class="mt-8">
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white">{{ __('messages.admin_users') }}</h3>
                        <span class="px-3 py-1 text-sm font-medium bg-white/20 text-white rounded-full">
                            {{ $stats['admin_users']->count() }} {{ __('messages.admins') }}
                        </span>
                    </div>
                </div>
                <div class="p-6">
                    @if($stats['admin_users']->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.name') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.email') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.created_at') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.status') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($stats['admin_users'] as $admin)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center shadow-md">
                                                        <span class="text-white font-semibold text-sm">{{ strtoupper(substr($admin->name, 0, 1)) }}</span>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">{{ $admin->name }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $admin->email }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-500">{{ $admin->created_at->format('M d, Y') }}</div>
                                                <div class="text-xs text-gray-400">{{ $admin->created_at->format('h:i A') }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    {{ __('messages.admin') }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <p class="mt-4 text-sm font-medium text-gray-900">{{ __('messages.no_admin_users_found') }}</p>
                            <p class="mt-2 text-sm text-gray-500">{{ __('messages.create_admin_user_command') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>