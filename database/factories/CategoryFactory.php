<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => ucfirst(fake()->unique()->words(2, true)),
            'description' => fake()->optional(0.75)->sentence(),
            'display_order' => fake()->numberBetween(0, 50),
            'is_active' => fake()->boolean(90),
            'is_featured' => fake()->boolean(20),
            'image_path' => fake()->optional(0.6)->passthrough('categories/' . fake()->uuid() . '.jpg'),
        ];
    }
}
