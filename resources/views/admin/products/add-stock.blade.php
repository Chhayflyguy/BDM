<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add Stock to Product') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6 pb-6 border-b border-gray-200">
                        <div class="flex items-center space-x-4 mb-4">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="h-16 w-16 rounded-lg object-cover border border-gray-200">
                            @endif
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $product->name }}</h3>
                                <div class="flex items-center space-x-4 text-sm text-gray-600">
                                    <span><strong>{{ __('Current Price:') }}</strong> ${{ number_format($product->price, 2) }}</span>
                                    <span><strong>{{ __('Current Stock:') }}</strong> 
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $product->quantity > 10 ? 'bg-green-100 text-green-800' : ($product->quantity > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ number_format($product->quantity ?? 0) }} {{ __('units') }}
                                        </span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.products.add-stock', $product) }}">
                        @csrf
                        <div>
                            <x-input-label for="quantity" :value="__('Quantity to Add')" />
                            <x-text-input id="quantity" class="block mt-1 w-full" type="number" min="1" name="quantity" :value="old('quantity')" required autofocus />
                            <p class="mt-1 text-sm text-gray-500">{{ __('Enter the number of units you want to add to the stock') }}</p>
                            @error('quantity')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">{{ __('Cancel') }}</a>
                            <x-primary-button>{{ __('Add Stock') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

