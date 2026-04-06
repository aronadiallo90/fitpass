<?php

namespace Tests\Unit\Services;

use App\Exceptions\QrRegenerationNoActiveSubscriptionException;
use App\Exceptions\QrRegenerationTooSoonException;
use App\Models\SmsLog;
use App\Models\Subscription;
use App\Models\User;
use App\Services\QrRegenerationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class QrRegenerationServiceTest extends TestCase
{
    use RefreshDatabase;

    private QrRegenerationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(QrRegenerationService::class);
    }

    #[Test]
    public function it_regenerates_qr_token_when_no_cooldown(): void
    {
        // Arrange — membre sans qr_token_regenerated_at + abonnement actif
        $member = User::factory()->create([
            'role'                    => 'member',
            'qr_token_regenerated_at' => null,
        ]);
        Subscription::factory()->active()->create(['user_id' => $member->id]);

        $oldToken = $member->qr_token;

        // Act
        $result = $this->service->regenerate($member);

        // Assert
        $this->assertArrayHasKey('token', $result);
        $this->assertArrayHasKey('regenerated_at', $result);
        $this->assertNotEquals($oldToken, $result['token']);
        $this->assertDatabaseHas('users', [
            'id'       => $member->id,
            'qr_token' => $result['token'],
        ]);
    }

    #[Test]
    public function it_throws_exception_when_cooldown_active(): void
    {
        // Arrange — régénération < 24h
        $member = User::factory()->create([
            'role'                    => 'member',
            'qr_token_regenerated_at' => now()->subHours(12),
        ]);
        Subscription::factory()->active()->create(['user_id' => $member->id]);

        // Act & Assert
        $this->expectException(QrRegenerationTooSoonException::class);
        $this->service->regenerate($member);
    }

    #[Test]
    public function it_throws_exception_when_no_active_subscription(): void
    {
        // Arrange — membre sans abonnement actif, pas de cooldown
        $member = User::factory()->create([
            'role'                    => 'member',
            'qr_token_regenerated_at' => null,
        ]);
        // Abonnement expiré — ne doit pas débloquer la régénération
        Subscription::factory()->expired()->create(['user_id' => $member->id]);

        // Act & Assert
        $this->expectException(QrRegenerationNoActiveSubscriptionException::class);
        $this->service->regenerate($member);
    }

    #[Test]
    public function it_updates_qr_token_regenerated_at_after_regeneration(): void
    {
        // Arrange
        $member = User::factory()->create([
            'role'                    => 'member',
            'qr_token_regenerated_at' => null,
        ]);
        Subscription::factory()->active()->create(['user_id' => $member->id]);

        // Act
        $this->service->regenerate($member);

        // Assert — qr_token_regenerated_at est maintenant rempli
        $member->refresh();
        $this->assertNotNull($member->qr_token_regenerated_at);
        $this->assertTrue($member->qr_token_regenerated_at->isToday());
    }

    #[Test]
    public function it_can_regenerate_after_24h_cooldown(): void
    {
        // Arrange — régénération il y a exactement 25h (> 24h)
        $member = User::factory()->create([
            'role'                    => 'member',
            'qr_token_regenerated_at' => now()->subHours(25),
        ]);
        Subscription::factory()->active()->create(['user_id' => $member->id]);

        // Act — ne doit pas lancer d'exception
        $result = $this->service->regenerate($member);

        // Assert
        $this->assertArrayHasKey('token', $result);
    }

    #[Test]
    public function it_logs_qr_regeneration_in_sms_logs(): void
    {
        // Arrange
        $member = User::factory()->create([
            'role'                    => 'member',
            'qr_token_regenerated_at' => null,
        ]);
        Subscription::factory()->active()->create(['user_id' => $member->id]);

        // Act
        $this->service->regenerate($member);

        // Assert — log créé dans sms_logs
        $this->assertDatabaseHas('sms_logs', [
            'user_id'    => $member->id,
            'event_type' => 'qr_regeneration',
            'status'     => 'sent',
        ]);
    }

    #[Test]
    public function can_regenerate_returns_true_when_no_previous_regeneration(): void
    {
        $member = User::factory()->create([
            'role'                    => 'member',
            'qr_token_regenerated_at' => null,
        ]);

        $this->assertTrue($this->service->canRegenerate($member));
    }

    #[Test]
    public function can_regenerate_returns_false_within_24h(): void
    {
        $member = User::factory()->create([
            'role'                    => 'member',
            'qr_token_regenerated_at' => now()->subHours(10),
        ]);

        $this->assertFalse($this->service->canRegenerate($member));
    }

    #[Test]
    public function get_next_regeneration_at_returns_null_when_can_regenerate(): void
    {
        $member = User::factory()->create([
            'role'                    => 'member',
            'qr_token_regenerated_at' => null,
        ]);

        $this->assertNull($this->service->getNextRegenerationAt($member));
    }

    #[Test]
    public function get_next_regeneration_at_returns_correct_time_during_cooldown(): void
    {
        $regeneratedAt = now()->subHours(10);
        $member = User::factory()->create([
            'role'                    => 'member',
            'qr_token_regenerated_at' => $regeneratedAt,
        ]);

        $nextAt = $this->service->getNextRegenerationAt($member);

        $this->assertNotNull($nextAt);
        // Doit être approximativement 14h dans le futur (24h - 10h déjà écoulées)
        $this->assertTrue($nextAt->isFuture());
        // Doit être dans 14h (±1 minute de tolérance pour l'exécution du test)
        $expectedDiff = 14 * 60; // minutes
        $actualDiff   = (int) now()->diffInMinutes($nextAt, false);
        $this->assertEqualsWithDelta($expectedDiff, $actualDiff, 1);
    }
}
