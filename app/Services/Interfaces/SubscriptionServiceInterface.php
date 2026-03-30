<?php

namespace App\Services\Interfaces;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;

interface SubscriptionServiceInterface
{
    public function create(User $user, SubscriptionPlan $plan): Subscription;

    public function activate(Subscription $subscription): Subscription;

    public function expire(Subscription $subscription): Subscription;

    public function cancel(Subscription $subscription): Subscription;

    public function getActive(User $user): ?Subscription;

    public function generateReference(): string;
}
