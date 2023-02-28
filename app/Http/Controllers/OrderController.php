<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Repositories\OrderRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Redis;

class OrderController extends Controller
{
    private $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(OrderRequest $request)
    {
        // Gets response from repository
        $response = $this->orderRepository->create($request->validated());

        // Checks if status is success
        if ($response['success']) {
            unset($response['success']);
            // If status is success -> true, sends the response as success
            return response()->json($response);
        }
        unset($response['success']);
        // If status is success -> false, sends the response as failed
        return response()->json($response, 400);
    }

    public function test()
    {
        $redis = Redis::connection();

        // Getting ingredients details for required products from redis
        return $redis->mget([1]);
    }

}
