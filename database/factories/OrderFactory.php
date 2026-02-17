<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(['pending', 'confirmed', 'preparing', 'ready', 'delivered', 'cancelled']);
        $paymentStatus = fake()->randomElement(['pending', 'paid', 'failed']);
        $paymentMethod = $paymentStatus === 'pending'
            ? fake()->optional(0.5)->randomElement(['cash', 'transfer'])
            : fake()->randomElement(['cash', 'transfer']);

        if ($status === 'cancelled') {
            $paymentStatus = fake()->randomElement(['pending', 'failed']);
            $paymentMethod = $paymentStatus === 'failed'
                ? fake()->randomElement(['cash', 'transfer'])
                : null;
        }

        return [
            'customer_id' => Customer::factory(),
            'branch_id' => Branch::factory(),
            'status' => $status,
            'total_price' => fake()->randomFloat(2, 5, 300),
            'special_instructions' => fake()->optional(0.3)->sentence(),
            'payment_method' => $paymentMethod,
            'payment_status' => $paymentStatus,
            'delivery_method' => fake()->randomElement(['self-pickup', 'delivery']),
            'order_time' => fake()->dateTimeBetween('-90 days', 'now'),
        ];
    }
}
