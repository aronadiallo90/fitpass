<?php

namespace Tests\Unit\Services;

use App\Models\Gym;
use App\Models\GymActivity;
use App\Models\User;
use App\Services\GymSearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GymSearchServiceTest extends TestCase
{
    use RefreshDatabase;

    private GymSearchService $service;
    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(GymSearchService::class);
        $this->owner   = User::factory()->create(['role' => 'gym_owner']);
    }

    #[Test]
    public function it_returns_only_active_gyms(): void
    {
        Gym::factory()->create(['owner_id' => $this->owner->id, 'name' => 'Salle Active',   'is_active' => true]);
        Gym::factory()->create(['owner_id' => $this->owner->id, 'name' => 'Salle Inactive', 'is_active' => false]);

        $results = $this->service->search([]);

        $this->assertCount(1, $results);
        $this->assertEquals('Salle Active', $results->first()->name);
    }

    #[Test]
    public function it_filters_by_name(): void
    {
        Gym::factory()->create(['owner_id' => $this->owner->id, 'name' => 'Iron Gym Plateau',  'is_active' => true]);
        Gym::factory()->create(['owner_id' => $this->owner->id, 'name' => 'Fitness Club Plus', 'is_active' => true]);

        $results = $this->service->search(['q' => 'Iron']);

        $this->assertCount(1, $results);
        $this->assertEquals('Iron Gym Plateau', $results->first()->name);
    }

    #[Test]
    public function it_filters_by_zone(): void
    {
        Gym::factory()->create(['owner_id' => $this->owner->id, 'name' => 'Gym Plateau',   'zone' => 'Plateau',   'is_active' => true]);
        Gym::factory()->create(['owner_id' => $this->owner->id, 'name' => 'Gym Almadies',  'zone' => 'Almadies',  'is_active' => true]);
        Gym::factory()->create(['owner_id' => $this->owner->id, 'name' => 'Gym Mermoz',    'zone' => 'Mermoz',    'is_active' => true]);

        $results = $this->service->search(['zone' => 'Plateau']);

        $this->assertCount(1, $results);
        $this->assertEquals('Gym Plateau', $results->first()->name);
    }

    #[Test]
    public function it_filters_by_activity_slug(): void
    {
        $yoga  = GymActivity::factory()->create(['name' => 'Yoga', 'slug' => 'yoga', 'icon' => '🧘']);
        $muscu = GymActivity::factory()->create(['name' => 'Musculation', 'slug' => 'muscu', 'icon' => '🏋️']);

        $gymYoga  = Gym::factory()->create(['owner_id' => $this->owner->id, 'name' => 'Yoga Center', 'is_active' => true]);
        $gymMuscu = Gym::factory()->create(['owner_id' => $this->owner->id, 'name' => 'Iron Gym',    'is_active' => true]);

        $gymYoga->activities()->attach($yoga->id);
        $gymMuscu->activities()->attach($muscu->id);

        $results = $this->service->search(['activity' => 'yoga']);

        $this->assertCount(1, $results);
        $this->assertEquals('Yoga Center', $results->first()->name);
    }

    #[Test]
    public function it_combines_name_and_zone_filters(): void
    {
        Gym::factory()->create(['owner_id' => $this->owner->id, 'name' => 'Iron Gym Plateau',  'zone' => 'Plateau',  'is_active' => true]);
        Gym::factory()->create(['owner_id' => $this->owner->id, 'name' => 'Iron Gym Almadies', 'zone' => 'Almadies', 'is_active' => true]);
        Gym::factory()->create(['owner_id' => $this->owner->id, 'name' => 'Yoga Center',       'zone' => 'Plateau',  'is_active' => true]);

        $results = $this->service->search(['q' => 'Iron', 'zone' => 'Plateau']);

        $this->assertCount(1, $results);
        $this->assertEquals('Iron Gym Plateau', $results->first()->name);
    }

    #[Test]
    public function it_returns_paginated_results(): void
    {
        Gym::factory()->count(20)->create(['owner_id' => $this->owner->id, 'is_active' => true]);

        $results = $this->service->search(['per_page' => 5]);

        $this->assertCount(5, $results->items());
        $this->assertEquals(20, $results->total());
    }

    #[Test]
    public function it_returns_empty_when_no_match(): void
    {
        Gym::factory()->create(['owner_id' => $this->owner->id, 'name' => 'Fitness Club', 'is_active' => true]);

        $results = $this->service->search(['q' => 'inexistant']);

        $this->assertCount(0, $results);
    }

    #[Test]
    public function it_caches_results(): void
    {
        Cache::shouldReceive('remember')
            ->once()
            ->andReturn([]);

        $this->service->search(['q' => 'test']);
    }

    #[Test]
    public function it_skips_haversine_without_coordinates(): void
    {
        // Sans lat/lng → tri par nom, pas d'erreur SQL
        Gym::factory()->count(3)->create(['owner_id' => $this->owner->id, 'is_active' => true]);

        $results = $this->service->search([]);

        $this->assertCount(3, $results);
    }
}
