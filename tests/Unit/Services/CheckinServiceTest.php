<?php

namespace Tests\Unit\Services;

use App\Models\Gym;
use App\Models\GymCheckin;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\CheckinService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CheckinServiceTest extends TestCase
{
    use RefreshDatabase;

    private CheckinService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(CheckinService::class);
        Queue::fake();
    }

    #[Test]
    public function it_validates_valid_qr_token_with_active_subscription(): void
    {
        // Arrange
        $user = User::factory()->create();
        $gym  = Gym::factory()->create();
        Subscription::factory()->active()->create(['user_id' => $user->id]);

        // Act
        $checkin = $this->service->validate($user->qr_token, $gym);

        // Assert
        $this->assertEquals('valid', $checkin->status);
        $this->assertEquals($user->id, $checkin->user_id);
        $this->assertEquals($gym->id, $checkin->gym_id);
        $this->assertDatabaseHas('gym_checkins', ['user_id' => $user->id, 'status' => 'valid']);
    }

    #[Test]
    public function it_rejects_unknown_qr_token(): void
    {
        // Arrange
        $gym = Gym::factory()->create();

        // Act
        $checkin = $this->service->validate('00000000-0000-0000-0000-000000000000', $gym);

        // Assert
        $this->assertEquals('invalid', $checkin->status);
        $this->assertEquals('QR code inconnu', $checkin->failure_reason);
    }

    #[Test]
    public function it_rejects_member_without_active_subscription(): void
    {
        // Arrange
        $user = User::factory()->create();
        $gym  = Gym::factory()->create();
        Subscription::factory()->expired()->create(['user_id' => $user->id]);

        // Act
        $checkin = $this->service->validate($user->qr_token, $gym);

        // Assert
        $this->assertEquals('invalid', $checkin->status);
        $this->assertStringContainsString('abonnement', $checkin->failure_reason);
    }

    #[Test]
    public function it_rejects_duplicate_checkin_same_gym_same_day(): void
    {
        // Arrange
        $user         = User::factory()->create();
        $gym          = Gym::factory()->create();
        $subscription = Subscription::factory()->active()->create(['user_id' => $user->id]);

        // Premier checkin (valide)
        GymCheckin::create([
            'user_id'         => $user->id,
            'gym_id'          => $gym->id,
            'subscription_id' => $subscription->id,
            'status'          => 'valid',
        ]);

        // Act — deuxième tentative
        $checkin = $this->service->validate($user->qr_token, $gym);

        // Assert
        $this->assertEquals('invalid', $checkin->status);
        $this->assertStringContainsString('aujourd\'hui', $checkin->failure_reason);
    }

    #[Test]
    public function it_allows_checkin_at_different_gym_same_day(): void
    {
        // Arrange
        $user         = User::factory()->create();
        $gym1         = Gym::factory()->create();
        $gym2         = Gym::factory()->create();
        $subscription = Subscription::factory()->active()->create(['user_id' => $user->id]);

        // Checkin salle 1
        GymCheckin::create([
            'user_id'         => $user->id,
            'gym_id'          => $gym1->id,
            'subscription_id' => $subscription->id,
            'status'          => 'valid',
        ]);

        // Act — salle 2 (doit être accepté)
        $checkin = $this->service->validate($user->qr_token, $gym2);

        // Assert
        $this->assertEquals('valid', $checkin->status);
    }

    #[Test]
    public function it_decrements_checkins_for_discovery_plan(): void
    {
        // Arrange
        $user         = User::factory()->create();
        $gym          = Gym::factory()->create();
        $subscription = Subscription::factory()->active()->decouverte()->create([
            'user_id'            => $user->id,
            'checkins_remaining' => 2,
        ]);

        // Act
        $this->service->validate($user->qr_token, $gym);

        // Assert
        $this->assertEquals(1, $subscription->fresh()->checkins_remaining);
    }

    #[Test]
    public function it_rejects_when_no_checkins_left_on_discovery_plan(): void
    {
        // Arrange
        $user         = User::factory()->create();
        $gym          = Gym::factory()->create();
        Subscription::factory()->active()->create([
            'user_id'            => $user->id,
            'checkins_remaining' => 0,
        ]);

        // Act
        $checkin = $this->service->validate($user->qr_token, $gym);

        // Assert
        $this->assertEquals('invalid', $checkin->status);
        $this->assertStringContainsString('séances', $checkin->failure_reason);
    }

    #[Test]
    public function it_does_not_decrement_unlimited_plan(): void
    {
        // Arrange
        $user         = User::factory()->create();
        $gym          = Gym::factory()->create();
        $subscription = Subscription::factory()->active()->create([
            'user_id'            => $user->id,
            'checkins_remaining' => null, // illimité
        ]);

        // Act
        $this->service->validate($user->qr_token, $gym);

        // Assert — checkins_remaining toujours null
        $this->assertNull($subscription->fresh()->checkins_remaining);
    }

    #[Test]
    public function it_dispatches_sms_job_on_valid_checkin(): void
    {
        // Arrange
        $user = User::factory()->create();
        $gym  = Gym::factory()->create();
        Subscription::factory()->active()->create(['user_id' => $user->id]);

        // Act
        $checkin = $this->service->validate($user->qr_token, $gym);

        // Assert
        $this->assertEquals('valid', $checkin->status);
        Queue::assertPushed(\App\Jobs\SendCheckinSms::class);
    }
}
