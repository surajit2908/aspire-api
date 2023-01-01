<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;

class AuthenticationTest extends TestCase
{
    public function testSuccessfulRegistration()
    {
        $userData = [
            "name" => "John Doe",
            "email" => "doe@example.com",
            "password" => "demo12345",
            "user_type" => "admin"
        ];

        $this->json('POST', 'api/customer/register', $userData, ['Accept' => 'application/json'])
            ->assertStatus(200);
    }

    public function testSuccessfulLogin()
    {
        $user = User::factory()->create([
            'email' => 'sample@test.com',
            'password' => bcrypt('sample123'),
        ]);

        $loginData = [
            'email' => 'sample@test.com',
            'password' => 'sample123',
            'remember_me' => 1
        ];

        $this->json('POST', 'api/login', $loginData, ['Accept' => 'application/json'])
            ->assertStatus(200);

        $this->assertAuthenticated();
    }
}
