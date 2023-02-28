<?php

namespace App\Repositories;

use App\Jobs\ingregientLessThanFiftyPercent;
use App\Models\Ingredient;
use App\Models\Order;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class OrderRepository implements OrderRepositoryInterface
{

    public function getProductsIdsAndQuantities($products_details): array
    {
        foreach ($products_details as $product_details) {
            // Extracting product ids from order
            $product_ids[] = $product_details['product_id'];

            // Extracting product quantity from order
            $order_product_quantity[] = $product_details['quantity'];
        }
        return [$product_ids, $order_product_quantity];
    }

    public function getFormattedIngredientsDataFromRedis($order_product_quantity, $ingredients_data): array
    {

        // Iterating through products to extract ingredients and their quantity
        $order_product_quantity_index = 0;
        $accumulative_ingredients_utilized = [];


        foreach ($ingredients_data as $ingredient_data) {
            $ingredient_data = json_decode($ingredient_data);
            foreach ($ingredient_data as $name => $quantity) {
                if (!array_key_exists($name, $accumulative_ingredients_utilized)) {
                    $accumulative_ingredients_utilized[$name] = 0;
                }
                $accumulative_ingredients_utilized[$name] += $quantity * $order_product_quantity[$order_product_quantity_index];
            }
            $order_product_quantity_index += 1;
        }
        return $accumulative_ingredients_utilized;
    }

    public function formatIngredientsDataFromDbAndAvailableResources($accumulative_ingredients_utilized, $ingredients): array
    {
        $updated_ingredients = [];

        // Looping through ingredients to calculate and update stock utilized quantities
        foreach ($ingredients as $ingredient) {
            $ingredient['stock_utilized_quantity'] = $ingredient['stock_utilized_quantity'] + $accumulative_ingredients_utilized[$ingredient['name']];

            // Checking if the amount ordered is in the stock
            if (($ingredient['stock_utilized_quantity'] - $ingredient['stock_base_quantity']) >= 0) {

                // Returning if order quantity is greater than available stock quantity
                return ['success' => false, 'message' => 'The ' . strtolower($ingredient['name']) . ' amount is not enough for this order.', 'errors' => [strtolower($ingredient['name']) => ['Insufficient resource.']]];
            }


            // Checking if any ingredient quantity has dropped to less than 50%
            if (($ingredient['stock_utilized_quantity'] / $ingredient['stock_base_quantity']) * 100 >= 50 && !$ingredient['low_stock_warning_sent']) {

                // Pushing a job to send email of ingredient that's left less than 50%
                ingregientLessThanFiftyPercent::dispatch($ingredient['name']);

                // Setting mail sent check to true
                $ingredient['low_stock_warning_sent'] = 1;
            }

            $updated_ingredients[] = $ingredient;
        }
        return $updated_ingredients;
    }

    public function createOrdersArray($products_details, $order): array
    {
        // Initializing empty array to store order data along with order id saved in orders table
        $order_with_order_id = [];
        foreach ($products_details as $product_details) {

            // Collecting order data in an object
            $updated_order['order_id'] = $order->id;
            $updated_order['product_id'] = $product_details['product_id'];
            $updated_order['quantity'] = $product_details['quantity'];

            // Merging collected data
            $order_with_order_id[] = $updated_order;
        }
        return $order_with_order_id;
    }

    public function create($products_data): array
    {
        // Beginning transactions, using manually for exception control on food limit reached
        DB::beginTransaction();

        try {

            // Getting products IDs and each product order quantity
            [$product_ids, $order_product_quantity] = $this->getProductsIdsAndQuantities($products_data['products']);

            // Establishing Redis Connection
            $redis = Redis::connection();
            // Getting ingredients details for required products from redis
            $ingredients_data = $redis->mget($product_ids);

            // Getting products IDs and each product order quantity
            $accumulative_ingredients_utilized = $this->getFormattedIngredientsDataFromRedis($order_product_quantity, $ingredients_data);

            // Getting all ingredients of limited columns from Database
            $ingredients = Ingredient::all('name', 'stock_base_quantity', 'stock_utilized_quantity', 'low_stock_warning_sent')->toArray();

            // Update ingredients quantity
            $updated_ingredients = $this->formatIngredientsDataFromDbAndAvailableResources($accumulative_ingredients_utilized, $ingredients);

            if (array_key_exists('success', $updated_ingredients)){
                // Rolling back database if order quantity is greater than available stock quantity
                DB::rollBack();
                return $updated_ingredients;
            }

            // Bulk updating the data
            Ingredient::upsert($updated_ingredients, ['stock_utilized_quantity', 'low_stock_warning_sent']);

            // Saving order to create an order instance, no parameter is passed as Table has only ID in it
            $order = Order::create();

            // Creating orders array from data
            $order_with_order_id = $this->createOrdersArray($products_data['products'], $order);

            // Saving merged order data in Order Products table
            $order->orderProducts()->createMany($order_with_order_id);

            // Committing the changes
            DB::commit();
        } catch (\Exception $e) {
            // Rolling back in case if any error
            DB::rollBack();
            return ['success' => false, 'message' => 'Something went wrong, please try again later.', 'errors' => [strtolower($ingredient['name']) => ['Insufficient resource.']]];

        }

        return ['success' => true, 'message' => 'Order placed successfully.'];
    }
}
