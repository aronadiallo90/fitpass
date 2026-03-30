<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'slug',
        'price_fcfa',
        'duration_days',
        'checkins_limit',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price_fcfa'     => 'integer',
            'duration_days'  => 'integer',
            'checkins_limit' => 'integer',
            'is_active'      => 'boolean',
            'sort_order'     => 'integer',
        ];
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'plan_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    // Accessors pour simplifier les vues
    public function getPriceAttribute(): int
    {
        return $this->price_fcfa;
    }

    public function getTypeAttribute(): string
    {
        return $this->checkins_limit !== null ? 'decouverte' : 'unlimited';
    }

    public function isDiscovery(): bool
    {
        return $this->checkins_limit !== null;
    }

    public function isUnlimited(): bool
    {
        return $this->checkins_limit === null;
    }

    public function formattedPrice(): string
    {
        return number_format($this->price_fcfa, 0, ',', ' ') . ' FCFA';
    }
}
