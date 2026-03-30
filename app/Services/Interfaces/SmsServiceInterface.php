<?php

namespace App\Services\Interfaces;

use App\Models\Subscription;
use App\Models\User;

interface SmsServiceInterface
{
    public function sendWelcome(User $user): void;

    public function sendActivation(User $user, Subscription $subscription): void;

    public function sendReminder(User $user, Subscription $subscription, int $daysLeft): void;

    public function sendExpiration(User $user, Subscription $subscription): void;

    public function sendCheckinConfirmation(User $user, string $gymName): void;
}
