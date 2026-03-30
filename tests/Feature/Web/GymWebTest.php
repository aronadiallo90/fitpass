<?php

namespace Tests\Feature\Web;

use App\Models\Gym;
use App\Models\GymCheckin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GymWebTest extends TestCase
{
    use RefreshDatabase;

    private function gymOwnerWithGym(): array
    {
        $owner = User::factory()->gymOwner()->create();
        $gym   = Gym::factory()->create(['owner_id' => $owner->id]);
        return [$owner, $gym];
    }

    // ─── Auth guards ───────────────────────────────────────────────────────

    #[Test]
    public function unauthenticated_user_is_redirected_from_gym_dashboard(): void
    {
        $this->get(route('gym.dashboard'))->assertRedirect(route('login'));
    }

    #[Test]
    public function member_cannot_access_gym_dashboard(): void
    {
        $member = User::factory()->create();
        $this->actingAs($member)->get(route('gym.dashboard'))->assertForbidden();
    }

    // ─── Dashboard ─────────────────────────────────────────────────────────

    #[Test]
    public function gym_owner_sees_their_dashboard(): void
    {
        [$owner] = $this->gymOwnerWithGym();

        $this->actingAs($owner)
            ->get(route('gym.dashboard'))
            ->assertOk()
            ->assertViewIs('gym.dashboard')
            ->assertViewHas('todayCount')
            ->assertViewHas('monthCount')
            ->assertViewHas('todayCheckins')
            ->assertViewHas('recentCheckins');
    }

    #[Test]
    public function gym_dashboard_counts_only_today_checkins(): void
    {
        [$owner, $gym] = $this->gymOwnerWithGym();
        $member        = User::factory()->create();

        // Checkin aujourd'hui
        GymCheckin::factory()->create([
            'gym_id'  => $gym->id,
            'user_id' => $member->id,
            'status'  => 'valid',
        ]);

        // Checkin hier — ne doit pas compter dans todayCount
        GymCheckin::factory()->create([
            'gym_id'     => $gym->id,
            'user_id'    => $member->id,
            'status'     => 'valid',
            'created_at' => now()->subDay(),
        ]);

        $response = $this->actingAs($owner)->get(route('gym.dashboard'));

        $this->assertEquals(1, $response->viewData('todayCount'));
    }

    #[Test]
    public function gym_owner_only_sees_own_gym_data(): void
    {
        [$owner, $gym] = $this->gymOwnerWithGym();

        // Autre gym avec checkins
        $otherGym = Gym::factory()->create();
        GymCheckin::factory()->count(5)->create(['gym_id' => $otherGym->id]);

        // Ma gym — 2 checkins
        GymCheckin::factory()->count(2)->create(['gym_id' => $gym->id]);

        $response = $this->actingAs($owner)->get(route('gym.dashboard'));

        $this->assertEquals(2, $response->viewData('todayCount'));
    }

    // ─── Scan page ─────────────────────────────────────────────────────────

    #[Test]
    public function gym_owner_can_view_scan_page(): void
    {
        [$owner] = $this->gymOwnerWithGym();

        $this->actingAs($owner)
            ->get(route('gym.scan'))
            ->assertOk()
            ->assertViewIs('gym.scan');
    }

    #[Test]
    public function member_cannot_access_scan_page(): void
    {
        $member = User::factory()->create();
        $this->actingAs($member)->get(route('gym.scan'))->assertForbidden();
    }

    // ─── Checkins history ──────────────────────────────────────────────────

    #[Test]
    public function gym_owner_can_view_checkins_history(): void
    {
        [$owner, $gym] = $this->gymOwnerWithGym();
        GymCheckin::factory()->count(3)->create(['gym_id' => $gym->id]);

        $response = $this->actingAs($owner)
            ->get(route('gym.checkins'))
            ->assertOk()
            ->assertViewIs('gym.checkins');

        $this->assertCount(3, $response->viewData('checkins'));
    }

    #[Test]
    public function gym_checkins_are_filterable_by_date(): void
    {
        [$owner, $gym] = $this->gymOwnerWithGym();

        GymCheckin::factory()->create([
            'gym_id'     => $gym->id,
            'created_at' => '2026-03-20 10:00:00',
        ]);
        GymCheckin::factory()->create([
            'gym_id'     => $gym->id,
            'created_at' => '2026-03-25 10:00:00',
        ]);
        GymCheckin::factory()->create([
            'gym_id'     => $gym->id,
            'created_at' => '2026-03-30 10:00:00',
        ]);

        $response = $this->actingAs($owner)
            ->get(route('gym.checkins', ['from' => '2026-03-24', 'to' => '2026-03-26']));

        $response->assertOk();
        $this->assertCount(1, $response->viewData('checkins'));
    }

    #[Test]
    public function gym_owner_cannot_see_other_gyms_checkins(): void
    {
        [$owner, $gym]    = $this->gymOwnerWithGym();
        $otherGym         = Gym::factory()->create();

        GymCheckin::factory()->count(5)->create(['gym_id' => $otherGym->id]);
        GymCheckin::factory()->count(2)->create(['gym_id' => $gym->id]);

        $response = $this->actingAs($owner)->get(route('gym.checkins'));

        $this->assertCount(2, $response->viewData('checkins'));
    }
}
