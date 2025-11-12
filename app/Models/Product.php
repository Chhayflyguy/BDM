<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'price', 'description', 'is_active', 'quantity', 'image'];

    public function bookings(): BelongsToMany
    {
        return $this->belongsToMany(Booking::class, 'booking_products')
            ->withPivot('quantity', 'price_at_time')
            ->withTimestamps();
    }
}
