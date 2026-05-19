<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Payment extends Model
{
    use LogsActivity;

    protected $fillable = [
        'reference', 'service_request_id', 'student_id',
        'amount', 'method', 'status', 'meta', 'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'meta' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['status', 'amount'])->logOnlyDirty();
    }

    protected static function booted(): void
    {
        static::creating(function (Payment $payment) {
            $payment->reference = 'PAY-' . strtoupper(uniqid());
        });
    }

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }
}
