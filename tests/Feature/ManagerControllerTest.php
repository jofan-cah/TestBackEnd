<?php

namespace Tests\Feature\Api;

use App\Models\Employee;
use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ManagerControllerTest extends TestCase
{
    use RefreshDatabase;

    // Siapkan data yang diperlukan untuk tes
    protected $manager;
    protected $company;
    protected $employee;

    public function setUp(): void
    {
        parent::setUp();

        // Siapkan data perusahaan
        $this->company = Company::factory()->create();

        // Siapkan user dengan role manager
        $this->manager = User::factory()->create([
            'role' => 'manager',
            'company_id' => $this->company->id,
        ]);

        // Siapkan employee untuk diupdate atau dihapus
        $this->employee = Employee::factory()->create([
            'company_id' => $this->company->id,
        ]);
    }

    // Test melihat semua manager
    public function test_manager_can_view_managers()
    {
        // Mendapatkan token JWT untuk manager
        $token = JWTAuth::fromUser($this->manager);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/managers');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => ['id', 'name', 'email', 'role', 'company_id'],
        ]);
    }

    // Test melihat semua employee
    public function test_manager_can_view_employees()
    {
        // Mendapatkan token JWT untuk manager
        $token = JWTAuth::fromUser($this->manager);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/employees');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => ['id', 'name', 'phone_number', 'address', 'company_id'],
        ]);
    }

    // // Test melihat detail employee
    public function test_manager_can_view_employee_details()
    {
        // Mendapatkan token JWT untuk manager
        $token = JWTAuth::fromUser($this->manager);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/employees/' . $this->employee->id);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $this->employee->id,
            'name' => $this->employee->name,
        ]);
    }

    // Test menambah employee
    public function test_manager_can_create_employee()
    {
        // Data employee baru
        $data = [
            'name' => 'New Employee',
            'phone_number' => '9876543210',
            'address' => '123 Test Street',
            'company_id' => $this->company->id,
        ];

        // Mendapatkan token JWT untuk manager
        $token = JWTAuth::fromUser($this->manager);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/employees', $data);

        $response->assertStatus(201);
        $response->assertJsonFragment([
            'name' => 'New Employee',
        ]);

        // Verifikasi data employee di database
        $this->assertDatabaseHas('employees', [
            'name' => 'New Employee',
        ]);
    }

    // Test mengupdate employee
    public function test_manager_can_update_employee()
    {
        // Data update untuk employee
        $data = [
            'phone_number' => '1112223333',
            'address' => '456 Update Street',
        ];

        // Mendapatkan token JWT untuk manager
        $token = JWTAuth::fromUser($this->manager);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/v1/employees/' . $this->employee->id, $data);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'phone_number' => '1112223333',
        ]);

        // Verifikasi data employee di database telah terupdate
        $this->assertDatabaseHas('employees', [
            'phone_number' => '1112223333',
        ]);
    }

    // Test menghapus employee
    public function test_manager_can_delete_employee()
    {
        // Mendapatkan token JWT untuk manager
        $token = JWTAuth::fromUser($this->manager);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/v1/employees/' . $this->employee->id);

        $response->assertStatus(204); // Status 204 untuk berhasil menghapus data
        $this->assertSoftDeleted('employees', [
            'id' => $this->employee->id,

        ]);
    }

    // Test manager mengupdate data diri sendiri
    public function test_manager_can_update_own_info()
    {
        // Data update untuk manager
        $data = [
            'name' => 'Updated Manager Name',
        ];

        // Mendapatkan token JWT untuk manager
        $token = JWTAuth::fromUser($this->manager);
        

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/v1/managers/' . $this->manager->id, $data);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => 'Updated Manager Name',
        ]);

        // Verifikasi data manager di database telah terupdate
        $this->assertDatabaseHas('users', [
            'name' => 'Updated Manager Name',
        ]);
    }
}
