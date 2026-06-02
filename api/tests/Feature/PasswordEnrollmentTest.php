<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordEnrollmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_social_only_user_can_enrol_a_password(): void
    {
        $user = User::factory()->socialOnly()->create();

        $this->actingAs($user)
            ->postJson('/api/v1/user/password', [
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
            ])
            ->assertOk();

        $this->assertTrue(Hash::check('new-password-123', $user->fresh()->password));
    }

    public function test_enrolment_is_rejected_when_user_already_has_a_password(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/api/v1/user/password', [
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['password']);
    }

    public function test_enrolment_is_rejected_when_password_confirmation_does_not_match(): void
    {
        $user = User::factory()->socialOnly()->create();

        $this->actingAs($user)
            ->postJson('/api/v1/user/password', [
                'password' => 'new-password-123',
                'password_confirmation' => 'different-password',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['password']);
    }

    public function test_enrolment_requires_authentication(): void
    {
        $this->postJson('/api/v1/user/password', [
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])->assertUnauthorized();
    }
}
