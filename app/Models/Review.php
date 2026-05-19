<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Review extends Model
{
    use LogsActivity;

    protected $fillable = [
        'service_request_id', 'student_id', 'provider_id',
        'rating', 'comment', 'is_flagged', 'flagged_reason',
    ];

    protected function casts(): array
    {
        return ['is_flagged' => 'boolean'];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }

    protected static function booted(): void
    {
        static::saved(function (Review $review) {
            $review->provider->update([
                'rating_avg' => Review::where('provider_id', $review->provider_id)->avg('rating'),
                'total_reviews' => Review::where('provider_id', $review->provider_id)->count(),
            ]);
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

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }
}
