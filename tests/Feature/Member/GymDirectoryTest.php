<?php

namespace Tests\Feature\Member;

use App\Models\Gym;
use App\Models\GymActivity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GymDirectoryTest extends TestCase
{
    use RefreshDatabase;

    private User $member;

    protected function setUp(): void
    {
        parent::setUp();
        $this->member = User::factory()->create(['role' => 'member']);
    }

    #[Test]
    public function member_can_view_gyms_index(): void
    {
        GymActivity::factory()->count(3)->create();

        $this->actingAs($this->member)
             ->get(route('member.gyms'))
             ->assertStatus(200)
             ->assertSee('Trouver une salle');
    }

    #[Test]
    public function gyms_index_shows_activities_and_zones(): void
    {
        GymActivity::factory()->create(['name' => 'Yoga', 'slug' => 'yoga']);

        $this->actingAs($this->member)
             ->get(route('member.gyms'))
             ->assertStatus(200)
             ->assertSee('Yoga')
             ->assertSee('Plateau');
    }

    #[Test]
    public function guest_cannot_view_gyms_directory(): void
    {
        $this->get(route('member.gyms'))
             ->assertRedirect(route('login'));
    }

    #[Test]
    public function member_can_view_gym_show_page(): void
    {
        $owner = User::factory()->create(['role' => 'gym_owner']);
        Gym::factory()->create(['owner_id' => $owner->id, 'slug' => 'iron-gym', 'is_active' => true]);

        $this->actingAs($this->member)
             ->get(route('member.gyms.show', 'iron-gym'))
             ->assertStatus(200)
             ->assertSee('gymProfile');
    }

    #[Test]
    public function gym_show_page_passes_slug_to_view(): void
    {
        $owner = User::factory()->create(['role' => 'gym_owner']);
        Gym::factory()->create(['owner_id' => $owner->id, 'slug' => 'test-gym', 'is_active' => true]);

        $response = $this->actingAs($this->member)
                         ->get(route('member.gyms.show', 'test-gym'));

        $response->assertStatus(200)
                 ->assertSee('test-gym');
    }
}
