<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Subscription extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'plan_id',
        'reference',
        'status',
        'amount_fcfa',
        'checkins_remaining',
        'starts_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'amount_fcfa'        => 'integer',
            'checkins_remaining' => 'integer',
            'starts_at'          => 'datetime',
            'expires_at'         => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function checkins(): HasMany
    {
        return $this->hasMany(GymCheckin::class);
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active')
            ->whereDate('expires_at', '>=', today());
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    // Helpers
    public function isActive(): bool
    {
        return $this->status === 'active' && $this->expires_at?->isFuture();
    }

    public function isUnlimited(): bool
    {
        return $this->checkins_remaining === null;
    }

    public function hasCheckinsLeft(): bool
    {
        return $this->isUnlimited() || $this->checkins_remaining > 0;
    }

    public function daysRemaining(): int
    {
        if ($this->expires_at === null) {
            return 0;
        }

        return max(0, (int) now()->diffInDays($this->expires_at, false));
    }

    public function isExpiringSoon(): bool
    {
        return $this->isActive() && $this->daysRemaining() <= 7;
    }
}
