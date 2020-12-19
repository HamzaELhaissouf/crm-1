<?php

namespace Database\Seeders;

use App\Models\User;
use App\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // User::factory()->create();
        Product::factory(100)->create();
    }
}
