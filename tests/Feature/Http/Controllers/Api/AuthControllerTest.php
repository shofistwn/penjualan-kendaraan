<?php

namespace Tests\Feature\Api;

use App\Services\VehicleService;
use App\Repositories\VehicleRepository;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
  use WithFaker;

  protected $vehicleService;
  protected static $token;

  protected function setUp(): void
  {
    parent::setUp();
  }

  public function testRegisterSuccess()
  {
    $payload = ['name' => 'John Doe', 'email' => $this->faker->email, 'password' => 'password'];
    $response = $this->postJson('/api/v1/auth/register', $payload);

    $response->assertStatus(200);
    $response->assertJsonStructure([
      'success',
      'message',
      'data' => [
        '_id',
        'email',
        'name',
        'password',
      ],
    ]);
  }

  public function testRegisterValidationFailed()
  {
    $payload = [];
    $response = $this->postJson('/api/v1/auth/register', $payload);

    $response->assertStatus(422);
    $response->assertJson([
      "success" => false,
      "message" => "Validasi gagal",
      "errors" => [
        "name" => [
          "The name field is required."
        ],
        "email" => [
          "The email field is required."
        ],
        "password" => [
          "The password field is required."
        ]
      ]
    ]);
  }

  public function testLoginSuccess()
  {
    $payload = ['email' => 'user@mail.com', 'password' => 'password'];
    $response = $this->postJson('/api/v1/auth/login', $payload);

    $responseData = $response->json();
    self::$token = $responseData['data']['access_token'];

    $response->assertStatus(200);
    $response->assertJsonStructure([
      'success',
      'message',
      'data' => [
        'access_token',
        'token_type',
        'expires_in',
      ],
    ]);
  }

  public function testLoginValidationFailed()
  {
    $payload = [];
    $response = $this->postJson('/api/v1/auth/login', $payload);

    $response->assertStatus(422);
    $response->assertJson([
      "success" => false,
      "message" => "Validasi gagal",
      "errors" => [
        "email" => [
          "The email field is required."
        ],
        "password" => [
          "The password field is required."
        ]
      ]
    ]);
  }

  public function testLoginInvalidCredentials()
  {
    $payload = ['email' => 'user@mail.com', 'password' => 'salahpassword'];
    $response = $this->postJson('/api/v1/auth/login', $payload);

    $response->assertStatus(401);
    $response->assertJson([
      "success" => false,
      "message" => "Password atau email salah"
    ]);
  }

  public function testLogoutSuccess()
  {
    $response = $this->withHeaders([
      'Authorization' => 'Bearer ' . self::$token,
    ])->postJson('/api/v1/auth/logout');

    $response->assertStatus(200);
  }

  public function testLogoutInvalidToken()
  {
    $response = $this->withHeaders([
      'Authorization' => 'Bearer ' . 'salahhtoken',
    ])->postJson('/api/v1/auth/logout');

    $response->assertStatus(200);
    $response->assertJson([
      "status" => "Token is Invalid"
    ]);
  }
}