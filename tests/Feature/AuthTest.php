<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase, WithFaker;

    public function test_register_with_valid_data()
    {
        $data = [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/register', $data);
        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'name', 'email', 'role_id', 'created_at', 'updated_at'],
                'token',
            ]);

        $this->assertDatabaseHas('users', ['email' => 'testuser@example.com']);
    }

    public function test_register_with_invalid_email()
    {
        $data = [
            'name' => 'Test User 1',
            'email' => 'not-an-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/register', $data);
        $response->assertStatus(422)
            ->assertJsonFragment(['The email field must be a valid email address.']);
    }

    public function test_register_with_incorrect_password_confirmation()
    {
        $data = [
            'name' => 'Test User 1',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password',
        ];

        $response = $this->postJson('/api/register', $data);
        $response->assertStatus(422)
            ->assertJsonFragment(["The password field confirmation does not match."]);
    }
}
