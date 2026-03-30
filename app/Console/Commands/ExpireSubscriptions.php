<?php

namespace App\Console\Commands;

use App\Jobs\SendReminderSms;
use App\Models\Subscription;
use App\Services\Interfaces\SubscriptionServiceInterface;
use Illuminate\Console\Command;

class ExpireSubscriptions extends Command
{
    protected $signature   = 'fitpass:expire-subscriptions';
    protected $description = 'Expire les abonnements échus et envoie les rappels J-7 et J-1';

    public function __construct(private readonly SubscriptionServiceInterface $subscriptionService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        // 1. Expirer les abonnements dont la date est dépassée
        $expired = Subscription::where('status', 'active')
            ->where('expires_at', '<', now())
            ->get();

        foreach ($expired as $subscription) {
            $this->subscriptionService->expire($subscription);
            $this->info("Expiré : {$subscription->reference}");
        }

        // 2. Rappels J-7
        $this->sendReminders(7);

        // 3. Rappels J-1
        $this->sendReminders(1);

        $this->info("Terminé : {$expired->count()} expirés.");

        return Command::SUCCESS;
    }

    private function sendReminders(int $daysLeft): void
    {
        $subscriptions = Subscription::where('status', 'active')
            ->whereDate('expires_at', now()->addDays($daysLeft))
            ->with('user')
            ->get();

        foreach ($subscriptions as $subscription) {
            SendReminderSms::dispatch($subscription, $daysLeft)->onQueue('notifications');
        }

        $this->info("Rappels J-{$daysLeft} : {$subscriptions->count()} envoyés.");
    }
}
