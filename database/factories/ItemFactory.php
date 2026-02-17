<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    protected $model = Item::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => ucfirst(fake()->words(2, true)),
            'price' => fake()->randomFloat(2, 2, 120),
            'category_id' => Category::factory(),
            'description' => fake()->optional(0.8)->sentence(),
            'is_active' => fake()->boolean(90),
            'is_featured' => fake()->boolean(20),
            'display_order' => fake()->numberBetween(0, 100),
        ];
    }
}
