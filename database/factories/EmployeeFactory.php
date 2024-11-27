<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'phone_number' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'company_id' => Company::factory(), // Membuat company baru jika belum ada

        ];
    }
}
