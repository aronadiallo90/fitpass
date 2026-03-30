<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\Interfaces\SmsServiceInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendCheckinSms implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(
        public readonly User $user,
        public readonly string $gymName
    ) {}

    public function handle(SmsServiceInterface $smsService): void
    {
        $smsService->sendCheckinConfirmation($this->user, $this->gymName);
    }
}
