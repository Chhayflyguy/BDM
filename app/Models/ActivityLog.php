<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'subject_type',
        'subject_id',
        'description',
        'ip_address',
        'user_agent',
    ];

    /**
     * The user who performed the action
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subject model (polymorphic relationship)
     */
    public function subject()
    {
        return $this->morphTo('subject', 'subject_type', 'subject_id');
    }

    /**
     * Get formatted action name
     */
    public function getActionNameAttribute(): string
    {
        return ucfirst($this->action);
    }

    /**
     * Get formatted subject type name
     */
    public function getSubjectTypeNameAttribute(): string
    {
        return class_basename($this->subject_type);
    }
}
