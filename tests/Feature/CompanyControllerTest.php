<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use Tymon\JWTAuth\Facades\JWTAuth;

class CompanyControllerTest extends TestCase
{
    use RefreshDatabase;

    private $superAdmin;
    private $company;

    protected function setUp(): void
    {
        parent::setUp();

        // Buat super admin
        $this->superAdmin = User::factory()->create([
            'role' => 'super_admin'
        ]);

        // Buat perusahaan
        $this->company = Company::factory()->create();
    }



    /** @test */
    public function test_super_admin_can_create_company()
    {
        // Pastikan super admin terautentikasi dengan benar
        $token = JWTAuth::fromUser($this->superAdmin);

        // Persiapkan data perusahaan
        $companyData = [
            'name' => 'New Test Company',
            'email' => 'newcompany@example.com', // Sesuaikan email yang digunakan
            'phone_number' => '1234567890',
        ];

        // Kirim permintaan untuk membuat perusahaan dengan header Authorization yang benar
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/companies', $companyData);

        // Verifikasi status code dan data perusahaan yang dikirim
        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'New Test Company',
                'email' => 'newcompany@example.com', // Sesuaikan email yang digunakan
                'phone_number' => '1234567890',
            ]);

        // Verifikasi akun manager dibuat
        $this->assertDatabaseHas('users', [
            'email' => 'newcompany@example.com', // Sesuaikan email yang digunakan
            'role' => 'manager',
            'company_id' => $response->json('company.id'),
        ]);

        // Verifikasi perusahaan dibuat
        $this->assertDatabaseHas('companies', $companyData);
    }


    /** @test */
    public function non_super_admin_cannot_create_company()
    {
        // Buat user biasa
        $user = User::factory()->create([
            'role' => 'manager'
        ]);

        $token = JWTAuth::fromUser($user);

        $companyData = [
            'name' => 'Unauthorized Company',
            'email' => 'unauthorized@example.com',
            'phone_number' => '9876543210'
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/companies', $companyData);

        $response->assertStatus(403);
    }

    // /** @test */
    public function can_fetch_companies_with_pagination()
    {
        // Buat beberapa perusahaan
        Company::factory()->count(5)->create();

        $token = JWTAuth::fromUser($this->superAdmin);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/companies');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'current_page',
                'data' => [
                    '*' => ['id', 'name', 'email']
                ],
                'per_page',
                'total'
            ])
            ->assertJsonPath('per_page', 2);
    }

    /** @test */
    public function can_update_company_as_super_admin()
    {
        $token = JWTAuth::fromUser($this->superAdmin);

        $updateData = [
            'name' => 'Updated Company Name',
            'email' => 'updated@example.com',
            'phone_number' => '0987654321'
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson("/api/v1/companies/{$this->company->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson($updateData);

        $this->assertDatabaseHas('companies', $updateData);
    }

    /** @test */
    public function can_delete_company_as_super_admin()
    {
        $token = JWTAuth::fromUser($this->superAdmin);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/v1/companies/{$this->company->id}");

        $response->assertStatus(204);

        $this->assertSoftDeleted('companies', [
            'id' => $this->company->id
        ]);
    }

    // Test view list of companies
    public function test_list_companies()
    {
        // Siapkan user dan autentikasi
        $user = User::factory()->create([
            'role' => 'super_admin',
        ]);
        // $token = JWTAuth::fromUser($this->superAdmin);
        $token = JWTAuth::fromUser($this->superAdmin);
        // Siapkan data perusahaan
        Company::factory()->create([
            'name' => 'Company A',
            'email' => 'a@company.com',
        ]);
        Company::factory()->create([
            'name' => 'Company B',
            'email' => 'b@company.com',
        ]);

        // Kirim request GET untuk mendapatkan daftar perusahaan
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/companies');

        // Verifikasi status code 200 (OK)
        $response->assertStatus(200);

        // Verifikasi ada 2 perusahaan dalam response
        $response->assertJsonCount(2, 'data');
    }
    public function test_show_company()
    {
        // Siapkan perusahaan dan autentikasi
        $company = Company::factory()->create();
        $user = User::factory()->create([
            'role' => 'super_admin',
        ]);
        $token = JWTAuth::fromUser($this->superAdmin);

        // Kirim request GET untuk melihat detail perusahaan
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/companies/' . $company->id);

        // Verifikasi status code 200 (OK)
        $response->assertStatus(200);

        // Verifikasi bahwa data perusahaan ada di response
        $response->assertJsonFragment([
            'name' => $company->name,
            'email' => $company->email,
        ]);
    }


    // Test update company
    public function test_update_company()
    {
        // Siapkan perusahaan untuk diupdate dan autentikasi
        $company = Company::factory()->create();
        $user = User::factory()->create([
            'role' => 'super_admin',
        ]);
        $token = JWTAuth::fromUser($this->superAdmin);

        // Data update perusahaan
        $data = [
            'name' => 'Updated Company',
            'email' => 'updated@company.com',
        ];

        // Kirim request PUT untuk mengupdate perusahaan
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/v1/companies/' . $company->id, $data);

        // Verifikasi status code 200 (OK)
        $response->assertStatus(200);

        // Verifikasi bahwa data perusahaan di database telah terupdate
        $this->assertDatabaseHas('companies', [
            'name' => 'Updated Company',
            'email' => 'updated@company.com',
        ]);
    }
}
