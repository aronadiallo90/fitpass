<?php

namespace Tests\Feature\Api;

use App\Models\Gym;
use App\Models\GymActivity;
use App\Models\GymProgram;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GymSearchApiTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();
        $this->owner = User::factory()->create(['role' => 'gym_owner']);
    }

    // ─── GET /api/v1/gyms/search ────────────────────────────────────────────

    #[Test]
    public function anyone_can_search_gyms(): void
    {
        Gym::factory()->count(3)->create(['owner_id' => $this->owner->id, 'is_active' => true]);
        Gym::factory()->create(['owner_id' => $this->owner->id, 'is_active' => false]);

        $response = $this->getJson('/api/v1/gyms/search');

        $response->assertStatus(200)
                 ->assertJsonStructure(['data', 'links', 'meta']);
        $this->assertCount(3, $response->json('data'));
    }

    #[Test]
    public function search_filters_by_name(): void
    {
        Gym::factory()->create(['owner_id' => $this->owner->id, 'name' => 'Iron Gym', 'is_active' => true]);
        Gym::factory()->create(['owner_id' => $this->owner->id, 'name' => 'Yoga Club', 'is_active' => true]);

        $response = $this->getJson('/api/v1/gyms/search?q=Iron');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('Iron Gym', $response->json('data.0.name'));
    }

    #[Test]
    public function search_filters_by_zone(): void
    {
        Gym::factory()->create(['owner_id' => $this->owner->id, 'name' => 'Gym Plateau', 'zone' => 'Plateau', 'is_active' => true]);
        Gym::factory()->create(['owner_id' => $this->owner->id, 'name' => 'Gym Almadies', 'zone' => 'Almadies', 'is_active' => true]);

        $response = $this->getJson('/api/v1/gyms/search?zone=Plateau');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('Gym Plateau', $response->json('data.0.name'));
    }

    #[Test]
    public function search_filters_by_activity(): void
    {
        $yoga = GymActivity::factory()->create(['slug' => 'yoga']);
        $gym  = Gym::factory()->create(['owner_id' => $this->owner->id, 'name' => 'Yoga Studio', 'is_active' => true]);
        $gym->gymActivities()->attach($yoga->id);

        Gym::factory()->create(['owner_id' => $this->owner->id, 'name' => 'Iron Gym', 'is_active' => true]);

        $response = $this->getJson('/api/v1/gyms/search?activity=yoga');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('Yoga Studio', $response->json('data.0.name'));
    }

    #[Test]
    public function search_rejects_invalid_per_page(): void
    {
        $response = $this->getJson('/api/v1/gyms/search?per_page=200');
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['per_page']);
    }

    // ─── GET /api/v1/gyms/{slug}/profile ────────────────────────────────────

    #[Test]
    public function anyone_can_get_gym_profile(): void
    {
        $gym = Gym::factory()->create([
            'owner_id'  => $this->owner->id,
            'slug'      => 'iron-gym-dakar',
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/v1/gyms/iron-gym-dakar/profile');

        $response->assertStatus(200)
                 ->assertJsonStructure(['data' => [
                     'id', 'name', 'slug', 'zone', 'address',
                     'activities', 'photos', 'programs', 'is_open_now',
                 ]]);
        $this->assertEquals('iron-gym-dakar', $response->json('data.slug'));
    }

    #[Test]
    public function profile_returns_404_for_inactive_gym(): void
    {
        Gym::factory()->create([
            'owner_id'  => $this->owner->id,
            'slug'      => 'closed-gym',
            'is_active' => false,
        ]);

        $this->getJson('/api/v1/gyms/closed-gym/profile')
             ->assertStatus(404);
    }

    #[Test]
    public function profile_returns_404_for_unknown_slug(): void
    {
        $this->getJson('/api/v1/gyms/does-not-exist/profile')
             ->assertStatus(404);
    }

    // ─── GET /api/v1/gyms/{slug}/programs ───────────────────────────────────

    #[Test]
    public function anyone_can_get_gym_programs(): void
    {
        $gym = Gym::factory()->create(['owner_id' => $this->owner->id, 'slug' => 'fit-club', 'is_active' => true]);
        GymProgram::factory()->count(2)->create(['gym_id' => $gym->id, 'is_active' => true]);
        GymProgram::factory()->create(['gym_id' => $gym->id, 'is_active' => false]);

        $response = $this->getJson('/api/v1/gyms/fit-club/programs');

        $response->assertStatus(200)
                 ->assertJsonStructure(['data' => [['id', 'name', 'duration_minutes']]]);
        $this->assertCount(2, $response->json('data'));
    }

    #[Test]
    public function programs_returns_404_for_inactive_gym(): void
    {
        Gym::factory()->create(['owner_id' => $this->owner->id, 'slug' => 'closed', 'is_active' => false]);

        $this->getJson('/api/v1/gyms/closed/programs')
             ->assertStatus(404);
    }
}
