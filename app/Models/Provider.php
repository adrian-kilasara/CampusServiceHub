<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Provider extends Model
{
    use LogsActivity;

    protected $fillable = [
        'user_id', 'business_name', 'bio', 'location', 'whatsapp',
        'documents', 'status', 'rejection_reason', 'rating_avg',
        'total_reviews', 'completed_jobs', 'wallet_balance', 'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'documents' => 'array',
            'rating_avg' => 'decimal:2',
            'wallet_balance' => 'decimal:2',
            'verified_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'provider_services')
            ->withPivot('custom_price', 'notes')
            ->withTimestamps();
    }

    public function serviceRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
