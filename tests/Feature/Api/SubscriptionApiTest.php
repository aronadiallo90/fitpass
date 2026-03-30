<?php

namespace Tests\Feature\Api;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SubscriptionApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
    }

    #[Test]
    public function authenticated_member_can_create_subscription(): void
    {
        // Arrange
        $user = User::factory()->create();
        $plan = SubscriptionPlan::factory()->mensuel()->create();
        Sanctum::actingAs($user);

        // Act
        $response = $this->postJson('/api/v1/subscriptions', [
            'plan_id' => $plan->id,
            'method'  => 'wave',
        ]);

        // Assert
        $response->assertStatus(201)
            ->assertJsonStructure([
                'data'    => ['id', 'reference', 'status', 'amount_fcfa', 'plan'],
                'payment' => ['id', 'paytech_ref', 'method', 'amount_fcfa'],
            ]);

        $this->assertDatabaseHas('subscriptions', ['user_id' => $user->id, 'status' => 'pending']);
    }

    #[Test]
    public function unauthenticated_user_cannot_create_subscription(): void
    {
        $plan = SubscriptionPlan::factory()->create();

        $response = $this->postJson('/api/v1/subscriptions', [
            'plan_id' => $plan->id,
            'method'  => 'wave',
        ]);

        $response->assertStatus(401);
    }

    #[Test]
    public function subscription_creation_fails_with_invalid_payment_method(): void
    {
        // Arrange
        $user = User::factory()->create();
        $plan = SubscriptionPlan::factory()->create();
        Sanctum::actingAs($user);

        // Act
        $response = $this->postJson('/api/v1/subscriptions', [
            'plan_id' => $plan->id,
            'method'  => 'mastercard', // invalide
        ]);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['method']);
    }

    #[Test]
    public function subscription_creation_fails_if_user_already_has_active_subscription(): void
    {
        // Arrange
        $user = User::factory()->create();
        $plan = SubscriptionPlan::factory()->mensuel()->create();
        Subscription::factory()->active()->create(['user_id' => $user->id, 'plan_id' => $plan->id]);
        Sanctum::actingAs($user);

        // Act
        $response = $this->postJson('/api/v1/subscriptions', [
            'plan_id' => $plan->id,
            'method'  => 'wave',
        ]);

        // Assert
        $response->assertStatus(422);
    }

    #[Test]
    public function member_can_list_their_subscriptions(): void
    {
        // Arrange
        $user = User::factory()->create();
        $plan = SubscriptionPlan::factory()->create();
        Subscription::factory()->count(3)->create(['user_id' => $user->id, 'plan_id' => $plan->id]);
        Sanctum::actingAs($user);

        // Act
        $response = $this->getJson('/api/v1/subscriptions');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'links', 'meta']);
        $this->assertCount(3, $response->json('data'));
    }

    #[Test]
    public function member_cannot_see_other_members_subscriptions(): void
    {
        // Arrange
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $plan  = SubscriptionPlan::factory()->create();
        Subscription::factory()->create(['user_id' => $user2->id, 'plan_id' => $plan->id]);
        Sanctum::actingAs($user1);

        // Act
        $response = $this->getJson('/api/v1/subscriptions');

        // Assert
        $response->assertStatus(200);
        $this->assertCount(0, $response->json('data'));
    }

    #[Test]
    public function member_can_show_their_subscription(): void
    {
        // Arrange
        $user         = User::factory()->create();
        $plan         = SubscriptionPlan::factory()->create();
        $subscription = Subscription::factory()->active()->create(['user_id' => $user->id, 'plan_id' => $plan->id]);
        Sanctum::actingAs($user);

        // Act
        $response = $this->getJson("/api/v1/subscriptions/{$subscription->id}");

        // Assert
        $response->assertStatus(200)
            ->assertJsonPath('data.id', $subscription->id);
    }

    #[Test]
    public function paytech_webhook_activates_subscription(): void
    {
        // Arrange
        $user         = User::factory()->create();
        $plan         = SubscriptionPlan::factory()->mensuel()->create();
        $subscription = Subscription::factory()->pending()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ]);
        $payment = \App\Models\Payment::factory()->pending()->create([
            'subscription_id' => $subscription->id,
            'user_id'         => $user->id,
            'paytech_ref'     => 'FAKE-WEBHOOK-TEST',
        ]);

        // Act — simuler webhook PayTech
        $response = $this->postJson('/api/v1/webhooks/paytech', [
            'ref_command'   => 'FAKE-WEBHOOK-TEST',
            'response_text' => 'SUCCESS',
        ]);

        // Assert
        $response->assertStatus(200);
        $this->assertDatabaseHas('payments', ['paytech_ref' => 'FAKE-WEBHOOK-TEST', 'status' => 'completed']);
        $this->assertDatabaseHas('subscriptions', ['id' => $subscription->id, 'status' => 'active']);
    }
}
