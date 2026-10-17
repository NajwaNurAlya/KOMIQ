<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    public function test_guest_cannot_view_profile(): void
    {
        $response = $this->get('/profile');
        $response->assertRedirect(route('login'));
    }

    public function test_user_can_view_profile(): void
    {
        $user = User::factory()->create([
            'name' => 'Irma Nurhayati',
            'role' => 'user',
            'coin_balance' => 150,
        ]);

        $response = $this->actingAs($user)->get('/profile');
        $response->assertStatus(200);
        $response->assertSee('Irma Nurhayati');
        $response->assertSee('150');
    }

    public function test_user_can_update_profile_info(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put('/profile', [
            'name' => 'Nama Baru Irma',
            'email' => $user->email,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nama Baru Irma',
        ]);
    }

    public function test_user_can_update_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword123'),
        ]);

        $response = $this->actingAs($user)->put('/profile/password', [
            'current_password' => 'oldpassword123',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
        ]);

        $response->assertSessionHas('success');
        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }

    public function test_user_cannot_access_admin_reviews_without_admin_role(): void
    {
        $regularUser = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($regularUser)->get('/admin/chapter-reviews');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_reviews(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/chapter-reviews');
        $response->assertStatus(200);
    }
}
