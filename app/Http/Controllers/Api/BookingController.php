<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Booking;
use App\Models\Service;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;
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
            ->with(['service:id,name,price', 'products:id,name,price', 'employee'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'service_name' => $booking->service->name,
                    'service_price' => $booking->service->price,
                    'employee' => $booking->employee ? [
                        'id' => $booking->employee->id,
                        'name' => $booking->employee->name,
                        'phone' => $booking->employee->phone,
                        'profile_image_url' => $booking->employee->profile_image_url,
                    ] : null,
                    'booking_datetime' => $booking->booking_datetime,
                    'status' => $booking->status,
                    'notes' => $booking->notes,
                    'products' => $booking->products->map(function ($product) {
                        return [
                            'id' => $product->id,
                            'name' => $product->name,
                            'quantity' => $product->pivot->quantity,
                            'price_at_time' => $product->pivot->price_at_time,
                        ];
                    }),
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
        $booking->load(['customer:id,name,phone', 'service:id,name,price', 'products:id,name,price', 'employee']);

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
                'employee' => $booking->employee ? [
                    'id' => $booking->employee->id,
                    'name' => $booking->employee->name,
                    'phone' => $booking->employee->phone,
                    'profile_image_url' => $booking->employee->profile_image_url,
                ] : null,
                'products' => $booking->products->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'quantity' => $product->pivot->quantity,
                        'price_at_time' => $product->pivot->price_at_time,
                    ];
                }),
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
            'employee_id' => 'nullable|exists:employees,id',
            'booking_datetime' => 'required|date|after:now',
            'notes' => 'nullable|string',
            'products' => 'nullable|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
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

        // Use database transaction to ensure data consistency
        DB::beginTransaction();
        try {
            $booking = Booking::create([
                'customer_id' => $customer->id,
                'service_id' => $validated['service_id'],
                'employee_id' => $validated['employee_id'] ?? null,
                'booking_datetime' => $validated['booking_datetime'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Handle products if provided
            if (!empty($validated['products'])) {
                foreach ($validated['products'] as $productData) {
                    $product = Product::findOrFail($productData['product_id']);
                    $quantity = $productData['quantity'];

                    // Check if enough stock is available
                    if ($product->quantity < $quantity) {
                        DB::rollBack();
                        return response()->json([
                            'message' => "Insufficient stock for product: {$product->name}. Available: {$product->quantity}, Requested: {$quantity}",
                        ], 400);
                    }

                    // Attach product to booking with quantity and price
                    $booking->products()->attach($product->id, [
                        'quantity' => $quantity,
                        'price_at_time' => $product->price,
                    ]);

                    // Decrease stock
                    $product->quantity -= $quantity;
                    $product->save();
                }
            }

            DB::commit();

            // Load relationships for response
            $booking->load(['service:id,name,price', 'products:id,name,price']);

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
                    'products' => $booking->products->map(function ($product) {
                        return [
                            'id' => $product->id,
                            'name' => $product->name,
                            'quantity' => $product->pivot->quantity,
                            'price_at_time' => $product->pivot->price_at_time,
                        ];
                    }),
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create booking: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified booking in storage.
     */
    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'phone' => 'required|string|max:50',
            'booking_datetime' => 'required|date|after:now',
            'employee_id' => 'nullable|exists:employees,id',
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
            'employee_id' => $validated['employee_id'] ?? $booking->employee_id,
            'notes' => $validated['notes'] ?? $booking->notes,
        ]);

        // Reload with employee relationship
        $booking->load('employee');

        return response()->json([
            'message' => 'Booking updated successfully.',
            'booking' => [
                'id' => $booking->id,
                'status' => $booking->status,
                'booking_datetime' => $booking->booking_datetime,
                'notes' => $booking->notes,
                'employee' => $booking->employee ? [
                    'id' => $booking->employee->id,
                    'name' => $booking->employee->name,
                    'phone' => $booking->employee->phone,
                    'profile_image_url' => $booking->employee->profile_image_url,
                ] : null,
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

        // Use database transaction to ensure data consistency
        DB::beginTransaction();
        try {
            // Load products before cancelling
            $booking->load('products');

            // Restore stock for all products in this booking
            foreach ($booking->products as $product) {
                $quantity = $product->pivot->quantity;
                $product->quantity += $quantity;
                $product->save();
            }

            // Instead of deleting, we change the status. This is better for record-keeping.
            $booking->status = 'cancelled';
            $booking->save();

            DB::commit();

            return response()->json([
                'message' => 'Booking cancelled successfully.',
                'booking' => [
                    'id' => $booking->id,
                    'status' => $booking->status,
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to cancel booking: ' . $e->getMessage(),
            ], 500);
        }
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