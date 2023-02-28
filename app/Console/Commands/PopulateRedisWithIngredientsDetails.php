<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class PopulateRedisWithIngredientsDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:PopulateRedisWithIngredientsDetails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $redis = Redis::connection();
        $redis->transaction(function ($redis) {
            $products = Product::with('ingredients')->get();
            $ingredients_data = [];
            foreach ($products as $product) {
                foreach ($product->ingredients as $ingredient) {
                    $ingredients_data[$ingredient->name] = $ingredient->pivot->ingredient_quantity;
                }
                $redis->set($product->id, json_encode($ingredients_data));
            }
        });
    }
}
