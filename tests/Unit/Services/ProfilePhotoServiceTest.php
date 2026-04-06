<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\ProfilePhotoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProfilePhotoServiceTest extends TestCase
{
    use RefreshDatabase;

    private ProfilePhotoService $service;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->service = app(ProfilePhotoService::class);
    }

    #[Test]
    public function it_stores_a_photo_and_updates_user(): void
    {
        $user = User::factory()->create(['role' => 'member']);
        $file = UploadedFile::fake()->image('photo.png', 800, 600);

        $path = $this->service->store($user, $file);

        $this->assertEquals("profiles/{$user->id}.jpg", $path);
        Storage::disk('public')->assertExists($path);
        $this->assertEquals($path, $user->fresh()->profile_photo_path);
    }

    #[Test]
    public function it_replaces_existing_photo_on_new_upload(): void
    {
        $user = User::factory()->create([
            'role'               => 'member',
            'profile_photo_path' => 'profiles/old.jpg',
        ]);
        // Créer l'ancien fichier
        Storage::disk('public')->put('profiles/old.jpg', 'fake');

        $file = UploadedFile::fake()->image('new.png', 400, 400);
        $this->service->store($user, $file);

        // L'ancien fichier doit être supprimé
        Storage::disk('public')->assertMissing('profiles/old.jpg');
        // Le nouveau fichier doit exister au path fixe
        Storage::disk('public')->assertExists("profiles/{$user->id}.jpg");
    }

    #[Test]
    public function it_deletes_photo_and_clears_db(): void
    {
        $user = User::factory()->create(['role' => 'member']);
        $file = UploadedFile::fake()->image('photo.png');
        $path = $this->service->store($user, $file);

        $this->service->delete($user);

        Storage::disk('public')->assertMissing($path);
        $this->assertNull($user->fresh()->profile_photo_path);
    }

    #[Test]
    public function it_does_nothing_on_delete_when_no_photo(): void
    {
        $user = User::factory()->create(['role' => 'member', 'profile_photo_path' => null]);

        // Ne doit pas lever d'exception
        $this->service->delete($user);

        $this->assertNull($user->fresh()->profile_photo_path);
    }

    #[Test]
    public function user_returns_empty_photo_url_when_no_photo(): void
    {
        $user = User::factory()->create(['profile_photo_path' => null]);

        $this->assertEquals('', $user->profile_photo_url);
    }

    #[Test]
    public function user_generates_correct_initials(): void
    {
        $user = User::factory()->make(['name' => 'Mamadou Diallo']);
        $this->assertEquals('MD', $user->initials);

        $user2 = User::factory()->make(['name' => 'Fatou']);
        $this->assertEquals('F', $user2->initials);

        $user3 = User::factory()->make(['name' => 'Jean-Marie Dupont']);
        $this->assertEquals('JD', $user3->initials);
    }
}
