<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $ingredients = Ingredient::all()->pluck('id','name')->toArray();
        $product = Product::create([
            'name' => 'Regular Burger',
        ]);
        $product->ingredients()->sync([
            $ingredients['Beef']=>['ingredient_quantity' => 150],
            $ingredients['Cheese']=>['ingredient_quantity' => 30],
            $ingredients['Onion']=>['ingredient_quantity' => 20],
            ]);

        $product = Product::create([
            'name' => 'Jumbo Burger',
        ]);
        $product->ingredients()->sync([
            $ingredients['Beef']=>['ingredient_quantity' => 250],
            $ingredients['Cheese']=>['ingredient_quantity' => 60],
            $ingredients['Onion']=>['ingredient_quantity' => 40],
        ]);

        $product = Product::create([
            'name' => 'Extra Cheese Burger',
        ]);
        $product->ingredients()->sync([
            $ingredients['Beef']=>['ingredient_quantity' => 150],
            $ingredients['Cheese']=>['ingredient_quantity' => 60],
            $ingredients['Onion']=>['ingredient_quantity' => 20],
        ]);

        $product = Product::create([
            'name' => 'Extra Onion Burger',
        ]);
        $product->ingredients()->sync([
            $ingredients['Beef']=>['ingredient_quantity' => 150],
            $ingredients['Cheese']=>['ingredient_quantity' => 30],
            $ingredients['Onion']=>['ingredient_quantity' => 40],
        ]);

        $product = Product::create([
            'name' => 'No Cheese Burger',
        ]);
        $product->ingredients()->sync([
            $ingredients['Beef']=>['ingredient_quantity' => 150],
            $ingredients['Onion']=>['ingredient_quantity' => 20],
        ]);

        $product = Product::create([
            'name' => 'No Onions Burger',
        ]);
        $product->ingredients()->sync([
            $ingredients['Beef']=>['ingredient_quantity' => 150],
            $ingredients['Cheese']=>['ingredient_quantity' => 30],
        ]);
    }
}
