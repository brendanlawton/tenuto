<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MobileSocialTokenTest extends TestCase
{
    use RefreshDatabase;

    private function fakeValidTokenInfo(string $googleId, string $email, string $name): void
    {
        Http::fake([
            'oauth2.googleapis.com/tokeninfo*' => Http::response([
                'sub' => $googleId,
                'email' => $email,
                'name' => $name,
                'aud' => config('services.google.ios_client_id'),
                'email_verified' => 'true',
            ], 200),
        ]);
    }

    public function test_valid_google_token_for_new_user_creates_user_and_returns_bearer_token(): void
    {
        $this->fakeValidTokenInfo('google-123', 'new@example.com', 'New User');

        $this->postJson('/api/v1/auth/google/token', ['id_token' => 'valid-id-token'])
            ->assertOk()
            ->assertJsonStructure(['token']);

        $this->assertDatabaseHas('users', ['email' => 'new@example.com']);
        $this->assertDatabaseHas('social_identities', ['provider' => 'google', 'provider_user_id' => 'google-123']);
    }

    public function test_valid_google_token_for_returning_user_returns_token_without_duplicates(): void
    {
        $user = User::factory()->socialOnly()->create(['email' => 'returning@example.com']);
        $user->socialIdentities()->create(['provider' => 'google', 'provider_user_id' => 'google-456']);
        $this->fakeValidTokenInfo('google-456', 'returning@example.com', 'Returning User');

        $this->postJson('/api/v1/auth/google/token', ['id_token' => 'valid-id-token'])
            ->assertOk()
            ->assertJsonStructure(['token']);

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('social_identities', 1);
    }

    public function test_valid_google_token_auto_links_to_existing_account_with_matching_email(): void
    {
        $existing = User::factory()->create(['email' => 'existing@example.com']);
        $this->fakeValidTokenInfo('google-789', 'existing@example.com', 'Existing User');

        $this->postJson('/api/v1/auth/google/token', ['id_token' => 'valid-id-token'])
            ->assertOk()
            ->assertJsonStructure(['token']);

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('social_identities', [
            'user_id' => $existing->id,
            'provider' => 'google',
            'provider_user_id' => 'google-789',
        ]);
    }

    public function test_valid_android_google_token_returns_bearer_token(): void
    {
        Http::fake([
            'oauth2.googleapis.com/tokeninfo*' => Http::response([
                'sub' => 'google-123',
                'email' => 'new@example.com',
                'name' => 'New User',
                'aud' => config('services.google.client_id'),
                'email_verified' => 'true',
            ], 200),
        ]);

        $this->postJson('/api/v1/auth/google/token', ['id_token' => 'valid-id-token'])
            ->assertOk()
            ->assertJsonStructure(['token']);
    }

    public function test_invalid_google_token_returns_422(): void
    {
        Http::fake([
            'oauth2.googleapis.com/tokeninfo*' => Http::response(['error' => 'invalid_token'], 400),
        ]);

        $this->postJson('/api/v1/auth/google/token', ['id_token' => 'bad-token'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['id_token']);
    }
}
