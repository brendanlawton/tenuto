<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_endpoint_includes_has_password_true_when_password_is_set(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->getJson('/api/v1/user')
            ->assertOk()
            ->assertJsonPath('has_password', true);
    }

    public function test_user_endpoint_includes_has_password_false_for_social_only_user(): void
    {
        $user = User::factory()->socialOnly()->create();

        $this->actingAs($user)
            ->getJson('/api/v1/user')
            ->assertOk()
            ->assertJsonPath('has_password', false);
    }
}
