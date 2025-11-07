<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Product') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                        <div>
                            <x-input-label for="name" :value="__('Product Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $product->name)" required autofocus />
                        </div>
                        <div class="mt-4">
                            <x-input-label for="price" :value="__('Price')" />
                            <x-text-input id="price" class="block mt-1 w-full" type="number" step="0.01" name="price" :value="old('price', $product->price)" required />
                        </div>
                        <div class="mt-4">
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('description', $product->description) }}</textarea>
                        </div>
                        <div class="mt-4">
                            <x-input-label for="quantity" :value="__('Stock Quantity')" />
                            <x-text-input id="quantity" class="block mt-1 w-full" type="number" min="0" name="quantity" :value="old('quantity', $product->quantity ?? 0)" />
                            <p class="mt-1 text-sm text-gray-500">{{ __('Current stock:') }} {{ number_format($product->quantity ?? 0) }} {{ __('units') }}</p>
                        </div>
                        <div class="mt-4">
                            <x-input-label for="image" :value="__('Change Product Image (optional)')" />
                            <input id="image" name="image" type="file" accept="image/*" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                            <p class="mt-1 text-sm text-gray-500">{{ __('Upload a new image (max 2MB). Accepted formats: JPEG, PNG, JPG, GIF, WebP') }}</p>
                            <x-input-error :messages="$errors->get('image')" class="mt-2" />
                            @if($product->image)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="h-20 w-20 rounded-md object-cover border border-gray-200">
                                    <p class="text-xs text-gray-500 mt-1">{{ __('Current Image') }}</p>
                                </div>
                            @endif
                        </div>
                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                            <x-primary-button>Update Product</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>