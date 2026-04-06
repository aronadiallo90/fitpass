<?php

namespace Tests\Feature\Member;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class QrRegenerationTest extends TestCase
{
    use RefreshDatabase;

    private User $member;

    protected function setUp(): void
    {
        parent::setUp();
        $this->member = User::factory()->create([
            'role'                    => 'member',
            'qr_token_regenerated_at' => null,
        ]);
    }

    #[Test]
    public function member_can_regenerate_qr_code(): void
    {
        // Arrange — abonnement actif, pas de cooldown
        Subscription::factory()->active()->create(['user_id' => $this->member->id]);

        $oldToken = $this->member->qr_token;

        // Act
        $response = $this->actingAs($this->member)
            ->post(route('member.qrcode.regenerate'));

        // Assert
        $response->assertRedirect(route('member.qrcode'));
        $response->assertSessionHas('success');

        $this->member->refresh();
        $this->assertNotEquals($oldToken, $this->member->qr_token);
    }

    #[Test]
    public function member_cannot_regenerate_within_24h(): void
    {
        // Arrange — cooldown actif (régénération il y a 2h)
        $this->member->update(['qr_token_regenerated_at' => now()->subHours(2)]);
        Subscription::factory()->active()->create(['user_id' => $this->member->id]);

        // Act
        $response = $this->actingAs($this->member)
            ->post(route('member.qrcode.regenerate'));

        // Assert — redirect back avec message d'erreur
        $response->assertRedirect();
        $response->assertSessionHas('error');

        // Le token ne doit pas avoir changé
        $tokenBefore = $this->member->qr_token;
        $this->member->refresh();
        $this->assertEquals($tokenBefore, $this->member->qr_token);
    }

    #[Test]
    public function member_cannot_regenerate_without_active_subscription(): void
    {
        // Arrange — pas d'abonnement du tout, pas de cooldown
        // Act
        $response = $this->actingAs($this->member)
            ->post(route('member.qrcode.regenerate'));

        // Assert
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    #[Test]
    public function unauthenticated_user_cannot_access_regeneration_route(): void
    {
        // Act — sans authentification
        $response = $this->post(route('member.qrcode.regenerate'));

        // Assert — redirigé vers login
        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function gym_owner_cannot_access_member_qr_regeneration(): void
    {
        // Arrange — gym_owner authentifié
        $gymOwner = User::factory()->create(['role' => 'gym_owner']);

        // Act
        $response = $this->actingAs($gymOwner)
            ->post(route('member.qrcode.regenerate'));

        // Assert — 403 car middleware role:member
        $response->assertStatus(403);
    }

    #[Test]
    public function member_can_regenerate_again_after_24h_cooldown(): void
    {
        // Arrange — cooldown expiré (il y a plus de 24h)
        $this->member->update(['qr_token_regenerated_at' => now()->subHours(25)]);
        Subscription::factory()->active()->create(['user_id' => $this->member->id]);

        // Act
        $response = $this->actingAs($this->member)
            ->post(route('member.qrcode.regenerate'));

        // Assert — succès
        $response->assertRedirect(route('member.qrcode'));
        $response->assertSessionHas('success');
    }
}
