<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\LogsActivity;

class Customer extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id',
        'customer_gid',
        'vip_card_id', 
        'vip_card_type', // NEW    
        'vip_card_balance', 
        'vip_card_expires_at',
        'next_booking_date', // NEW
        'booking_completed_at', // NEW
        'name',
        'phone',
        'gender',
        'age',
        'height',
        'weight',
        'health_conditions',
        'problem_areas',
    ];

    protected $casts = [
        'health_conditions' => 'array',
        'problem_areas' => 'array',
        'vip_card_balance' => 'decimal:2',
        'vip_card_expires_at' => 'date',
        'next_booking_date' => 'date', // This line fixes the error
        'booking_completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(CustomerLog::class);
    }
}

