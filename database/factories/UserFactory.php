<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Company;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        $company = Company::factory()->create();

        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('password123'),
            'role' => $this->faker->randomElement(['manager', 'employee', 'super_admin']),
            'company_id' => $company->id
        ];
    }
}
