<?php

namespace Tests\Feature\Api;

use App\Models\Gym;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CheckinApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function gym_kiosk_can_validate_valid_qr_code(): void
    {
        // Arrange
        $user = User::factory()->create();
        $gym  = Gym::factory()->create();
        Subscription::factory()->active()->create(['user_id' => $user->id]);

        // Act — requête borne autonome (token salle dans header)
        $response = $this->postJson('/api/v1/checkins/validate', [
            'qr_token'      => $user->qr_token,
            'gym_api_token' => $gym->api_token,
        ]);

        // Assert
        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['id', 'status', 'is_valid', 'gym']]);
    }

    #[Test]
    public function kiosk_validation_fails_with_expired_subscription(): void
    {
        // Arrange
        $user = User::factory()->create();
        $gym  = Gym::factory()->create();
        Subscription::factory()->expired()->create(['user_id' => $user->id]);

        // Act
        $response = $this->postJson('/api/v1/checkins/validate', [
            'qr_token'      => $user->qr_token,
            'gym_api_token' => $gym->api_token,
        ]);

        // Assert
        $response->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    #[Test]
    public function kiosk_validation_fails_with_invalid_gym_token(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->postJson('/api/v1/checkins/validate', [
            'qr_token'      => $user->qr_token,
            'gym_api_token' => 'invalid-token',
        ]);

        // Assert
        $response->assertStatus(403);
    }

    #[Test]
    public function authenticated_member_can_list_their_checkins(): void
    {
        // Arrange
        $user = User::factory()->create();
        $gym  = Gym::factory()->create();
        $plan = SubscriptionPlan::factory()->create();
        $sub  = Subscription::factory()->active()->create(['user_id' => $user->id, 'plan_id' => $plan->id]);

        \App\Models\GymCheckin::factory()->count(3)->create([
            'user_id'         => $user->id,
            'gym_id'          => $gym->id,
            'subscription_id' => $sub->id,
        ]);

        Sanctum::actingAs($user);

        // Act
        $response = $this->getJson('/api/v1/checkins');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'links', 'meta']);
        $this->assertCount(3, $response->json('data'));
    }

    #[Test]
    public function unauthenticated_user_cannot_list_checkins(): void
    {
        $response = $this->getJson('/api/v1/checkins');
        $response->assertStatus(401);
    }
}
