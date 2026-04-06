<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasUuids, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'password',
        'role',
        'qr_token',
        'is_active',
        'profile_photo_path',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
    ];

    protected $appends = ['profile_photo_url', 'initials'];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'password'                   => 'hashed',
            'is_active'                  => 'boolean',
            'two_factor_recovery_codes'  => 'array',
            'two_factor_confirmed_at'    => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (User $user) {
            // HasUuids gère $user->id automatiquement
            $user->qr_token = (string) Str::uuid();
        });
    }

    // Accessors photo profil
    public function getProfilePhotoUrlAttribute(): string
    {
        if ($this->profile_photo_path) {
            return Storage::url($this->profile_photo_path);
        }

        // Avatar initiales — pas de photo = URL générée côté front (composant Blade)
        return '';
    }

    public function getInitialsAttribute(): string
    {
        $words = explode(' ', trim($this->name));
        $initials = '';
        foreach (array_slice($words, 0, 2) as $word) {
            $initials .= mb_strtoupper(mb_substr($word, 0, 1));
        }
        return $initials ?: '?';
    }

    // Relations
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 'active')
            ->latest();
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function checkins(): HasMany
    {
        return $this->hasMany(GymCheckin::class);
    }

    public function gyms(): HasMany
    {
        return $this->hasMany(Gym::class, 'owner_id');
    }

    public function gym(): HasOne
    {
        return $this->hasOne(Gym::class, 'owner_id');
    }

    public function latestCheckin(): HasOne
    {
        return $this->hasOne(GymCheckin::class)->latestOfMany();
    }

    public function smsLogs(): HasMany
    {
        return $this->hasMany(SmsLog::class);
    }

    // Helpers rôles
    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'super_admin']);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isGymOwner(): bool
    {
        return $this->role === 'gym_owner';
    }

    public function isMember(): bool
    {
        return $this->role === 'member';
    }

    public function hasTwoFactorEnabled(): bool
    {
        return $this->two_factor_confirmed_at !== null;
    }
}
