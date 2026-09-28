<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_register_with_valid_details(): void
    {
        $response = $this->postJson('/register', [
            'username' => 'hardwarefan',
            'email' => 'hardware@example.com',
            'password' => 'SecureAB12!',
            'password_confirmation' => 'SecureAB12!',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('message', 'Your account was created successfully.');

        $user = User::where('name', 'hardwarefan')->firstOrFail();

        $this->assertSame('hardware@example.com', $user->email);
        $this->assertTrue(Hash::check('SecureAB12!', $user->password));
    }

    public function test_an_existing_username_cannot_be_registered_again(): void
    {
        User::factory()->create(['name' => 'hardwarefan']);

        $this->postJson('/register', [
            'username' => 'hardwarefan',
            'email' => 'another@example.com',
            'password' => 'SecureAB12!',
            'password_confirmation' => 'SecureAB12!',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('username');
    }

    #[DataProvider('invalidPasswords')]
    public function test_password_must_meet_every_complexity_requirement(string $password): void
    {
        $this->postJson('/register', [
            'username' => 'hardwarefan',
            'email' => 'hardware@example.com',
            'password' => $password,
            'password_confirmation' => $password,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('password');
    }

    /**
     * @return array<string, array{string}>
     */
    public static function invalidPasswords(): array
    {
        return [
            'fewer than eight characters' => ['AB12!xy'],
            'fewer than two uppercase letters' => ['Securea12!'],
            'fewer than two numbers' => ['SecureAB1!'],
            'missing a special character' => ['SecureAB12'],
        ];
    }
}
