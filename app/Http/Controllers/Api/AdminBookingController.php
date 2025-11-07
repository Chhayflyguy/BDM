<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

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

        $query = Booking::with(['customer:id,name,phone', 'service:id,name,price']);

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
        $bookings = $query->orderBy('booking_datetime', 'desc')
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

        $booking->load(['customer:id,name,phone', 'service:id,name,price']);

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

        $booking->update($validated);

        $booking->load(['customer:id,name,phone', 'service:id,name,price']);

        return response()->json([
            'message' => 'Booking status updated successfully.',
            'booking' => [
                'id' => $booking->id,
                'status' => $booking->status,
                'customer_name' => $booking->customer->name,
                'service_name' => $booking->service->name,
                'booking_datetime' => $booking->booking_datetime,
            ]
        ], 200);
    }
}

