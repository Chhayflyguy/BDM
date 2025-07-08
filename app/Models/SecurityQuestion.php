<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Casts\Attribute;

class SecurityQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'question_1',
        'answer_1',
        'question_2',
        'answer_2',
    ];

    protected function answer1(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => Hash::make(strtolower(trim($value))),
        );
    }

    protected function answer2(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => Hash::make(strtolower(trim($value))),
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
