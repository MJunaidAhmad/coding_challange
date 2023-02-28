<?php


it('returns array of product IDs and quantities', function () {
    $response = (new App\Repositories\OrderRepository)->getProductsIdsAndQuantities([
        [
            "product_id" => 1,
            "quantity" => 1
        ]
    ]);
    $this->assertEquals([
        [
            1
        ],
        [
            1
        ]
    ], $response);
});

it('returns array of ingredients', function () {
    $response = (new App\Repositories\OrderRepository)->getFormattedIngredientsDataFromRedis([1], [
        '{"Beef":150,"Cheese":30,"Onion":20}',
    ]);
    $this->assertEquals([
        "Beef" => 150,
        "Cheese" => 30,
        "Onion" => 20
    ], $response);
});

it('returns array of updated ingredients quantity', function () {
    $accumulative_ingredients_utilized = [
        "Beef" => 150,
        "Cheese" => 30,
        "Onion" => 20
    ];

    $ingredients = [
        [
            "name" => "Beef",
            "stock_base_quantity" => 20000,
            "stock_utilized_quantity" => 750,
            "low_stock_warning_sent" => 0,
        ],
        [
            "name" => "Cheese",
            "stock_base_quantity" => 5000,
            "stock_utilized_quantity" => 150,
            "low_stock_warning_sent" => 0,
        ],
        [
            "name" => "Onion",
            "stock_base_quantity" => 1000,
            "stock_utilized_quantity" => 100,
            "low_stock_warning_sent" => 0,
        ],

    ];
    $updated_ingredients = [
        [
            "name" => "Beef",
            "stock_base_quantity" => 20000,
            "stock_utilized_quantity" => 900,
            "low_stock_warning_sent" => 0,
        ],
        [
            "name" => "Cheese",
            "stock_base_quantity" => 5000,
            "stock_utilized_quantity" => 180,
            "low_stock_warning_sent" => 0,
        ],
        [
            "name" => "Onion",
            "stock_base_quantity" => 1000,
            "stock_utilized_quantity" => 120,
            "low_stock_warning_sent" => 0,
        ],

    ];
    $response = (new App\Repositories\OrderRepository)->formatIngredientsDataFromDbAndAvailableResources($accumulative_ingredients_utilized, json_decode(json_encode($ingredients), true));
    $this->assertEquals($updated_ingredients, $response);
});

it('returns array of insufficient onions', function () {
    $accumulative_ingredients_utilized = [
        "Beef" => 150,
        "Cheese" => 30,
        "Onion" => 2000
    ];

    $ingredients = [
        [
            "name" => "Beef",
            "stock_base_quantity" => 20000,
            "stock_utilized_quantity" => 750,
            "low_stock_warning_sent" => 0,
        ],
        [
            "name" => "Cheese",
            "stock_base_quantity" => 5000,
            "stock_utilized_quantity" => 150,
            "low_stock_warning_sent" => 0,
        ],
        [
            "name" => "Onion",
            "stock_base_quantity" => 1000,
            "stock_utilized_quantity" => 100,
            "low_stock_warning_sent" => 0,
        ],

    ];
    $updated_ingredients = [
        "success" => false,
        "message" => "The onion amount is not enough for this order.",
        "errors" => [
            "onion" => [
                "Insufficient resource."
            ]
        ]];
    $response = (new App\Repositories\OrderRepository)->formatIngredientsDataFromDbAndAvailableResources($accumulative_ingredients_utilized, json_decode(json_encode($ingredients), true));
    $this->assertEquals($updated_ingredients, $response);
});

it('returns array order details', function () {
    $order = (object) '';
    $order->id= 1;
    $response = (new App\Repositories\OrderRepository)->createOrdersArray([[
            "product_id" => 1,
            "quantity" => 1
        ]
    ], $order);
    $this->assertEquals([[
        "order_id" => 1,
        "product_id" => 1,
        "quantity" => 1
    ]
    ], $response);
});
