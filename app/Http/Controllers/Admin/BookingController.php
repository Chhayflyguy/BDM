<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['customer', 'service', 'products'])->latest()->paginate(15);
        return view('admin.bookings.index', compact('bookings'));
    }

    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled',
        ]);

        $oldStatus = $booking->status;
        $newStatus = $validated['status'];

        // Use database transaction to ensure data consistency
        DB::beginTransaction();
        try {
            // Load products before status change
            $booking->load('products');

            // Handle stock changes based on status transitions
            if ($oldStatus !== $newStatus) {
                // If changing to cancelled, restore stock
                if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                    foreach ($booking->products as $product) {
                        $quantity = $product->pivot->quantity;
                        $product->quantity += $quantity;
                        $product->save();
                    }
                }
                // If changing from cancelled to confirmed/pending, decrease stock
                elseif ($oldStatus === 'cancelled' && $newStatus !== 'cancelled') {
                    foreach ($booking->products as $product) {
                        $quantity = $product->pivot->quantity;
                        
                        // Check if enough stock is available
                        if ($product->quantity < $quantity) {
                            DB::rollBack();
                            return redirect()->route('admin.bookings.index')
                                ->with('error', "Insufficient stock for product: {$product->name}. Available: {$product->quantity}, Required: {$quantity}");
                        }
                        
                        $product->quantity -= $quantity;
                        $product->save();
                    }
                }
            }

            $booking->update($validated);

            DB::commit();

            return redirect()->route('admin.bookings.index')->with('success', 'Booking status updated.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.bookings.index')
                ->with('error', 'Failed to update booking status: ' . $e->getMessage());
        }
    }

    public function destroy(Booking $booking)
    {
        // Use database transaction to ensure data consistency
        DB::beginTransaction();
        try {
            // Load products before deleting
            $booking->load('products');

            // Restore stock for all products in this booking if not already cancelled
            if ($booking->status !== 'cancelled') {
                foreach ($booking->products as $product) {
                    $quantity = $product->pivot->quantity;
                    $product->quantity += $quantity;
                    $product->save();
                }
            }

            $booking->delete();

            DB::commit();

            return redirect()->route('admin.bookings.index')->with('success', 'Booking deleted.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.bookings.index')
                ->with('error', 'Failed to delete booking: ' . $e->getMessage());
        }
    }
}
