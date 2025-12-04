<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Booking extends Model
{
    use HasFactory;
    protected $fillable = ['customer_id', 'service_id', 'employee_id', 'booking_datetime', 'status', 'notes'];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'booking_products')
            ->withPivot('quantity', 'price_at_time')
            ->withTimestamps();
    }
}