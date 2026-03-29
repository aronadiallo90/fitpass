<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Gym extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'owner_id',
        'name',
        'slug',
        'description',
        'address',
        'latitude',
        'longitude',
        'activities',
        'opening_hours',
        'phone',
        'photo_url',
        'api_token',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'latitude'      => 'decimal:8',
            'longitude'     => 'decimal:8',
            'activities'    => 'array',
            'opening_hours' => 'array',
            'is_active'     => 'boolean',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (Gym $gym) {
            $gym->api_token = (string) Str::uuid();
            if (empty($gym->slug)) {
                $gym->slug = Str::slug($gym->name);
            }
        });
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function checkins(): HasMany
    {
        return $this->hasMany(GymCheckin::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function todayCheckins(): HasMany
    {
        return $this->hasMany(GymCheckin::class)
            ->whereDate('created_at', today());
    }
}
