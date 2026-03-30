<?php

namespace App\Jobs;

use App\Models\Subscription;
use App\Services\Interfaces\SmsServiceInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendExpirationSms implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(public readonly Subscription $subscription) {}

    public function handle(SmsServiceInterface $smsService): void
    {
        $smsService->sendExpiration($this->subscription->user, $this->subscription);
    }
}
