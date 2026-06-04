<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class SocialAuthTest extends TestCase
{
    use RefreshDatabase;

    private function mockSocialiteUser(string $id, string $email, string $name): void
    {
        $socialiteUser = Mockery::mock(\Laravel\Socialite\Two\User::class);
        $socialiteUser->shouldReceive('getId')->andReturn($id);
        $socialiteUser->shouldReceive('getEmail')->andReturn($email);
        $socialiteUser->shouldReceive('getName')->andReturn($name);

        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('user')->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);
    }

    public function test_redirect_redirects_to_google(): void
    {
        $response = $this->get('/auth/google/redirect');

        $response->assertRedirect();
        $this->assertStringContainsString('accounts.google.com', $response->headers->get('Location'));
    }

    public function test_callback_redirects_to_login_with_error_when_oauth_fails(): void
    {
        Socialite::shouldReceive('driver->user')->andThrow(new \Exception('OAuth failed'));

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect(config('app.frontend_url').'/login?error=social_auth_failed');
    }

    public function test_callback_logs_in_returning_social_user_without_creating_duplicates(): void
    {
        $user = User::factory()->socialOnly()->create(['email' => 'returning@example.com']);
        $user->socialIdentities()->create(['provider' => 'google', 'provider_user_id' => 'google-789']);
        $this->mockSocialiteUser('google-789', 'returning@example.com', 'Returning User');

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect(config('app.frontend_url'));
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('social_identities', 1);
    }

    public function test_callback_auto_links_google_account_to_existing_password_user(): void
    {
        $existingUser = User::factory()->create(['email' => 'existing@example.com']);
        $this->mockSocialiteUser('google-456', 'existing@example.com', 'Existing User');

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect(config('app.frontend_url'));

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('social_identities', [
            'user_id' => $existingUser->id,
            'provider' => 'google',
            'provider_user_id' => 'google-456',
        ]);
    }

    public function test_callback_creates_user_and_social_identity_for_new_google_account(): void
    {
        $this->mockSocialiteUser('google-123', 'new@example.com', 'New User');

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect(config('app.frontend_url'));

        $user = User::where('email', 'new@example.com')->firstOrFail();
        $this->assertNotNull($user->email_verified_at);
        $this->assertNull($user->password);
        $this->assertSame('New User', $user->name);

        $this->assertDatabaseHas('social_identities', [
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_user_id' => 'google-123',
        ]);
    }

    public function test_callback_authenticates_user_in_session(): void
    {
        $this->mockSocialiteUser('google-123', 'new@example.com', 'New User');

        $this->get('/auth/google/callback');

        $this->assertAuthenticated();
    }

    public function test_callback_auto_link_preserves_existing_password(): void
    {
        $existingUser = User::factory()->create(['email' => 'existing@example.com']);
        $originalPassword = $existingUser->getAttributes()['password'];
        $this->mockSocialiteUser('google-456', 'existing@example.com', 'Existing User');

        $this->get('/auth/google/callback');

        $this->assertSame($originalPassword, $existingUser->fresh()->getAttributes()['password']);
    }

    public function test_callback_does_not_authenticate_user_when_oauth_fails(): void
    {
        Socialite::shouldReceive('driver->user')->andThrow(new \Exception('OAuth failed'));

        $this->get('/auth/google/callback');

        $this->assertGuest();
    }
}
