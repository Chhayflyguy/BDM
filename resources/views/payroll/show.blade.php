<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">{{ $employee->name }} - {{ __('messages.payroll_details') }}</h2>
                <p class="text-sm text-gray-600 mt-1">{{ __('messages.viewing_transactions_for') }} {{ $months[$currentMonth] }} {{ $currentYear }}</p>
            </div>
            <a href="{{ route('payroll.index', ['month' => $currentMonth, 'year' => $currentYear]) }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium rounded-lg transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('messages.back_to_payroll') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-md">
                    <div class="flex">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <p class="ml-3 text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-indigo-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">{{ __('messages.total_services') }}</p>
                            <p class="text-3xl font-bold mt-2 text-gray-900">{{ $customerLogs->count() }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">{{ __('messages.total_commission') }}</p>
                            <p class="text-3xl font-bold mt-2 text-green-600">${{ number_format($totalCommission, 2) }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">{{ __('messages.zero_commission_logs') }}</p>
                            <p class="text-3xl font-bold mt-2 text-yellow-600">{{ $zeroCommissionCount }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transactions Table -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('messages.service_history') }}</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.date') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.customer') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.masseuse') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.service_price') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.commission') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.notes') }}</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($customerLogs as $log)
                                <tr class="hover:bg-gray-50 transition duration-150 {{ $log->employee_commission == 0 ? 'bg-yellow-50' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $log->completed_at->format('M d, Y') }}
                                        <div class="text-xs text-gray-500">{{ $log->completed_at->format('h:i A') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $log->customer->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $log->masseuse_name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-right">
                                        ${{ number_format($log->massage_price, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-right {{ $log->employee_commission == 0 ? 'text-red-600' : 'text-green-600' }}">
                                        ${{ number_format($log->employee_commission, 2) }}
                                        @if($log->employee_commission == 0)
                                            <span class="block text-xs text-red-500 font-normal">({{ __('messages.free') }})</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
                                        {{ $log->notes }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <button 
                                            onclick="openEditModal('{{ $log->id }}', '{{ $log->employee_commission }}', '{{ addslashes($log->notes) }}')"
                                            class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded-md transition">
                                            {{ __('messages.edit') }}
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                        {{ __('messages.no_services_found') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Commission Modal -->
    <div id="editModal" class="fixed inset-0 z-50 hidden">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/50" onclick="closeModal()"></div>
        
        <!-- Centered Modal Box -->
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl" style="width: 280px;">
                <!-- Header -->
                <div class="flex items-center justify-between px-5 py-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('messages.edit_commission') }}</h3>
                    <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <!-- Form -->
                <form id="editForm" method="POST" action="">
                    @csrf
                    @method('PATCH')
                    <div class="px-5 py-4 space-y-4">
                        <!-- Commission Amount -->
                        <div>
                            <label for="employee_commission" class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.commission_amount') }}</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                                <input type="number" name="employee_commission" id="employee_commission" step="0.01" min="0" required
                                       class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                       placeholder="0.00">
                            </div>
                        </div>
                        
                        <!-- Notes -->
                        <div>
                            <label for="modal_notes" class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.notes') }}</label>
                            <textarea id="modal_notes" name="notes" rows="3" 
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                      placeholder="{{ __('messages.add_reason_adjustment') }}"></textarea>
                        </div>
                    </div>
                    
                    <!-- Footer -->
                    <div class="flex justify-end gap-3 px-5 py-4 border-t bg-gray-50 rounded-b-lg">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            {{ __('messages.cancel') }}
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                            {{ __('messages.update') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openEditModal(id, commission, notes) {
            document.getElementById('editForm').action = `/payroll/customer-log/${id}/commission`;
            document.getElementById('employee_commission').value = commission;
            document.getElementById('modal_notes').value = '';
            document.getElementById('editModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('editModal').classList.add('hidden');
            document.body.style.overflow = '';
        }
        
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeModal();
        });
    </script>
</x-app-layout>

