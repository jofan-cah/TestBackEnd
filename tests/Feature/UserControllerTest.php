<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserControllerTest extends TestCase
{
    // use RefreshDatabase;

    // private $manager;
    // private $company;
    // private $employee;

    // protected function setUp(): void
    // {
    //     parent::setUp();

    //     // Buat perusahaan
    //     $this->company = Company::factory()->create();

    //     // Buat manager
    //     $this->manager = User::factory()->create([
    //         'role' => 'manager',
    //         'company_id' => $this->company->id
    //     ]);

    //     // Buat beberapa karyawan
    //     $this->employee = User::factory()->create([
    //         'role' => 'employee',
    //         'company_id' => $this->company->id
    //     ]);
    // }

    // /** @test */
    // public function manager_can_create_employee()
    // {
    //     $token = JWTAuth::fromUser($this->manager);

    //     $employeeData = [
    //         'name' => 'New Employee',
    //         'email' => 'newemployee@example.com',
    //         'phone_number' => '1234567890',
    //         'address' => 'Test Address',
    //         'password' => 'password123'
    //     ];

    //     $response = $this->withHeader('Authorization', 'Bearer ' . $token)
    //         ->postJson('/api/v1/users', $employeeData);

    //     // Debug step: If the test fails, this will help identify the issue
    //     // if ($response->status() !== 201) {
    //     //     dd([
    //     //         'status' => $response->status(),
    //     //         'content' => $response->getContent(),
    //     //         'headers' => $response->headers->all()
    //     //     ]);
    //     // }

    //     $response->assertStatus(201)
    //         ->assertJsonStructure([
    //             'id',
    //             'name',
    //             'email',
    //             'phone_number',
    //             'address'
    //         ]);

    //     $this->assertDatabaseHas('users', [
    //         'name' => 'New Employee',
    //         'email' => 'newemployee@example.com',
    //         'role' => 'employee'
    //     ]);
    // }

    // /** @test */
    // public function employee_cannot_create_new_user()
    // {
    //     $employee = User::factory()->create([
    //         'role' => 'employee',
    //         'company_id' => $this->company->id
    //     ]);

    //     $token = JWTAuth::fromUser($employee);

    //     $employeeData = [
    //         'name' => 'Another Employee',
    //         'email' => 'anotheremployee@example.com',
    //         'phone_number' => '9876543210',
    //         'password' => 'password123'
    //     ];

    //     $response = $this->withHeader('Authorization', 'Bearer ' . $token)
    //         ->postJson('/api/v1/users', $employeeData);

    //     $response->assertStatus(403);
    // }

    // /** @test */
    // public function manager_can_view_all_users_in_company()
    // {
    //     $token = JWTAuth::fromUser($this->manager);

    //     // Tambah beberapa karyawan
    //     User::factory()->count(5)->create([
    //         'company_id' => $this->company->id,
    //         'role' => 'employee'
    //     ]);

    //     $response = $this->withHeader('Authorization', 'Bearer ' . $token)
    //         ->getJson('/api/v1/users');

    //     // Debug step: If the test fails, this will help identify the issue
    //     // if ($response->status() !== 200) {
    //     //     dd([
    //     //         'status' => $response->status(),
    //     //         'content' => $response->getContent(),
    //     //         'headers' => $response->headers->all()
    //     //     ]);
    //     // }

    //     $response->assertStatus(200)
    //         ->assertJsonStructure([
    //             'current_page',
    //             'data' => [
    //                 '*' => ['id', 'name', 'email', 'role']
    //             ],
    //             'per_page',
    //             'total'
    //         ])
    //         ->assertJsonPath('per_page', 2);
    // }

    // /** @test */
    // public function employee_can_only_view_own_details()
    // {
    //     $token = JWTAuth::fromUser($this->employee);

    //     $response = $this->withHeader('Authorization', 'Bearer ' . $token)
    //         ->getJson("/api/v1/users/{$this->employee->id}");

    //     $response->assertStatus(200)
    //         ->assertJson([
    //             'id' => $this->employee->id,
    //             'name' => $this->employee->name
    //         ]);
    // }

    // /** @test */
    // public function user_can_update_own_information()
    // {
    //     $token = JWTAuth::fromUser($this->employee);

    //     $updateData = [
    //         'name' => 'Updated Name',
    //         'phone_number' => '0987654321',
    //         'address' => 'New Address'
    //     ];

    //     $response = $this->withHeader('Authorization', 'Bearer ' . $token)
    //         ->putJson("/api/v1/users/{$this->employee->id}", $updateData);

    //     $response->assertStatus(200)
    //         ->assertJson($updateData);

    //     $this->assertDatabaseHas('users', $updateData + [
    //         'id' => $this->employee->id
    //     ]);
    // }

    // /** @test */
    // public function manager_can_delete_employee()
    // {
    //     $token = JWTAuth::fromUser($this->manager);

    //     $employeeToDelete = User::factory()->create([
    //         'company_id' => $this->company->id,
    //         'role' => 'employee'
    //     ]);

    //     $response = $this->withHeader('Authorization', 'Bearer ' . $token)
    //         ->deleteJson("/api/v1/users/{$employeeToDelete->id}");

    //     $response->assertStatus(204);

    //     $this->assertSoftDeleted('users', [
    //         'id' => $employeeToDelete->id
    //     ]);
    // }
}
