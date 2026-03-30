<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'subscription_id',
        'user_id',
        'paytech_ref',
        'paytech_token',
        'method',
        'status',
        'amount_fcfa',
        'paytech_payload',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount_fcfa'     => 'integer',
            'paytech_payload' => 'array',
            'paid_at'         => 'datetime',
        ];
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Accessor pour simplifier les vues
    public function getAmountAttribute(): int
    {
        return $this->amount_fcfa;
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function formattedAmount(): string
    {
        return number_format($this->amount_fcfa, 0, ',', ' ') . ' FCFA';
    }
}
