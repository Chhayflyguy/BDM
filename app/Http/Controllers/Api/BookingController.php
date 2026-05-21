<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Booking;
use App\Models\Service;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;
use App\Notifications\NewBookingCreated;

class BookingController extends Controller
{
    /**
     * Display a listing of the authenticated customer's bookings.
     * Replaces the old ?phone= query param approach.
     */
    public function index(Request $request)
    {
        /** @var Customer $customer */
        $customer = $request->user();

        $bookings = Booking::where('customer_id', $customer->id)
            ->with(['service:id,name,price', 'products:id,name,price', 'employee'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($booking) {
                return [
                    'id'               => $booking->id,
                    'customer_name'    => $booking->customer->name ?? '',
                    'customer_phone'   => $booking->customer->phone ?? '',
                    'service_name'     => $booking->service->name,
                    'service_price'    => $booking->service->price,
                    'service_id'       => $booking->service_id,
                    'employee'         => $booking->employee ? [
                        'id'                => $booking->employee->id,
                        'name'              => $booking->employee->name,
                        'phone'             => $booking->employee->phone,
                        'profile_image_url' => $booking->employee->profile_image_url,
                    ] : null,
                    'booking_datetime' => $booking->booking_datetime,
                    'status'           => $booking->status,
                    'notes'            => $booking->notes,
                    'products'         => $booking->products->map(function ($product) {
                        return [
                            'id'           => $product->id,
                            'name'         => $product->name,
                            'quantity'     => $product->pivot->quantity,
                            'price_at_time'=> $product->pivot->price_at_time,
                        ];
                    }),
                    'created_at' => $booking->created_at,
                ];
            });

        return response()->json([
            'message'  => 'Bookings retrieved successfully.',
            'bookings' => $bookings,
        ], 200);
    }

    /**
     * Display a specific booking (must belong to the authenticated customer).
     */
    public function show(Request $request, Booking $booking)
    {
        /** @var Customer $customer */
        $customer = $request->user();

        $booking->load(['customer:id,name,phone', 'service:id,name,price', 'products:id,name,price', 'employee']);

        // Authorization: booking must belong to this customer
        if ($booking->customer_id !== $customer->id) {
            return response()->json([
                'message' => 'Unauthorized. This booking does not belong to you.',
            ], 403);
        }

        return response()->json([
            'message' => 'Booking retrieved successfully.',
            'booking' => [
                'id'               => $booking->id,
                'customer_name'    => $booking->customer->name,
                'customer_phone'   => $booking->customer->phone,
                'service'          => [
                    'id'    => $booking->service->id,
                    'name'  => $booking->service->name,
                    'price' => $booking->service->price,
                ],
                'employee'         => $booking->employee ? [
                    'id'                => $booking->employee->id,
                    'name'              => $booking->employee->name,
                    'phone'             => $booking->employee->phone,
                    'profile_image_url' => $booking->employee->profile_image_url,
                ] : null,
                'products'         => $booking->products->map(function ($product) {
                    return [
                        'id'            => $product->id,
                        'name'          => $product->name,
                        'quantity'      => $product->pivot->quantity,
                        'price_at_time' => $product->pivot->price_at_time,
                    ];
                }),
                'booking_datetime' => $booking->booking_datetime,
                'status'           => $booking->status,
                'notes'            => $booking->notes,
                'created_at'       => $booking->created_at,
                'updated_at'       => $booking->updated_at,
            ],
        ], 200);
    }

    /**
     * Store a newly created booking.
     * Customer identity comes from the auth token — no longer from request body.
     */
    public function store(Request $request)
    {
        /** @var Customer $customer */
        $customer = $request->user();

        $validated = $request->validate([
            'service_id'              => 'required|exists:services,id',
            'employee_id'             => 'nullable|exists:employees,id',
            'booking_datetime'        => 'required|date|after:now',
            'notes'                   => 'nullable|string',
            'products'                => 'nullable|array',
            'products.*.product_id'   => 'required|exists:products,id',
            'products.*.quantity'     => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $booking = Booking::create([
                'customer_id'      => $customer->id,
                'service_id'       => $validated['service_id'],
                'employee_id'      => $validated['employee_id'] ?? null,
                'booking_datetime' => $validated['booking_datetime'],
                'notes'            => $validated['notes'] ?? null,
            ]);

            // Handle products if provided
            if (!empty($validated['products'])) {
                foreach ($validated['products'] as $productData) {
                    $product  = Product::findOrFail($productData['product_id']);
                    $quantity = $productData['quantity'];

                    if ($product->quantity < $quantity) {
                        DB::rollBack();
                        return response()->json([
                            'message' => "Insufficient stock for product: {$product->name}. Available: {$product->quantity}, Requested: {$quantity}",
                        ], 400);
                    }

                    $booking->products()->attach($product->id, [
                        'quantity'       => $quantity,
                        'price_at_time'  => $product->price,
                    ]);

                    $product->quantity -= $quantity;
                    $product->save();
                }
            }

            DB::commit();

            $booking->load(['service:id,name,price', 'products:id,name,price']);

            // Push Telegram notification
            try {
                Notification::route('telegram', env('TELEGRAM_CHAT_ID'))
                    ->notify(new NewBookingCreated($booking));
            } catch (\Throwable $e) {
                // Silently ignore notification failures
            }

            return response()->json([
                'message' => 'Booking created successfully.',
                'booking' => [
                    'id'               => $booking->id,
                    'status'           => $booking->status,
                    'booking_datetime' => $booking->booking_datetime,
                    'service_name'     => $booking->service->name,
                    'products'         => $booking->products->map(function ($product) {
                        return [
                            'id'            => $product->id,
                            'name'          => $product->name,
                            'quantity'      => $product->pivot->quantity,
                            'price_at_time' => $product->pivot->price_at_time,
                        ];
                    }),
                ],
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create booking: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update a booking's date/time/notes (must belong to authenticated customer).
     */
    public function update(Request $request, Booking $booking)
    {
        /** @var Customer $customer */
        $customer = $request->user();

        // Authorization check
        if ($booking->customer_id !== $customer->id) {
            return response()->json([
                'message' => 'Unauthorized. This booking does not belong to you.',
            ], 403);
        }

        $validated = $request->validate([
            'booking_datetime' => 'required|date|after:now',
            'employee_id'      => 'nullable|exists:employees,id',
            'notes'            => 'nullable|string',
        ]);

        if ($booking->status === 'cancelled') {
            return response()->json([
                'message' => 'Cannot update a cancelled booking.',
            ], 400);
        }

        $booking->update([
            'booking_datetime' => $validated['booking_datetime'],
            'employee_id'      => $validated['employee_id'] ?? $booking->employee_id,
            'notes'            => $validated['notes'] ?? $booking->notes,
        ]);

        $booking->load('employee');

        return response()->json([
            'message' => 'Booking updated successfully.',
            'booking' => [
                'id'               => $booking->id,
                'status'           => $booking->status,
                'booking_datetime' => $booking->booking_datetime,
                'notes'            => $booking->notes,
                'employee'         => $booking->employee ? [
                    'id'                => $booking->employee->id,
                    'name'              => $booking->employee->name,
                    'phone'             => $booking->employee->phone,
                    'profile_image_url' => $booking->employee->profile_image_url,
                ] : null,
            ],
        ]);
    }

    /**
     * Cancel a booking (must belong to authenticated customer).
     */
    public function destroy(Request $request, Booking $booking)
    {
        /** @var Customer $customer */
        $customer = $request->user();

        // Authorization check
        if ($booking->customer_id !== $customer->id) {
            return response()->json([
                'message' => 'Unauthorized. This booking does not belong to you.',
            ], 403);
        }

        if ($booking->status === 'cancelled') {
            return response()->json([
                'message' => 'This booking is already cancelled.',
            ], 400);
        }

        DB::beginTransaction();
        try {
            $booking->load('products');

            // Restore stock for all products in this booking
            foreach ($booking->products as $product) {
                $product->quantity += $product->pivot->quantity;
                $product->save();
            }

            $booking->status = 'cancelled';
            $booking->save();

            DB::commit();

            return response()->json([
                'message' => 'Booking cancelled successfully.',
                'booking' => [
                    'id'     => $booking->id,
                    'status' => $booking->status,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to cancel booking: ' . $e->getMessage(),
            ], 500);
        }
    }
}