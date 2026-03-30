<?php

namespace Tests\Feature\Sms;

use App\Jobs\SendActivationSms;
use App\Jobs\SendExpirationSms;
use App\Jobs\SendReminderSms;
use App\Jobs\SendWelcomeSms;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OnboardingSequenceTest extends TestCase
{
    use RefreshDatabase;

    // ─── Inscription ───────────────────────────────────────────────────────

    #[Test]
    public function register_dispatches_welcome_sms(): void
    {
        Queue::fake();

        $this->post(route('register.store'), [
            'name'                  => 'Awa Diop',
            'phone'                 => '+221771234567',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        Queue::assertPushedOn('notifications', SendWelcomeSms::class, function (SendWelcomeSms $job): bool {
            return $job->user->phone === '+221771234567';
        });
    }

    // ─── Activation ────────────────────────────────────────────────────────

    #[Test]
    public function activate_dispatches_activation_sms(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $plan = SubscriptionPlan::factory()->create(['duration_days' => 30]);
        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status'  => 'pending',
        ]);

        $service = app(SubscriptionService::class);
        $service->activate($subscription);

        Queue::assertPushedOn('notifications', SendActivationSms::class, function (SendActivationSms $job) use ($subscription): bool {
            return $job->subscription->id === $subscription->id;
        });
    }

    // ─── Expiration ────────────────────────────────────────────────────────

    #[Test]
    public function expire_dispatches_expiration_sms(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $plan = SubscriptionPlan::factory()->create();
        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status'  => 'active',
        ]);

        $service = app(SubscriptionService::class);
        $service->expire($subscription);

        Queue::assertPushedOn('notifications', SendExpirationSms::class, function (SendExpirationSms $job) use ($subscription): bool {
            return $job->subscription->id === $subscription->id;
        });
    }

    #[Test]
    public function expire_sets_status_to_expired(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $plan = SubscriptionPlan::factory()->create();
        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status'  => 'active',
        ]);

        $service = app(SubscriptionService::class);
        $result = $service->expire($subscription);

        $this->assertEquals('expired', $result->status);
        $this->assertDatabaseHas('subscriptions', [
            'id'     => $subscription->id,
            'status' => 'expired',
        ]);
    }

    // ─── Rappels J-7 / J-1 ────────────────────────────────────────────────

    #[Test]
    public function expire_subscriptions_command_dispatches_reminder_j7(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $plan = SubscriptionPlan::factory()->create();
        Subscription::factory()->create([
            'user_id'    => $user->id,
            'plan_id'    => $plan->id,
            'status'     => 'active',
            'expires_at' => now()->addDays(7)->startOfDay(),
        ]);

        $this->artisan('fitpass:expire-subscriptions')->assertSuccessful();

        Queue::assertPushedOn('notifications', SendReminderSms::class, function (SendReminderSms $job): bool {
            return $job->daysLeft === 7;
        });
    }

    #[Test]
    public function expire_subscriptions_command_dispatches_reminder_j1(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $plan = SubscriptionPlan::factory()->create();
        Subscription::factory()->create([
            'user_id'    => $user->id,
            'plan_id'    => $plan->id,
            'status'     => 'active',
            'expires_at' => now()->addDays(1)->startOfDay(),
        ]);

        $this->artisan('fitpass:expire-subscriptions')->assertSuccessful();

        Queue::assertPushedOn('notifications', SendReminderSms::class, function (SendReminderSms $job): bool {
            return $job->daysLeft === 1;
        });
    }

    #[Test]
    public function expire_subscriptions_command_expires_overdue_subscriptions(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $plan = SubscriptionPlan::factory()->create();
        $subscription = Subscription::factory()->create([
            'user_id'    => $user->id,
            'plan_id'    => $plan->id,
            'status'     => 'active',
            'expires_at' => now()->subDay(),
        ]);

        $this->artisan('fitpass:expire-subscriptions')->assertSuccessful();

        $this->assertDatabaseHas('subscriptions', [
            'id'     => $subscription->id,
            'status' => 'expired',
        ]);

        Queue::assertPushedOn('notifications', SendExpirationSms::class);
    }
}
