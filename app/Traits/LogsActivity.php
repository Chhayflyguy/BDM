<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    /**
     * Boot the trait
     */
    protected static function bootLogsActivity()
    {
        // Log when a model is created
        static::created(function ($model) {
            $model->logActivity('create', "Created {$model->getModelName()}");
        });

        // Log when a model is updated
        static::updated(function ($model) {
            $model->logActivity('update', "Updated {$model->getModelName()}");
        });

        // Log when a model is deleted
        static::deleted(function ($model) {
            $model->logActivity('delete', "Deleted {$model->getModelName()}");
        });
    }

    /**
     * Log an activity for this model
     */
    protected function logActivity(string $action, string $description): void
    {
        // Only log if user is authenticated
        if (!Auth::check()) {
            return;
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'subject_type' => get_class($this),
            'subject_id' => $this->id ?? null,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Get a human-readable model name
     */
    protected function getModelName(): string
    {
        $className = class_basename($this);
        
        // Add specific names for different models
        $names = [
            'Customer' => 'customer',
            'CustomerLog' => 'customer log',
            'Employee' => 'employee',
            'Product' => 'product',
            'Service' => 'service',
            'DailyExpense' => 'daily expense',
            'User' => 'user',
        ];

        return $names[$className] ?? strtolower($className);
    }
}
