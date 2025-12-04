<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminBookingController extends Controller
{
    /**
     * Display a listing of all bookings for admin.
     */
    public function index(Request $request)
    {
        // Verify user is authenticated and is admin
        if (!$request->user() || !$request->user()->is_admin) {
            return response()->json([
                'message' => 'Unauthorized. Admin access required.'
            ], 403);
        }

        $query = Booking::with(['customer:id,name,phone', 'service:id,name,price', 'products:id,name,price', 'employee']);

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range if provided
        if ($request->has('date_from')) {
            $query->whereDate('booking_datetime', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('booking_datetime', '<=', $request->date_to);
        }

        // Search by customer name or phone
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('customer', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $bookings = $query->orderBy('created_at', 'desc')
            ->paginate($perPage);

        // Format bookings for response
        $formattedBookings = $bookings->map(function ($booking) {
            return [
                'id' => $booking->id,
                'customer' => [
                    'id' => $booking->customer->id,
                    'name' => $booking->customer->name,
                    'phone' => $booking->customer->phone,
                ],
                'service' => [
                    'id' => $booking->service->id,
                    'name' => $booking->service->name,
                    'price' => $booking->service->price,
                ],
                'products' => $booking->products->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'quantity' => $product->pivot->quantity,
                        'price_at_time' => $product->pivot->price_at_time,
                    ];
                }),
                'employee' => $booking->employee ? [
                    'id' => $booking->employee->id,
                    'name' => $booking->employee->name,
                    'phone' => $booking->employee->phone,
                    'profile_image_url' => $booking->employee->profile_image_url,
                ] : null,
                'booking_datetime' => $booking->booking_datetime,
                'status' => $booking->status,
                'notes' => $booking->notes,
                'created_at' => $booking->created_at,
                'updated_at' => $booking->updated_at,
            ];
        });

        return response()->json([
            'message' => 'Bookings retrieved successfully.',
            'bookings' => $formattedBookings,
            'pagination' => [
                'current_page' => $bookings->currentPage(),
                'last_page' => $bookings->lastPage(),
                'per_page' => $bookings->perPage(),
                'total' => $bookings->total(),
            ]
        ], 200);
    }

    /**
     * Display the specified booking for admin.
     */
    public function show(Request $request, Booking $booking)
    {
        // Verify user is authenticated and is admin
        if (!$request->user() || !$request->user()->is_admin) {
            return response()->json([
                'message' => 'Unauthorized. Admin access required.'
            ], 403);
        }

        $booking->load(['customer:id,name,phone', 'service:id,name,price', 'products:id,name,price', 'employee']);

        return response()->json([
            'message' => 'Booking retrieved successfully.',
            'booking' => [
                'id' => $booking->id,
                'customer' => [
                    'id' => $booking->customer->id,
                    'name' => $booking->customer->name,
                    'phone' => $booking->customer->phone,
                ],
                'service' => [
                    'id' => $booking->service->id,
                    'name' => $booking->service->name,
                    'price' => $booking->service->price,
                ],
                'products' => $booking->products->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'quantity' => $product->pivot->quantity,
                        'price_at_time' => $product->pivot->price_at_time,
                    ];
                }),
                'employee' => $booking->employee ? [
                    'id' => $booking->employee->id,
                    'name' => $booking->employee->name,
                    'phone' => $booking->employee->phone,
                    'profile_image_url' => $booking->employee->profile_image_url,
                ] : null,
                'booking_datetime' => $booking->booking_datetime,
                'status' => $booking->status,
                'notes' => $booking->notes,
                'created_at' => $booking->created_at,
                'updated_at' => $booking->updated_at,
            ]
        ], 200);
    }

    /**
     * Update the specified booking status (admin only).
     */
    public function update(Request $request, Booking $booking)
    {
        // Verify user is authenticated and is admin
        if (!$request->user() || !$request->user()->is_admin) {
            return response()->json([
                'message' => 'Unauthorized. Admin access required.'
            ], 403);
        }

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
                            return response()->json([
                                'message' => "Insufficient stock for product: {$product->name}. Available: {$product->quantity}, Required: {$quantity}",
                            ], 400);
                        }
                        
                        $product->quantity -= $quantity;
                        $product->save();
                    }
                }
            }

            $booking->update($validated);

            DB::commit();

            $booking->load(['customer:id,name,phone', 'service:id,name,price', 'products:id,name,price', 'employee']);

            return response()->json([
                'message' => 'Booking status updated successfully.',
                'booking' => [
                    'id' => $booking->id,
                    'status' => $booking->status,
                    'customer_name' => $booking->customer->name,
                    'service_name' => $booking->service->name,
                    'booking_datetime' => $booking->booking_datetime,
                    'products' => $booking->products->map(function ($product) {
                        return [
                            'id' => $product->id,
                            'name' => $product->name,
                            'quantity' => $product->pivot->quantity,
                            'price_at_time' => $product->pivot->price_at_time,
                        ];
                    }),
                    'employee' => $booking->employee ? [
                        'id' => $booking->employee->id,
                        'name' => $booking->employee->name,
                        'phone' => $booking->employee->phone,
                        'profile_image_url' => $booking->employee->profile_image_url,
                    ] : null,
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update booking status: ' . $e->getMessage(),
            ], 500);
        }
    }
}

