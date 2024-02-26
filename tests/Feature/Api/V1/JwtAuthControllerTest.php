<?php

namespace Tests\Feature\Api\V1;

use App\Enums\RolesEnum;
use App\Models\User;
use Tests\TestCase;

/**
 * @group api
 * @group auth
 */
class JwtAuthControllerTest extends TestCase
{
    const API_AUTH_GUARD = 'api';
    const API_VERSION = 'v1';

    public function test_login(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson(
            route(sprintf('api.%s.auth.login', self::API_VERSION)),
            [
                'email' => $user->email,
                'password' => $user->email,
            ]
        );

        $response->assertOk();
        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in',
        ]);
        $this->assertValidToken($response['access_token'], $response['token_type']);
    }

    public function test_login_with_invalid_credentials(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson(
            route(sprintf('api.%s.auth.login', self::API_VERSION)),
            [
                'email' => $user->email . $user->email,
                'password' => $user->email,
            ]
        );

        $response->assertStatus(401);
        $response->assertJsonStructure([
            'message',
        ]);
    }

    public function test_me_not_authorized(): void
    {
        $response = $this->postJson(
            route(sprintf('api.%s.auth.me', self::API_VERSION)),
        );

        $response->assertStatus(401);
        $response->assertJsonStructure([
            'message',
        ]);
    }

    public function test_me_authorized(): void
    {
        $this->actingAsRandomUser();
        $response = $this->postJson(
            route(sprintf('api.%s.auth.me', self::API_VERSION)),
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'name',
            'email',
            'profile_image_url',
        ]);
    }

    public function test_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson(
            route(sprintf('api.%s.auth.login', self::API_VERSION)),
            [
                'email' => $user->email,
                'password' => $user->email,
            ]
        );

        $token = $response['access_token'];
        $tokenType = $response['token_type'];

        $response = $this->postJson(
            route(sprintf('api.%s.auth.logout', self::API_VERSION)),
            [],
            [
                'Authorization' => $tokenType . " " . $token,
            ]
        );

        $response->assertOk();
        $response->assertJsonStructure([
            'message',
        ]);
        $this->assertInvalidToken($token, $tokenType);
    }

    public function test_logout_not_authorized(): void
    {
        $response = $this->postJson(
            route(sprintf('api.%s.auth.logout', self::API_VERSION)),
            [],
            [
                'Authorization' => 'asdasdasd.asdasdasd',
            ]
        );

        $response->assertStatus(401);
        $response->assertJsonStructure([
            'message',
        ]);
    }

    public function test_refresh(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson(
            route(sprintf('api.%s.auth.login', self::API_VERSION)),
            [
                'email' => $user->email,
                'password' => $user->email,
            ]
        );

        $response = $this->postJson(
            route(sprintf('api.%s.auth.refresh', self::API_VERSION)),
            [],
            [
                'Authorization' => $response['token_type'] . " " . $response['access_token'],
            ]
        );

        $response->assertOk();
        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in',
        ]);
        $this->assertValidToken($response['access_token'], $response['token_type']);
    }

    public function test_refresh_not_auth(): void
    {
        $response = $this->postJson(
            route(sprintf('api.%s.auth.refresh', self::API_VERSION)),
            [],
            [
                'Authorization' => 'asdasdasd.asdasdasd',
            ]
        );

        $response->assertStatus(401);
        $response->assertJsonStructure([
            'message',
        ]);
    }

    protected function actingAsRandomUser(?RolesEnum $role = null): void
    {
        $user = User::factory()->create();
        if (!empty($role)) {
            $user->assignRole($role);
        }
        $this->actingAs($user, self::API_AUTH_GUARD);
    }

    private function assertValidToken(?string $token, ?string $tokenType = 'bearer'): void
    {
        $this->assertFalse(empty($token) || empty($tokenType));

        $response = $this->postJson(
            route(sprintf('api.%s.auth.me', self::API_VERSION)),
            [],
            [
                'Authorization' => $tokenType . " " . $token,
            ]
        );
        $response->assertOk();
    }

    private function assertInvalidToken(string $token, string $tokenType = "bearer"): void
    {
        $response = $this->postJson(
            route(sprintf('api.%s.auth.me', self::API_VERSION)),
            [],
            [
                'Authorization' => $tokenType . " " . $token,
            ]
        );
        $response->assertStatus(401);
    }
}
