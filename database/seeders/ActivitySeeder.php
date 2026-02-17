<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = User::query()->get();

        if ($users->isEmpty()) {
            $users = User::factory()->count(5)->create();
        }

        $orderIds = Order::query()->pluck('id');

        $users->each(function (User $user) use ($orderIds): void {
            Activity::factory()
                ->count(fake()->numberBetween(5, 12))
                ->for($user)
                ->state(function () use ($orderIds): array {
                    if ($orderIds->isEmpty() || fake()->boolean(30)) {
                        return ['details' => null];
                    }

                    return [
                        'details' => [
                            'order_id' => $orderIds->random(),
                            'note' => fake()->sentence(),
                        ],
                    ];
                })
                ->create();
        });
    }
}
