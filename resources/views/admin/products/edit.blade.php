<x-app-layout>
    <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.edit_product') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4">
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    {{ __('messages.back_to_admin_dashboard') }}
                </a>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                        <div>
                            <x-input-label for="name" :value="__('messages.product_name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $product->name)" required autofocus />
                        </div>
                        <div class="mt-4">
                            <x-input-label for="price" :value="__('messages.price')" />
                            <x-text-input id="price" class="block mt-1 w-full" type="number" step="0.01" name="price" :value="old('price', $product->price)" required />
                        </div>
                        <div class="mt-4">
                            <x-input-label for="description" :value="__('messages.description')" />
                            <textarea id="description" name="description" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('description', $product->description) }}</textarea>
                        </div>
                        <div class="mt-4">
                            <x-input-label for="quantity" :value="__('messages.stock_quantity')" />
                            <x-text-input id="quantity" class="block mt-1 w-full" type="number" min="0" name="quantity" :value="old('quantity', $product->quantity ?? 0)" />
                            <p class="mt-1 text-sm text-gray-500">{{ __('messages.current_stock') }}: {{ number_format($product->quantity ?? 0) }} {{ __('messages.units') }}</p>
                        </div>
                        <div class="mt-4">
                            <x-input-label for="image" :value="__('messages.change_product_image_optional')" />
                            <input id="image" name="image" type="file" accept="image/*" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                            <p class="mt-1 text-sm text-gray-500">{{ __('messages.upload_new_image_max_2mb') }}</p>
                            <x-input-error :messages="$errors->get('image')" class="mt-2" />
                            @if($product->image)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="h-20 w-20 rounded-md object-cover border border-gray-200">
                                    <p class="text-xs text-gray-500 mt-1">{{ __('messages.current_image') }}</p>
                                </div>
                            @endif
                        </div>
                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">{{ __('messages.cancel') }}</a>
                            <x-primary-button>{{ __('messages.update_product') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>