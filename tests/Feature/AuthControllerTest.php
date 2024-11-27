<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_registration()
    {
        // Data untuk registrasi
        $data = [
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => 'password123', // Password harus lebih dari 6 karakter
        ];

        // Mengirimkan POST request ke endpoint register
        $response = $this->postJson('/api/v1/register', $data);

        // Memastikan status code 201 (Created)
        $response->assertStatus(201);

        // Memastikan bahwa user baru terdaftar di database
        $this->assertDatabaseHas('users', [
            'email' => $data['email'],
        ]);

        // Memastikan password yang disimpan adalah hash
        $user = User::where('email', $data['email'])->first();
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    /**
     * Test login endpoint.
     *
     * @return void
     */
    public function test_user_login()
    {
        // Pertama, buat user yang sudah terdaftar
        $user = User::factory()->create([
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Data untuk login
        $data = [
            'email' => 'superadmin@example.com',
            'password' => 'password123',
        ];

        // Mengirimkan POST request untuk login
        $response = $this->postJson('/api/v1/login', $data);

        // Memastikan status code 200 (OK)
        $response->assertStatus(200);

        // Memastikan token JWT ada di response
        $response->assertJsonStructure([
            'token',
        ]);
    }

    // Test login with wrong email
    public function test_user_login_wrong_email()
    {
        $data = [
            'email' => 'wrongemail@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/v1/login', $data);

        // Verifikasi status code 401 (Unauthorized)
        $response->assertStatus(401);
        $response->assertJson(['error' => 'Unauthorized']);
    }

    // Test login with wrong password
    public function test_user_login_wrong_password()
    {
        $user = User::factory()->create([
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password123'),
        ]);

        $data = [
            'email' => 'superadmin@example.com',
            'password' => 'wrongpassword',
        ];

        $response = $this->postJson('/api/v1/login', $data);

        // Verifikasi status code 401 (Unauthorized)
        $response->assertStatus(401);
        $response->assertJson(['error' => 'Unauthorized']);
    }

    // Test login with missing credentials
    public function test_user_login_missing_credentials()
    {
        $response = $this->postJson('/api/v1/login', []);

        // Verifikasi status code 422 (Unprocessable Entity) jika data tidak valid
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email', 'password']);
    }

    // Test login with empty password
    public function test_user_login_empty_password()
    {
        $user = User::factory()->create([
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password123'),
        ]);

        $data = [
            'email' => 'superadmin@example.com',
            'password' => '',
        ];

        $response = $this->postJson('/api/v1/login', $data);

        // Verifikasi status code 422 jika password kosong
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
    }
}
