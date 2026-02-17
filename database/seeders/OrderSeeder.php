<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $branches = Branch::query()->get();
        $items = Item::query()->get();

        if ($branches->isEmpty() || $items->isEmpty()) {
            return;
        }

        $customers = Customer::factory()->count(25)->create();

        Order::factory()
            ->count(80)
            ->make()
            ->each(function (Order $order) use ($branches, $items, $customers): void {
                $order->customer()->associate($customers->random());
                $order->branch()->associate($branches->random());
                $order->save();

                $lineItemCount = fake()->numberBetween(1, min(5, $items->count()));
                $selectedItems = $items->random($lineItemCount);

                $selectedItems->each(function (Item $item) use ($order): void {
                    OrderItem::query()->create([
                        'order_id' => $order->id,
                        'item_id' => $item->id,
                    ]);
                });

                $totalPrice = $selectedItems->sum(fn (Item $item): float => (float) $item->price);

                $order->update([
                    'total_price' => round($totalPrice, 2),
                ]);


            });
    }
}
