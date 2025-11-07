<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::where('is_active', true)->get();
        return ProductResource::collection($products);
    }

    public function purchase(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        
        // Check if product is active
        if (!$product->is_active) {
            return response()->json([
                'message' => 'Product is not available.',
            ], 400);
        }

        // Check if product has enough stock
        if (($product->quantity ?? 0) < $validated['quantity']) {
            return response()->json([
                'message' => 'Insufficient stock. Available: ' . ($product->quantity ?? 0),
                'available_quantity' => $product->quantity ?? 0,
            ], 400);
        }

        // Decrease quantity
        $product->decrement('quantity', $validated['quantity']);

        return response()->json([
            'message' => 'Product purchased successfully.',
            'product' => new ProductResource($product->fresh()),
            'purchased_quantity' => $validated['quantity'],
            'remaining_stock' => $product->fresh()->quantity ?? 0,
        ], 200);
    }
}
