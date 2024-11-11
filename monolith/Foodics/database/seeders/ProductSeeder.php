<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Ingredient;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $burger = Product::create(['id' => 1, 'name' => 'Burger']);

        // Link ingredients with quantities needed for a Burger
        $burger->ingredients()->attach([
            Ingredient::where('name', 'Beef')->first()->id => ['quantity' => 150],
            Ingredient::where('name', 'Cheese')->first()->id => ['quantity' => 30],
            Ingredient::where('name', 'Onion')->first()->id => ['quantity' => 20],
        ]);
    }
}
