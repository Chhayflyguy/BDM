<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerLog extends Model
{
    use HasFactory;

    /**
     * MODIFIED: Added customer_id and removed old fields
     */
    protected $fillable = [
        'user_id',
        'customer_id',
        'product_id', // NEW: For tracking product purchases
        'product_quantity', // NEW: Track quantity purchased
        'employee_id', 
        'payment_method', 
        'employee_commission', 
        'next_meeting',
        'product_purchased',
        'product_price',
        'masseuse_name', 
        'massage_price',
        'payment_amount',
        'notes',
        'status',
        'is_vip_top_up', // NEW
        'completed_at',
    ];

    /**
     * MODIFIED: Added casts for new price fields
     */
    protected $casts = [
        'next_meeting' => 'date',
        'completed_at' => 'datetime',
        'product_price' => 'decimal:2',
        'massage_price' => 'decimal:2',
        'payment_amount' => 'decimal:2',
        'employee_commission' => 'decimal:2', // NEW
        'is_vip_top_up' => 'boolean', // NEW
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * NEW: Define the relationship to the Customer model.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * NEW: Define the relationship to the Product model.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
