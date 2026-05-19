<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ApiKey extends Model
{
    protected $fillable = [
        'user_id', 'name', 'key', 'abilities', 'usage_count', 'last_used_at', 'expires_at', 'revoked_at',
    ];

    protected $hidden = ['key'];

    protected function casts(): array
    {
        return [
            'abilities' => 'array',
            'last_used_at' => 'datetime',
            'expires_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    public static function generate(User $user, string $name, array $abilities = ['*']): static
    {
        return static::create([
            'user_id' => $user->id,
            'name' => $name,
            'key' => Str::random(64),
            'abilities' => $abilities,
        ]);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isRevoked(): bool
    {
        return $this->revoked_at !== null;
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isActive(): bool
    {
        return !$this->isRevoked() && !$this->isExpired();
    }
}
