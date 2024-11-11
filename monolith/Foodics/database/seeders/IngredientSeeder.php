<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class IngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Ingredient::insert([
            ['name' => 'Beef', 'total_stock' => 20000, 'current_stock' => 20000],
            ['name' => 'Cheese', 'total_stock' => 5000, 'current_stock' => 5000],
            ['name' => 'Onion', 'total_stock' => 1000, 'current_stock' => 1000],
        ]);
    }
}
