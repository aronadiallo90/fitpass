<?php

namespace Tests\Unit\Services;

use App\Exceptions\SubscriptionException;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SubscriptionServiceTest extends TestCase
{
    use RefreshDatabase;

    private SubscriptionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(SubscriptionService::class);
        Queue::fake(); // Bloquer les jobs async pendant les tests
    }

    #[Test]
    public function it_creates_a_pending_subscription(): void
    {
        // Arrange
        $user = User::factory()->create();
        $plan = SubscriptionPlan::factory()->mensuel()->create();

        // Act
        $subscription = $this->service->create($user, $plan);

        // Assert
        $this->assertInstanceOf(Subscription::class, $subscription);
        $this->assertEquals('pending', $subscription->status);
        $this->assertEquals($plan->price_fcfa, $subscription->amount_fcfa);
        $this->assertNull($subscription->starts_at);
        $this->assertDatabaseHas('subscriptions', ['id' => $subscription->id, 'status' => 'pending']);
    }

    #[Test]
    public function it_throws_exception_when_user_already_has_active_subscription(): void
    {
        // Arrange
        $user = User::factory()->create();
        $plan = SubscriptionPlan::factory()->mensuel()->create();
        Subscription::factory()->active()->create(['user_id' => $user->id, 'plan_id' => $plan->id]);

        // Act & Assert
        $this->expectException(SubscriptionException::class);
        $this->service->create($user, $plan);
    }

    #[Test]
    public function it_activates_a_pending_subscription(): void
    {
        // Arrange
        $plan         = SubscriptionPlan::factory()->mensuel()->create();
        $subscription = Subscription::factory()->pending()->create(['plan_id' => $plan->id]);

        // Act
        $activated = $this->service->activate($subscription);

        // Assert
        $this->assertEquals('active', $activated->status);
        $this->assertNotNull($activated->starts_at);
        $this->assertNotNull($activated->expires_at);
        $this->assertTrue($activated->expires_at->isAfter(now()));
    }

    #[Test]
    public function it_sets_correct_expiry_date_on_activation(): void
    {
        // Arrange
        $plan         = SubscriptionPlan::factory()->create(['duration_days' => 30]);
        $subscription = Subscription::factory()->pending()->create(['plan_id' => $plan->id]);

        // Act
        $activated = $this->service->activate($subscription);

        // Assert — expires dans ~30 jours
        $this->assertTrue($activated->expires_at->isBetween(now()->addDays(29), now()->addDays(31)));
    }

    #[Test]
    public function it_dispatches_activation_sms_job(): void
    {
        // Arrange
        $plan         = SubscriptionPlan::factory()->mensuel()->create();
        $subscription = Subscription::factory()->pending()->create(['plan_id' => $plan->id]);

        // Act
        $this->service->activate($subscription);

        // Assert — job SMS dispatché
        Queue::assertPushed(\App\Jobs\SendActivationSms::class);
    }

    #[Test]
    public function it_expires_an_active_subscription(): void
    {
        // Arrange
        $subscription = Subscription::factory()->active()->create();

        // Act
        $expired = $this->service->expire($subscription);

        // Assert
        $this->assertEquals('expired', $expired->status);
    }

    #[Test]
    public function it_cancels_a_pending_subscription(): void
    {
        // Arrange
        $subscription = Subscription::factory()->pending()->create();

        // Act
        $cancelled = $this->service->cancel($subscription);

        // Assert
        $this->assertEquals('cancelled', $cancelled->status);
    }

    #[Test]
    public function it_cannot_cancel_an_expired_subscription(): void
    {
        // Arrange
        $subscription = Subscription::factory()->expired()->create();

        // Act & Assert
        $this->expectException(SubscriptionException::class);
        $this->service->cancel($subscription);
    }

    #[Test]
    public function it_returns_active_subscription_for_user(): void
    {
        // Arrange
        $user         = User::factory()->create();
        $plan         = SubscriptionPlan::factory()->create();
        $subscription = Subscription::factory()->active()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ]);

        // Act
        $active = $this->service->getActive($user);

        // Assert
        $this->assertNotNull($active);
        $this->assertEquals($subscription->id, $active->id);
    }

    #[Test]
    public function it_returns_null_when_no_active_subscription(): void
    {
        // Arrange
        $user = User::factory()->create();
        Subscription::factory()->expired()->create(['user_id' => $user->id]);

        // Act
        $active = $this->service->getActive($user);

        // Assert
        $this->assertNull($active);
    }

    #[Test]
    public function it_generates_unique_reference_in_correct_format(): void
    {
        // Act
        $ref1 = $this->service->generateReference();
        // Créer une subscription pour que la séquence avance
        Subscription::factory()->create(['reference' => $ref1]);
        $ref2 = $this->service->generateReference();

        // Assert
        $year = now()->year;
        $this->assertMatchesRegularExpression("/^FIT-{$year}-\\d{5}$/", $ref1);
        $this->assertNotEquals($ref1, $ref2);
    }

    #[Test]
    public function discovery_plan_sets_checkins_limit(): void
    {
        // Arrange
        $user = User::factory()->create();
        $plan = SubscriptionPlan::factory()->decouverte()->create();

        // Act
        $subscription = $this->service->create($user, $plan);

        // Assert
        $this->assertEquals(4, $subscription->checkins_remaining);
    }
}
