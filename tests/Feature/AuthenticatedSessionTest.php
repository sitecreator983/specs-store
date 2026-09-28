<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticatedSessionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_sign_in_with_registered_username_and_password(): void
    {
        $user = User::factory()->create([
            'name' => 'hardwarefan',
            'password' => Hash::make('SecureAB12!'),
        ]);

        $response = $this->postJson('/login', [
            'username' => 'hardwarefan',
            'password' => 'SecureAB12!',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('redirect', route('home'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_is_redirected_to_admin_products_after_sign_in(): void
    {
        $admin = User::factory()->create([
            'name' => 'admin',
            'password' => Hash::make('123'),
            'is_admin' => true,
        ]);

        $response = $this->postJson('/login', [
            'username' => 'admin',
            'password' => '123',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('redirect', route('admin.products'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_user_cannot_sign_in_with_invalid_credentials(): void
    {
        User::factory()->create([
            'name' => 'hardwarefan',
            'password' => Hash::make('SecureAB12!'),
        ]);

        $response = $this->postJson('/login', [
            'username' => 'hardwarefan',
            'password' => 'wrong-password',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('username');

        $this->assertGuest();
    }

    public function test_user_can_sign_out(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/logout');

        $response
            ->assertOk()
            ->assertJsonPath('redirect', route('home'));

        $this->assertGuest();
    }
}
