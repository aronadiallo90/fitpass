<?php

namespace App\Jobs;

use App\Models\Subscription;
use App\Services\Interfaces\SmsServiceInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendReminderSms implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public readonly Subscription $subscription,
        public readonly int $daysLeft
    ) {}

    public function handle(SmsServiceInterface $smsService): void
    {
        $smsService->sendReminder($this->subscription->user, $this->subscription, $this->daysLeft);
    }
}
