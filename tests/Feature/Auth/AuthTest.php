<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    // ─── Register ──────────────────────────────────────────────────────────

    #[Test]
    public function user_can_register_with_phone(): void
    {
        $response = $this->post(route('register.store'), [
            'name'                  => 'Awa Diop',
            'phone'                 => '+221771234567',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('member.dashboard'));
        $this->assertDatabaseHas('users', [
            'phone' => '+221771234567',
            'role'  => 'member',
        ]);
    }

    #[Test]
    public function register_fails_with_duplicate_phone(): void
    {
        User::factory()->create(['phone' => '+221771234567']);

        $response = $this->post(route('register.store'), [
            'name'                  => 'Autre',
            'phone'                 => '+221771234567',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('phone');
    }

    #[Test]
    public function register_fails_without_password_confirmation(): void
    {
        $response = $this->post(route('register.store'), [
            'name'                  => 'Awa',
            'phone'                 => '+221779999999',
            'password'              => 'password123',
            'password_confirmation' => 'different',
        ]);

        $response->assertSessionHasErrors('password');
    }

    // ─── Login ─────────────────────────────────────────────────────────────

    #[Test]
    public function member_can_login_with_phone(): void
    {
        $user = User::factory()->create([
            'phone'    => '+221771234567',
            'password' => bcrypt('password123'),
            'role'     => 'member',
        ]);

        $response = $this->post(route('login.store'), [
            'phone'    => '+221771234567',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('member.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function login_fails_with_wrong_password(): void
    {
        User::factory()->create([
            'phone'    => '+221771234567',
            'password' => bcrypt('correct'),
        ]);

        $response = $this->post(route('login.store'), [
            'phone'    => '+221771234567',
            'password' => 'wrong',
        ]);

        $response->assertSessionHasErrors('phone');
        $this->assertGuest();
    }

    #[Test]
    public function admin_is_redirected_to_admin_dashboard_after_login(): void
    {
        User::factory()->create([
            'phone'    => '+221700000001',
            'password' => bcrypt('password123'),
            'role'     => 'admin',
        ]);

        $response = $this->post(route('login.store'), [
            'phone'    => '+221700000001',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
    }

    #[Test]
    public function gym_owner_is_redirected_to_gym_dashboard_after_login(): void
    {
        User::factory()->create([
            'phone'    => '+221770000010',
            'password' => bcrypt('password123'),
            'role'     => 'gym_owner',
        ]);

        $response = $this->post(route('login.store'), [
            'phone'    => '+221770000010',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('gym.dashboard'));
    }

    // ─── Accès protégés ────────────────────────────────────────────────────

    #[Test]
    public function unauthenticated_user_cannot_access_member_dashboard(): void
    {
        $this->get(route('member.dashboard'))
            ->assertRedirect(route('login'));
    }

    #[Test]
    public function member_cannot_access_admin_dashboard(): void
    {
        $member = User::factory()->create(['role' => 'member']);

        $this->actingAs($member)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    #[Test]
    public function member_cannot_access_gym_dashboard(): void
    {
        $member = User::factory()->create(['role' => 'member']);

        $this->actingAs($member)
            ->get(route('gym.dashboard'))
            ->assertForbidden();
    }

    #[Test]
    public function gym_owner_cannot_access_admin_dashboard(): void
    {
        $gymOwner = User::factory()->create(['role' => 'gym_owner']);

        $this->actingAs($gymOwner)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    // ─── Logout ────────────────────────────────────────────────────────────

    #[Test]
    public function authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
