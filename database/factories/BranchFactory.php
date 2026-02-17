<?php

namespace Database\Factories;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Branch>
 */
class BranchFactory extends Factory
{
    protected $model = Branch::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company() . ' Branch',
            'address' => fake()->optional(0.8)->address(),
            'contact_info' => [
                'phone' => fake()->phoneNumber(),
                'email' => fake()->companyEmail(),
            ],
        ];
    }
}
