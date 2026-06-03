<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MobileRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_registration_creates_user_and_returns_201(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'name' => 'Brendan Lawton',
            'email' => 'brendan@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])
            ->assertCreated()
            ->assertJson(['message' => 'Registration successful. Please verify your email.']);

        $this->assertDatabaseHas('users', ['email' => 'brendan@example.com']);
    }

    public function test_duplicate_email_returns_422(): void
    {
        User::factory()->create(['email' => 'brendan@example.com']);

        $this->postJson('/api/v1/auth/register', [
            'name' => 'Brendan Lawton',
            'email' => 'brendan@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_missing_fields_return_422(): void
    {
        $this->postJson('/api/v1/auth/register', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_password_confirmation_mismatch_returns_422(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'name' => 'Brendan Lawton',
            'email' => 'brendan@example.com',
            'password' => 'password',
            'password_confirmation' => 'different-password',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['password']);
    }
}
