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

class AdminWebTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Crée un admin avec 2FA validé et session vérifiée.
     * Sans ça, le middleware 2fa redirige vers /two-factor/setup.
     */
    private function adminWithSession(): User
    {
        $admin = User::factory()->admin()->create([
            'two_factor_secret'       => encrypt('fakesecret'),
            'two_factor_confirmed_at' => now(),
        ]);

        // Marquer la session 2FA comme vérifiée
        $this->withSession(['two_factor_verified' => true]);

        return $admin;
    }

    // ─── Auth guards ───────────────────────────────────────────────────────

    #[Test]
    public function unauthenticated_user_is_redirected_from_admin(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    #[Test]
    public function member_cannot_access_admin_dashboard(): void
    {
        $member = User::factory()->create();
        $this->actingAs($member)->get(route('admin.dashboard'))->assertForbidden();
    }

    #[Test]
    public function gym_owner_cannot_access_admin_dashboard(): void
    {
        $owner = User::factory()->gymOwner()->create();
        $this->actingAs($owner)->get(route('admin.dashboard'))->assertForbidden();
    }

    #[Test]
    public function admin_without_2fa_is_redirected_to_setup(): void
    {
        // Admin sans 2FA configuré → redirigé vers setup
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('two-factor.setup'));
    }

    // ─── Dashboard ─────────────────────────────────────────────────────────

    #[Test]
    public function admin_sees_dashboard_with_kpis(): void
    {
        $admin = $this->adminWithSession();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertViewIs('admin.dashboard')
            ->assertViewHas('totalMembers')
            ->assertViewHas('monthRevenue')
            ->assertViewHas('activeSubscriptions')
            ->assertViewHas('todayCheckins')
            ->assertViewHas('recentPayments')
            ->assertViewHas('recentMembers');
    }

    #[Test]
    public function dashboard_counts_active_members_correctly(): void
    {
        $admin = $this->adminWithSession();

        User::factory()->count(3)->create(['role' => 'member', 'is_active' => true]);
        User::factory()->count(2)->create(['role' => 'member', 'is_active' => false]);
        User::factory()->count(1)->admin()->create(['is_active' => true]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        // Seuls les 3 membres actifs comptent (pas les admins, pas les inactifs)
        $this->assertEquals(3, $response->viewData('totalMembers'));
    }

    #[Test]
    public function dashboard_revenue_counts_only_completed_payments_this_month(): void
    {
        $admin  = $this->adminWithSession();
        $member = User::factory()->create();
        $plan   = SubscriptionPlan::factory()->mensuel()->create();

        $subscription = Subscription::factory()->for($member)->for($plan, 'plan')->create();

        // Paiement complété ce mois — doit compter
        Payment::factory()->create([
            'subscription_id' => $subscription->id,
            'user_id'         => $member->id,
            'status'          => 'completed',
            'amount_fcfa'     => 25000,
        ]);

        // Paiement échoué — ne doit pas compter
        Payment::factory()->failed()->create([
            'subscription_id' => $subscription->id,
            'user_id'         => $member->id,
            'amount_fcfa'     => 25000,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $this->assertEquals(25000, $response->viewData('monthRevenue'));
    }

    // ─── Members ───────────────────────────────────────────────────────────

    #[Test]
    public function admin_can_list_members(): void
    {
        $admin = $this->adminWithSession();
        User::factory()->count(5)->create(['role' => 'member']);

        $response = $this->actingAs($admin)
            ->get(route('admin.members'))
            ->assertOk()
            ->assertViewIs('admin.members');

        $this->assertEquals(5, $response->viewData('members')->total());
    }

    #[Test]
    public function members_list_is_searchable_by_name(): void
    {
        $admin = $this->adminWithSession();
        User::factory()->create(['role' => 'member', 'name' => 'Moussa Diop']);
        User::factory()->create(['role' => 'member', 'name' => 'Awa Fall']);

        $response = $this->actingAs($admin)
            ->get(route('admin.members', ['search' => 'Moussa']));

        $this->assertCount(1, $response->viewData('members'));
    }

    #[Test]
    public function admin_can_toggle_member_active_status(): void
    {
        $admin  = $this->adminWithSession();
        $member = User::factory()->create(['role' => 'member', 'is_active' => true]);

        $this->actingAs($admin)
            ->patch(route('admin.members.toggle', $member))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertFalse($member->fresh()->is_active);
    }

    #[Test]
    public function admin_can_reactivate_disabled_member(): void
    {
        $admin  = $this->adminWithSession();
        $member = User::factory()->create(['role' => 'member', 'is_active' => false]);

        $this->actingAs($admin)
            ->patch(route('admin.members.toggle', $member));

        $this->assertTrue($member->fresh()->is_active);
    }

    // ─── Gyms ──────────────────────────────────────────────────────────────

    #[Test]
    public function admin_can_list_gyms(): void
    {
        $admin = $this->adminWithSession();
        Gym::factory()->count(4)->create();

        $this->actingAs($admin)
            ->get(route('admin.gyms'))
            ->assertOk()
            ->assertViewIs('admin.gyms')
            ->assertViewHas('gyms');
    }

    #[Test]
    public function admin_can_view_gym_create_form(): void
    {
        $admin = $this->adminWithSession();

        $this->actingAs($admin)
            ->get(route('admin.gyms.create'))
            ->assertOk()
            ->assertViewIs('admin.gyms-form');
    }

    #[Test]
    public function admin_can_create_gym(): void
    {
        $admin = $this->adminWithSession();
        $owner = User::factory()->gymOwner()->create();

        $this->actingAs($admin)
            ->post(route('admin.gyms.store'), [
                'owner_id'   => $owner->id,
                'name'       => 'FitZone Dakar',
                'address'    => 'Route des Almadies, Dakar',
                'latitude'   => 14.7200,
                'longitude'  => -17.4700,
                'activities' => ['musculation', 'cardio'],
            ])
            ->assertRedirect(route('admin.gyms'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('gyms', ['name' => 'FitZone Dakar']);
    }

    #[Test]
    public function gym_creation_fails_without_required_fields(): void
    {
        $admin = $this->adminWithSession();

        $this->actingAs($admin)
            ->post(route('admin.gyms.store'), [])
            ->assertSessionHasErrors(['owner_id', 'name', 'address', 'latitude', 'longitude']);
    }

    #[Test]
    public function admin_can_toggle_gym_status(): void
    {
        $admin = $this->adminWithSession();
        $gym   = Gym::factory()->create(['is_active' => true]);

        $this->actingAs($admin)
            ->patch(route('admin.gyms.toggle', $gym))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertFalse($gym->fresh()->is_active);
    }

    // ─── Payments ──────────────────────────────────────────────────────────

    #[Test]
    public function admin_can_view_payments_page(): void
    {
        $admin  = $this->adminWithSession();
        $member = User::factory()->create();
        $plan   = SubscriptionPlan::factory()->mensuel()->create();
        $sub    = Subscription::factory()->for($member)->for($plan, 'plan')->create();
        Payment::factory()->count(3)->create([
            'subscription_id' => $sub->id,
            'user_id'         => $member->id,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.payments'))
            ->assertOk()
            ->assertViewIs('admin.payments');

        $this->assertCount(3, $response->viewData('payments'));
    }

    #[Test]
    public function payments_filterable_by_status(): void
    {
        $admin  = $this->adminWithSession();
        $member = User::factory()->create();
        $plan   = SubscriptionPlan::factory()->mensuel()->create();
        $sub    = Subscription::factory()->for($member)->for($plan, 'plan')->create();

        Payment::factory()->create(['subscription_id' => $sub->id, 'user_id' => $member->id, 'status' => 'completed']);
        Payment::factory()->create(['subscription_id' => $sub->id, 'user_id' => $member->id, 'status' => 'failed']);

        $response = $this->actingAs($admin)
            ->get(route('admin.payments', ['status' => 'completed']));

        $this->assertCount(1, $response->viewData('payments'));
    }

    #[Test]
    public function payments_page_shows_total_revenue(): void
    {
        $admin  = $this->adminWithSession();
        $member = User::factory()->create();
        $plan   = SubscriptionPlan::factory()->mensuel()->create();
        $sub    = Subscription::factory()->for($member)->for($plan, 'plan')->create();

        Payment::factory()->create(['subscription_id' => $sub->id, 'user_id' => $member->id, 'status' => 'completed', 'amount_fcfa' => 25000]);
        Payment::factory()->create(['subscription_id' => $sub->id, 'user_id' => $member->id, 'status' => 'completed', 'amount_fcfa' => 65000]);
        Payment::factory()->failed()->create(['subscription_id' => $sub->id, 'user_id' => $member->id, 'amount_fcfa' => 25000]);

        $response = $this->actingAs($admin)->get(route('admin.payments'));

        $this->assertEquals(90000, $response->viewData('totalRevenue'));
    }
}
