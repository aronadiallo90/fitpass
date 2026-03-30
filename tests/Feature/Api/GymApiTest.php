<?php

namespace Tests\Feature\Api;

use App\Models\Gym;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GymApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function anyone_can_list_active_gyms(): void
    {
        // Arrange
        Gym::factory()->count(3)->create(['is_active' => true]);
        Gym::factory()->create(['is_active' => false]);

        // Act
        $response = $this->getJson('/api/v1/gyms');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'links', 'meta']);
        $this->assertCount(3, $response->json('data'));
    }

    #[Test]
    public function anyone_can_get_gym_details(): void
    {
        // Arrange
        $gym = Gym::factory()->create(['is_active' => true]);

        // Act
        $response = $this->getJson("/api/v1/gyms/{$gym->id}");

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'name', 'address', 'latitude', 'longitude', 'activities']]);
    }

    #[Test]
    public function inactive_gym_returns_404(): void
    {
        // Arrange
        $gym = Gym::factory()->inactive()->create();

        // Act
        $response = $this->getJson("/api/v1/gyms/{$gym->id}");

        // Assert
        $response->assertStatus(404);
    }

    #[Test]
    public function geojson_endpoint_returns_valid_feature_collection(): void
    {
        // Arrange
        Gym::factory()->count(2)->create(['is_active' => true]);

        // Act
        $response = $this->getJson('/api/v1/gyms/geojson');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure(['type', 'features'])
            ->assertJsonPath('type', 'FeatureCollection');
        $this->assertCount(2, $response->json('features'));
    }
}
