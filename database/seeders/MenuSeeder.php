<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Item;
use App\Models\ItemImage;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Branch::factory()->count(3)->create();

        $categoryNames = [
            'Coffee',
            'Tea',
            'Breakfast',
            'Lunch',
            'Desserts',
            'Seasonal',
        ];

        collect($categoryNames)->each(function (string $name, int $index): void {
            $category = Category::factory()->create([
                'name' => $name,
                'display_order' => $index + 1,
                'is_active' => true,
                'is_featured' => $index < 2,
            ]);

            $items = Item::factory()
                ->count(fake()->numberBetween(4, 8))
                ->for($category)
                ->create();

            $items->each(function (Item $item): void {
                ItemImage::factory()->for($item)->primary()->create();

                ItemImage::factory()
                    ->count(fake()->numberBetween(0, 2))
                    ->for($item)
                    ->create();
            });
        });
    }
}
