<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase {
    use DatabaseTransactions;

    public function testRequiredFieldsForRegistration() {
        $this->json('POST', 'api/register', ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'name' => ['The name field is required.'],
                    'email' => ['The email field is required.'],
                    'password' => ['The password field is required.']
                ]
            ]);
    }

    public function testRepeatPassword() {
        $userData = [
            "name" => "John Doe",
            "email" => "doe@example.com",
            "password" => "demo12345"
        ];

        $this->json('POST', 'api/register', $userData, ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJson([
                "message" => "The given data was invalid.",
                "errors" => [
                    "password" => ["The password confirmation does not match."]
                ]
            ]);
    }

    public function testSuccessfulRegistration() {
        $userData = [
            "name" => "John Doe",
            "email" => "doe@example.com",
            "password" => "demo12345",
            "password_confirmation" => "demo12345"
        ];

        $this->json('POST', 'api/register', $userData, ['Accept' => 'application/json'])
            ->assertStatus(201)
            ->assertJsonStructure([
                "user" => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                ],
                "access_token"
            ]);
    }

    public function testRequiredFieldsForLogin() {
        $this->json('POST', 'api/login', ['Accept' => 'application/json'])
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                'email' => ['The email field is required.'],
                'password' => ['The password field is required.']
            ]
        ]);
    }

    public function testSuccessfulLogin() {
        $this->withoutExceptionHandling();

        $user = User::factory()->create([
            'email' => 'admin@admin.com',
            'password' => Hash::make('haslo123')
        ]);

        $body = [
            'email' => $user->email,
            'password' => 'haslo123'
        ];

        $this->json('POST', 'api/login', $body, ['Accept' => 'application/json'])
        ->assertStatus(200)
        ->assertJsonStructure([
            "user" => [
                'id',
                'name',
                'email',
                'created_at',
                'updated_at',
            ],
            "access_token"
        ]);
    }
}
