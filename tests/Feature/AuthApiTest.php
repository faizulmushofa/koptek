<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_via_api(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);

        $response->assertCreated()
            ->assertJson([
                'message' => 'success',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
        ]);
    }

    public function test_user_can_login_and_receive_token(): void
    {
        User::factory()->create([
            'email' => 'jane@example.com',
            'password' => 'password123',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'jane@example.com',
            'password' => 'password123',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'message',
                'data' => [
                    'message',
                    'token',
                ],
            ]);
    }

    public function test_authenticated_user_can_access_profile_and_logout(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test_token')->plainTextToken;

        $profileResponse = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/user');

        $profileResponse->assertOk()
            ->assertJsonPath('data.email', $user->email);

        $logoutResponse = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/logout');

        $logoutResponse->assertOk()
            ->assertJson(['message' => 'success']);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
