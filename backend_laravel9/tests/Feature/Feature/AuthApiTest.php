<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_login_and_logout(): void
    {
        $registerResponse = $this->postJson('/api/register', [
            'name' => 'Ana Silva',
            'email' => 'ana@example.com',
            'password' => 'secret123',
        ]);

        $registerResponse
            ->assertCreated()
            ->assertJsonStructure([
                'token',
                'user' => ['id', 'name', 'email'],
            ]);

        $loginResponse = $this->postJson('/api/login', [
            'email' => 'ana@example.com',
            'password' => 'secret123',
        ]);

        $token = $loginResponse->json('token');

        $loginResponse
            ->assertOk()
            ->assertJsonPath('user.email', 'ana@example.com');

        $this->withToken($token)
            ->postJson('/api/logout')
            ->assertOk()
            ->assertJsonPath('message', 'Logged out successfully.');
    }
}