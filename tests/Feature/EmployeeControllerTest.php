<?php

namespace Tests\Feature\Api;

use App\Models\Employee;
use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class EmployeeControllerTest extends TestCase
{
    use RefreshDatabase;

    private $employee;
    private $fellowEmployees;

    protected function setUp(): void
    {
        parent::setUp();

        // Buat pengguna employee yang akan login
        $this->employee = User::factory()->create([
            'role' => 'employee',
        ]);

        // Buat rekan-rekan employee lainnya
        $this->fellowEmployees = User::factory()->count(3)->create([
            'role' => 'employee',
        ]);

        // Tambahkan pengguna dengan role lain (tidak harus terlihat oleh employee)
        User::factory()->create([
            'role' => 'manager',
        ]);
    }

    /** @test */
    public function test_employee_can_view_fellow_employees()
    {
        $company = Company::factory()->create();

        $employee1 = User::factory()->create(['role' => 'employee', 'company_id' => $company->id]);
        $employee2 = User::factory()->create(['role' => 'employee', 'company_id' => $company->id]);
        $employee3 = User::factory()->create(['role' => 'employee', 'company_id' => $company->id]);
        $manager = User::factory()->create(['role' => 'manager', 'company_id' => $company->id]);

        $token = JWTAuth::fromUser($employee1);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/fellow-employees');

        $response->assertStatus(200);
        $response->assertJsonCount(2); // Employee2 dan Employee3 (Employee1 dikecualikan)
    }

    public function test_employee_can_view_fellow_employee_details()
    {
        $company = Company::factory()->create();

        $employee1 = User::factory()->create(['role' => 'employee', 'company_id' => $company->id]);
        $employee2 = User::factory()->create(['role' => 'employee', 'company_id' => $company->id]);

        $token = JWTAuth::fromUser($employee1);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/fellow-employees/' . $employee2->id);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $employee2->id,
                'name' => $employee2->name,
            ]);
    }

    public function test_employee_cannot_view_fellow_employee_from_other_company()
    {
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();

        $employee1 = User::factory()->create(['role' => 'employee', 'company_id' => $company1->id]);
        $employee2 = User::factory()->create(['role' => 'employee', 'company_id' => $company2->id]);

        $token = JWTAuth::fromUser($employee1);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/fellow-employees/' . $employee2->id);

        $response->assertStatus(401);
    }
}
