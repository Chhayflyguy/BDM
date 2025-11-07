<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Booking;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewBookingCreated;

class BookingController extends Controller
{
    /**
     * Display a listing of bookings for a customer.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string|max:50',
        ]);

        $customer = Customer::where('phone', $validated['phone'])->first();

        if (!$customer) {
            return response()->json([
                'message' => 'No bookings found for this phone number.',
                'bookings' => []
            ], 200);
        }

        $bookings = Booking::where('customer_id', $customer->id)
            ->with(['service:id,name,price'])
            ->orderBy('booking_datetime', 'desc')
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'service_name' => $booking->service->name,
                    'service_price' => $booking->service->price,
                    'booking_datetime' => $booking->booking_datetime,
                    'status' => $booking->status,
                    'notes' => $booking->notes,
                    'created_at' => $booking->created_at,
                ];
            });

        return response()->json([
            'message' => 'Bookings retrieved successfully.',
            'bookings' => $bookings
        ], 200);
    }

    /**
     * Display the specified booking.
     */
    public function show(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'phone' => 'required|string|max:50',
        ]);

        // Load relationships
        $booking->load(['customer:id,name,phone', 'service:id,name,price']);

        // Verify that the booking belongs to the customer with this phone number
        if ($booking->customer->phone !== $validated['phone']) {
            return response()->json([
                'message' => 'Unauthorized. This booking does not belong to you.'
            ], 403);
        }

        return response()->json([
            'message' => 'Booking retrieved successfully.',
            'booking' => [
                'id' => $booking->id,
                'customer_name' => $booking->customer->name,
                'customer_phone' => $booking->customer->phone,
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
     * Store a newly created booking in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:50',
            'service_id' => 'required|exists:services,id',
            'booking_datetime' => 'required|date|after:now',
            'notes' => 'nullable|string',
        ]);

        // Find existing customer by phone or create a new one
        $customer = Customer::firstOrCreate(
            ['phone' => $validated['customer_phone']],
            [
                'name' => $validated['customer_name'],
                'user_id' => null, // API-created customers don't have a user_id
                'customer_gid' => $this->generateCustomerGid(),
            ]
        );

        // If customer exists but name is empty or different, update it
        if ($customer->name !== $validated['customer_name']) {
            $customer->name = $validated['customer_name'];
            $customer->save();
        }

        $booking = Booking::create([
            'customer_id' => $customer->id,
            'service_id' => $validated['service_id'],
            'booking_datetime' => $validated['booking_datetime'],
            'notes' => $validated['notes'] ?? null,
        ]);

        // Load relationships for response
        $booking->load(['service:id,name,price']);

        // Push Telegram notification
        try {
            Notification::route('telegram', env('TELEGRAM_CHAT_ID'))
                ->notify(new NewBookingCreated($booking));
        } catch (\Throwable $e) {
            // Silently ignore notification failures to not block booking creation
        }

        return response()->json([
            'message' => 'Booking created successfully.',
            'booking' => [
                'id' => $booking->id,
                'status' => $booking->status,
                'booking_datetime' => $booking->booking_datetime,
                'service_name' => $booking->service->name,
            ]
        ], 201);
    }

    /**
     * Update the specified booking in storage.
     */
    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'phone' => 'required|string|max:50',
            'booking_datetime' => 'required|date|after:now',
            'notes' => 'nullable|string',
        ]);

        // Load customer relationship
        $booking->load('customer:id,phone');

        // Verify that the booking belongs to the customer with this phone number
        if ($booking->customer->phone !== $validated['phone']) {
            return response()->json([
                'message' => 'Unauthorized. This booking does not belong to you.'
            ], 403);
        }

        // Check if booking can be updated (not cancelled)
        if ($booking->status === 'cancelled') {
            return response()->json([
                'message' => 'Cannot update a cancelled booking.'
            ], 400);
        }

        $booking->update([
            'booking_datetime' => $validated['booking_datetime'],
            'notes' => $validated['notes'] ?? $booking->notes,
        ]);

        return response()->json([
            'message' => 'Booking updated successfully.',
            'booking' => [
                'id' => $booking->id,
                'status' => $booking->status,
                'booking_datetime' => $booking->booking_datetime,
                'notes' => $booking->notes,
            ]
        ]);
    }

    /**
     * Cancel the specified booking.
     */
    public function destroy(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'phone' => 'required|string|max:50',
        ]);

        // Load customer relationship
        $booking->load('customer:id,phone');

        // Verify that the booking belongs to the customer with this phone number
        if ($booking->customer->phone !== $validated['phone']) {
            return response()->json([
                'message' => 'Unauthorized. This booking does not belong to you.'
            ], 403);
        }

        // Check if booking is already cancelled
        if ($booking->status === 'cancelled') {
            return response()->json([
                'message' => 'This booking is already cancelled.'
            ], 400);
        }

        // Instead of deleting, we change the status. This is better for record-keeping.
        $booking->status = 'cancelled';
        $booking->save();

        return response()->json([
            'message' => 'Booking cancelled successfully.',
            'booking' => [
                'id' => $booking->id,
                'status' => $booking->status,
            ]
        ]);
    }

    /**
     * Generate a unique customer GID
     */
    private function generateCustomerGid(): string
    {
        do {
            $customerGid = (string) random_int(100000, 999999);
        } while (Customer::where('customer_gid', $customerGid)->exists());

        return $customerGid;
    }
}