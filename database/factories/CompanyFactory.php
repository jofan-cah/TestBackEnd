<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Company;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition()
    {
        return [
            'name' => $this->faker->unique()->company,
            'email' => $this->faker->unique()->companyEmail,
            'phone_number' => $this->faker->unique()->phoneNumber
        ];
    }
}
