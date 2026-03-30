<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        'zone',
        'latitude',
        'longitude',
        'activities',     // JSON legacy — utilisé par la landing page
        'opening_hours',
        'phone',
        'phone_whatsapp',
        'photo_url',
        'api_token',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'latitude'      => 'decimal:8',
            'longitude'     => 'decimal:8',
            'activities'    => 'array',  // JSON legacy — landing page
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

    public function activities(): BelongsToMany
    {
        return $this->belongsToMany(GymActivity::class, 'gym_activity');
    }

    public function programs(): HasMany
    {
        return $this->hasMany(GymProgram::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(GymPhoto::class)->orderBy('display_order');
    }

    public function coverPhoto(): HasMany
    {
        return $this->hasMany(GymPhoto::class)->where('is_cover', true)->limit(1);
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
