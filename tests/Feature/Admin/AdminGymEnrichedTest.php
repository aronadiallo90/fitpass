<?php

namespace Tests\Feature\Admin;

use App\Models\Gym;
use App\Models\GymActivity;
use App\Models\GymPhoto;
use App\Models\GymProgram;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminGymEnrichedTest extends TestCase
{
    use RefreshDatabase;

    private User  $admin;
    private User  $owner;
    private Gym   $gym;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create([
            'role'                    => 'admin',
            'two_factor_secret'       => encrypt('fakesecret'),
            'two_factor_confirmed_at' => now(),
        ]);
        $this->owner = User::factory()->create(['role' => 'gym_owner']);
        $this->gym   = Gym::factory()->create(['owner_id' => $this->owner->id]);

        // Marquer la session 2FA comme vérifiée (pattern du projet)
        $this->withSession(['two_factor_verified' => true]);
    }

    // ─── Edit page ──────────────────────────────────────────────────────────

    #[Test]
    public function admin_can_view_enriched_edit_page(): void
    {
        GymActivity::factory()->count(3)->create();
        GymProgram::factory()->count(2)->create(['gym_id' => $this->gym->id]);

        $this->actingAs($this->admin)
             ->get(route('admin.gyms.edit', $this->gym))
             ->assertStatus(200)
             ->assertSee('Informations')
             ->assertSee('Horaires')
             ->assertSee('Activités')
             ->assertSee('Programmes');
    }

    // ─── Update enrichi ─────────────────────────────────────────────────────

    #[Test]
    public function admin_can_update_gym_with_zone_and_whatsapp(): void
    {
        $this->actingAs($this->admin)
             ->put(route('admin.gyms.update', $this->gym), [
                 'owner_id'       => $this->owner->id,
                 'name'           => 'Iron Gym Plateau',
                 'address'        => '12 rue Félix Faure',
                 'latitude'       => 14.692,
                 'longitude'      => -17.447,
                 'zone'           => 'Plateau',
                 'phone_whatsapp' => '+221 77 123 45 67',
             ])
             ->assertRedirect(route('admin.gyms.edit', $this->gym));

        $this->assertDatabaseHas('gyms', [
            'id'             => $this->gym->id,
            'zone'           => 'Plateau',
            'phone_whatsapp' => '+221 77 123 45 67',
        ]);
    }

    #[Test]
    public function admin_can_sync_gym_activities(): void
    {
        $yoga  = GymActivity::factory()->create(['slug' => 'yoga']);
        $muscu = GymActivity::factory()->create(['slug' => 'muscu']);

        $this->actingAs($this->admin)
             ->put(route('admin.gyms.update', $this->gym), [
                 'owner_id'     => $this->owner->id,
                 'name'         => $this->gym->name,
                 'address'      => $this->gym->address,
                 'latitude'     => $this->gym->latitude,
                 'longitude'    => $this->gym->longitude,
                 'activity_ids' => [$yoga->id, $muscu->id],
             ]);

        $this->assertCount(2, $this->gym->fresh()->gymActivities);
    }

    // ─── Programmes ─────────────────────────────────────────────────────────

    #[Test]
    public function admin_can_add_program(): void
    {
        $this->actingAs($this->admin)
             ->post(route('admin.gyms.programs.store', $this->gym), [
                 'name'             => 'Yoga du matin',
                 'duration_minutes' => 60,
                 'is_active'        => 1,
             ])
             ->assertRedirect();

        $this->assertDatabaseHas('gym_programs', [
            'gym_id' => $this->gym->id,
            'name'   => 'Yoga du matin',
        ]);
    }

    #[Test]
    public function admin_can_update_program(): void
    {
        $program = GymProgram::factory()->create(['gym_id' => $this->gym->id, 'name' => 'Ancien nom']);

        $this->actingAs($this->admin)
             ->put(route('admin.gyms.programs.update', [$this->gym, $program]), [
                 'name'             => 'Nouveau nom',
                 'duration_minutes' => 45,
             ])
             ->assertRedirect();

        $this->assertEquals('Nouveau nom', $program->fresh()->name);
    }

    #[Test]
    public function admin_can_delete_program(): void
    {
        $program = GymProgram::factory()->create(['gym_id' => $this->gym->id]);

        $this->actingAs($this->admin)
             ->delete(route('admin.gyms.programs.destroy', [$this->gym, $program]))
             ->assertRedirect();

        $this->assertDatabaseMissing('gym_programs', ['id' => $program->id]);
    }

    #[Test]
    public function admin_cannot_delete_program_of_another_gym(): void
    {
        $otherGym    = Gym::factory()->create(['owner_id' => $this->owner->id]);
        $program     = GymProgram::factory()->create(['gym_id' => $otherGym->id]);

        $this->actingAs($this->admin)
             ->delete(route('admin.gyms.programs.destroy', [$this->gym, $program]))
             ->assertStatus(403);
    }

    #[Test]
    public function admin_cannot_update_program_of_another_gym(): void
    {
        $otherGym = Gym::factory()->create(['owner_id' => $this->owner->id]);
        $program  = GymProgram::factory()->create(['gym_id' => $otherGym->id]);

        $this->actingAs($this->admin)
             ->put(route('admin.gyms.programs.update', [$this->gym, $program]), [
                 'name'             => 'Hack',
                 'duration_minutes' => 60,
             ])
             ->assertStatus(403);
    }

    #[Test]
    public function updating_program_without_is_active_deactivates_it(): void
    {
        $program = GymProgram::factory()->create(['gym_id' => $this->gym->id, 'is_active' => true]);

        // Soumettre sans is_active (checkbox décochée)
        $this->actingAs($this->admin)
             ->put(route('admin.gyms.programs.update', [$this->gym, $program]), [
                 'name'             => $program->name,
                 'duration_minutes' => $program->duration_minutes,
                 // is_active absent = checkbox décochée
             ]);

        $this->assertFalse($program->fresh()->is_active);
    }

    #[Test]
    public function member_cannot_access_admin_programs_route(): void
    {
        $member  = User::factory()->create(['role' => 'member']);
        $program = GymProgram::factory()->create(['gym_id' => $this->gym->id]);

        $this->actingAs($member)
             ->delete(route('admin.gyms.programs.destroy', [$this->gym, $program]))
             ->assertForbidden();
    }

    #[Test]
    public function member_cannot_access_admin_photos_route(): void
    {
        Storage::fake('public');
        $member = User::factory()->create(['role' => 'member']);

        $this->actingAs($member)
             ->post(route('admin.gyms.photos.store', $this->gym), [
                 'photo' => UploadedFile::fake()->image('test.png'),
             ])
             ->assertForbidden();
    }

    #[Test]
    public function admin_cannot_delete_photo_of_another_gym(): void
    {
        Storage::fake('public');
        $otherGym = Gym::factory()->create(['owner_id' => $this->owner->id]);
        $photo    = GymPhoto::factory()->create(['gym_id' => $otherGym->id, 'photo_storage_key' => null]);

        $this->actingAs($this->admin)
             ->delete(route('admin.gyms.photos.destroy', [$this->gym, $photo]))
             ->assertStatus(403);
    }

    // ─── Photos ─────────────────────────────────────────────────────────────

    #[Test]
    public function admin_can_upload_photo(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin)
             ->post(route('admin.gyms.photos.store', $this->gym), [
                 'photo'    => UploadedFile::fake()->image('gym.png', 800, 600),
                 'is_cover' => 1,
             ])
             ->assertRedirect();

        $this->assertCount(1, $this->gym->fresh()->photos);
        $this->assertTrue($this->gym->fresh()->photos->first()->is_cover);
    }

    #[Test]
    public function admin_can_delete_photo(): void
    {
        Storage::fake('public');

        $photo = GymPhoto::factory()->create(['gym_id' => $this->gym->id, 'photo_storage_key' => null]);

        $this->actingAs($this->admin)
             ->delete(route('admin.gyms.photos.destroy', [$this->gym, $photo]))
             ->assertRedirect();

        $this->assertDatabaseMissing('gym_photos', ['id' => $photo->id]);
    }

    #[Test]
    public function admin_can_set_cover_photo(): void
    {
        Storage::fake('public');

        $photo1 = GymPhoto::factory()->create(['gym_id' => $this->gym->id, 'is_cover' => true,  'photo_storage_key' => null]);
        $photo2 = GymPhoto::factory()->create(['gym_id' => $this->gym->id, 'is_cover' => false, 'photo_storage_key' => null]);

        $this->actingAs($this->admin)
             ->patch(route('admin.gyms.photos.cover', [$this->gym, $photo2]))
             ->assertRedirect();

        $this->assertFalse($photo1->fresh()->is_cover);
        $this->assertTrue($photo2->fresh()->is_cover);
    }
}
