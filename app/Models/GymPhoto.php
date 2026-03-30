<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GymPhoto extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'gym_id',
        'photo_url',
        'photo_storage_key', // null en local, public_id Cloudinary en prod
        'display_order',
        'is_cover',
    ];

    protected function casts(): array
    {
        return [
            'display_order' => 'integer',
            'is_cover'      => 'boolean',
        ];
    }

    public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class);
    }
}
