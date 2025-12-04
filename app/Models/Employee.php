<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany; 

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_gid',
        'name',
        'phone',
        'gender',
        'email',
        'address', // NEW
        'experience',
        'working_status',
        'profile_image',
    ];

    /**
     * Get the full URL for the profile image
     */
    public function getProfileImageUrlAttribute(): ?string
    {
        if ($this->profile_image) {
            return asset('storage/' . $this->profile_image);
        }
        return null;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function completedLogs(): HasMany
    {
        return $this->hasMany(CustomerLog::class);
    }
}