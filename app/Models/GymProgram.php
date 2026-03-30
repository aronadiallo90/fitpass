<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GymProgram extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'gym_id',
        'name',
        'description',
        'schedule',
        'duration_minutes',
        'max_spots',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'schedule'         => 'array',
            'duration_minutes' => 'integer',
            'max_spots'        => 'integer',
            'is_active'        => 'boolean',
        ];
    }

    public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class);
    }
}
