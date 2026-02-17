<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Activity>
 */
class ActivityFactory extends Factory
{
    protected $model = Activity::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'action' => fake()->randomElement([
                'auth.login',
                'auth.logout',
                'order.created',
                'order.updated',
                'item.updated',
                'menu.published',
            ]),
            'details' => fake()->boolean(70) ? [
                'ip' => fake()->ipv4(),
                'user_agent' => fake()->userAgent(),
                'note' => fake()->sentence(),
            ] : null,
        ];
    }
}
