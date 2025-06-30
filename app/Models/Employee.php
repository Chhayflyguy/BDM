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
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function completedLogs(): HasMany
    {
        return $this->hasMany(CustomerLog::class);
    }
}