<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->count(8)->create();

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@menu.test',
        ]);

        $this->call([
            MenuSeeder::class,
            OrderSeeder::class,
            ActivitySeeder::class,
        ]);
    }
}
