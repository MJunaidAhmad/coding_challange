<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use Illuminate\Database\Seeder;

class IngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Ingredient::insert([
            [
                'name' => 'Beef',
                'stock_base_quantity' => 20000, // Keeping stock in grams to simplify calculations
                'stock_utilized_quantity' => 0,
                'low_stock_warning_sent' => false,
            ],
            [
                'name' => 'Cheese',
                'stock_base_quantity' => 5000,
                'stock_utilized_quantity' => 0,
                'low_stock_warning_sent' => false,
            ],
            [
                'name' => 'Onion',
                'stock_base_quantity' => 1000,
                'stock_utilized_quantity' => 0,
                'low_stock_warning_sent' => false,
            ],
        ]);
    }
}
