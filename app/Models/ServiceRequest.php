<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ServiceRequest extends Model
{
    use LogsActivity;

    protected $fillable = [
        'request_number', 'student_id', 'provider_id', 'service_id',
        'title', 'description', 'urgency', 'status', 'files',
        'quoted_price', 'final_price', 'admin_notes', 'cancellation_reason',
        'accepted_at', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'files' => 'array',
            'quoted_price' => 'decimal:2',
            'final_price' => 'decimal:2',
            'accepted_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['status', 'provider_id', 'final_price'])->logOnlyDirty();
    }

    protected static function booted(): void
    {
        static::creating(function (ServiceRequest $request) {
            $request->request_number = 'REQ-' . strtoupper(uniqid());
        });
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function isPending(): bool { return $this->status === 'pending'; }
    public function isCompleted(): bool { return $this->status === 'completed'; }
    public function isDisputed(): bool { return $this->status === 'disputed'; }
}
