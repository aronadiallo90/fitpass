<?php

namespace Tests\Feature\Web;

use App\Models\Gym;
use App\Models\GymCheckin;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MemberWebTest extends TestCase
{
    use RefreshDatabase;

    // ─── Auth guards ───────────────────────────────────────────────────────

    #[Test]
    public function unauthenticated_user_is_redirected_from_dashboard(): void
    {
        $this->get(route('member.dashboard'))->assertRedirect(route('login'));
    }

    #[Test]
    public function gym_owner_cannot_access_member_dashboard(): void
    {
        $owner = User::factory()->gymOwner()->create();
        $this->actingAs($owner)->get(route('member.dashboard'))->assertForbidden();
    }

    // ─── Dashboard ─────────────────────────────────────────────────────────

    #[Test]
    public function member_sees_dashboard_without_subscription(): void
    {
        $member = User::factory()->create();

        $this->actingAs($member)
            ->get(route('member.dashboard'))
            ->assertOk()
            ->assertViewIs('member.dashboard')
            ->assertViewHas('activeSubscription', null);
    }

    #[Test]
    public function member_sees_dashboard_with_active_subscription(): void
    {
        $member = User::factory()->create();
        $plan   = SubscriptionPlan::factory()->mensuel()->create();
        Subscription::factory()->active()->for($member)->for($plan, 'plan')->create();

        $response = $this->actingAs($member)->get(route('member.dashboard'));

        $response->assertOk()
            ->assertViewIs('member.dashboard')
            ->assertViewHas('activeSubscription');

        $this->assertNotNull($response->viewData('activeSubscription'));
    }

    #[Test]
    public function member_dashboard_shows_recent_checkins(): void
    {
        $member = User::factory()->create();
        $gym    = Gym::factory()->create();
        GymCheckin::factory()->count(3)->create([
            'user_id' => $member->id,
            'gym_id'  => $gym->id,
            'status'  => 'valid',
        ]);

        $response = $this->actingAs($member)->get(route('member.dashboard'));

        $response->assertOk();
        $this->assertCount(3, $response->viewData('recentCheckins'));
    }

    #[Test]
    public function dashboard_shows_max_5_recent_checkins(): void
    {
        $member = User::factory()->create();
        $gym    = Gym::factory()->create();
        GymCheckin::factory()->count(8)->create([
            'user_id' => $member->id,
            'gym_id'  => $gym->id,
            'status'  => 'valid',
        ]);

        $response = $this->actingAs($member)->get(route('member.dashboard'));

        $response->assertOk();
        $this->assertCount(5, $response->viewData('recentCheckins'));
    }

    // ─── QR Code ───────────────────────────────────────────────────────────

    #[Test]
    public function member_can_view_qrcode_page(): void
    {
        $member = User::factory()->create();

        $this->actingAs($member)
            ->get(route('member.qrcode'))
            ->assertOk()
            ->assertViewIs('member.qrcode');
    }

    #[Test]
    public function qrcode_page_passes_qr_code_to_view(): void
    {
        $member = User::factory()->create();

        $response = $this->actingAs($member)->get(route('member.qrcode'));

        $response->assertOk();
        $this->assertNotNull($response->viewData('qrCode'));
    }

    #[Test]
    public function qrcode_page_shows_null_subscription_when_none_active(): void
    {
        $member = User::factory()->create();

        $response = $this->actingAs($member)->get(route('member.qrcode'));

        $response->assertOk()->assertViewHas('activeSubscription', null);
    }

    // ─── Subscriptions ─────────────────────────────────────────────────────

    #[Test]
    public function member_can_view_subscriptions_page(): void
    {
        $member = User::factory()->create();
        SubscriptionPlan::factory()->mensuel()->create();

        $this->actingAs($member)
            ->get(route('member.subscriptions'))
            ->assertOk()
            ->assertViewIs('member.subscriptions')
            ->assertViewHas('plans');
    }

    #[Test]
    public function subscriptions_page_lists_available_plans(): void
    {
        $member = User::factory()->create();
        SubscriptionPlan::factory()->count(3)->create(['is_active' => true]);
        SubscriptionPlan::factory()->create(['is_active' => false]);

        $response = $this->actingAs($member)->get(route('member.subscriptions'));

        // Seuls les plans actifs sont affichés
        $this->assertCount(3, $response->viewData('plans'));
    }

    #[Test]
    public function member_can_create_subscription_via_web(): void
    {
        $member = User::factory()->create();
        $plan   = SubscriptionPlan::factory()->mensuel()->create();

        $this->actingAs($member)
            ->post(route('member.subscriptions.store'), [
                'plan_id' => $plan->id,
                'method'  => 'wave',
            ])
            ->assertRedirect(route('member.subscriptions'));

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $member->id,
            'plan_id' => $plan->id,
            'status'  => 'pending',
        ]);
    }

    #[Test]
    public function subscription_creation_fails_with_invalid_plan(): void
    {
        $member = User::factory()->create();

        $this->actingAs($member)
            ->post(route('member.subscriptions.store'), [
                'plan_id' => 'invalid-uuid',
                'method'  => 'wave',
            ])
            ->assertSessionHasErrors('plan_id');
    }

    #[Test]
    public function subscription_creation_fails_with_invalid_method(): void
    {
        $member = User::factory()->create();
        $plan   = SubscriptionPlan::factory()->mensuel()->create();

        $this->actingAs($member)
            ->post(route('member.subscriptions.store'), [
                'plan_id' => $plan->id,
                'method'  => 'paypal',
            ])
            ->assertSessionHasErrors('method');
    }

    #[Test]
    public function member_cannot_subscribe_when_already_active(): void
    {
        $member = User::factory()->create();
        $plan   = SubscriptionPlan::factory()->mensuel()->create();
        Subscription::factory()->active()->for($member)->for($plan, 'plan')->create();

        $this->actingAs($member)
            ->post(route('member.subscriptions.store'), [
                'plan_id' => $plan->id,
                'method'  => 'wave',
            ])
            ->assertRedirect()
            ->assertSessionHas('error');
    }

    // ─── Payments ──────────────────────────────────────────────────────────

    #[Test]
    public function member_can_view_payments_page(): void
    {
        $member = User::factory()->create();

        $this->actingAs($member)
            ->get(route('member.payments'))
            ->assertOk()
            ->assertViewIs('member.payments')
            ->assertViewHas('payments');
    }

    #[Test]
    public function member_only_sees_own_payments(): void
    {
        $member = User::factory()->create();
        $other  = User::factory()->create();
        $plan   = SubscriptionPlan::factory()->mensuel()->create();

        $mySubscription    = Subscription::factory()->for($member)->for($plan, 'plan')->create();
        $otherSubscription = Subscription::factory()->for($other)->for($plan, 'plan')->create();

        Payment::factory()->create(['subscription_id' => $mySubscription->id, 'user_id' => $member->id]);
        Payment::factory()->create(['subscription_id' => $otherSubscription->id, 'user_id' => $other->id]);

        $response = $this->actingAs($member)->get(route('member.payments'));

        $this->assertCount(1, $response->viewData('payments'));
    }

    // ─── Checkins ──────────────────────────────────────────────────────────

    #[Test]
    public function member_can_view_checkins_page(): void
    {
        $member = User::factory()->create();

        $this->actingAs($member)
            ->get(route('member.checkins'))
            ->assertOk()
            ->assertViewIs('member.checkins')
            ->assertViewHas('checkins');
    }

    #[Test]
    public function member_only_sees_own_checkins(): void
    {
        $member = User::factory()->create();
        $other  = User::factory()->create();
        $gym    = Gym::factory()->create();

        GymCheckin::factory()->create(['user_id' => $member->id, 'gym_id' => $gym->id]);
        GymCheckin::factory()->create(['user_id' => $other->id, 'gym_id' => $gym->id]);

        $response = $this->actingAs($member)->get(route('member.checkins'));

        $this->assertCount(1, $response->viewData('checkins'));
    }

    // ─── Map ───────────────────────────────────────────────────────────────

    #[Test]
    public function member_can_view_map_page(): void
    {
        $member = User::factory()->create();
        Gym::factory()->count(3)->create(['is_active' => true]);

        $this->actingAs($member)
            ->get(route('member.map'))
            ->assertOk()
            ->assertViewIs('member.map')
            ->assertViewHas('gyms');
    }

    #[Test]
    public function map_only_shows_active_gyms(): void
    {
        $member = User::factory()->create();
        Gym::factory()->count(2)->create(['is_active' => true]);
        Gym::factory()->inactive()->create();

        $response = $this->actingAs($member)->get(route('member.map'));

        $this->assertCount(2, $response->viewData('gyms'));
    }
}
