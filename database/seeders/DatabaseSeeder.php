<?php

namespace Database\Seeders;

use App\Models\User;
use App\Product;
use Illuminate\Database\Seeder;
use App\Operation;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create();
        Product::factory(100)
                ->has(Operation::factory()->count(29))        
                ->create();
    }
}
