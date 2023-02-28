<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses( RefreshDatabase::class);


it('returns error for required product filled with empty array', function () {

    $response = $this->postJson('/api/order', []);
    $response->assertStatus(422)->assertJson(["message" => "The products field is required.",
        "errors" => [
            "products" => [
                "The products field is required."
            ]
        ]
    ]);
});


it('creates error for required product filled with empty products array', function () {
    $response = $this->postJson('/api/order', ["products" => []]);
    $response->assertStatus(422)->assertJson(["message" => "The products field is required.",
        "errors" => [
            "products" => [
                "The products field is required."
            ]
        ]
    ]);
});

it('returns success with deduction in food', function () {
    $this-> refreshDatabase();
    $this->seed();
    $response = $this->postJson('/api/order', ["products" => [
        [
            "product_id" => 1,
            "quantity" => 1
        ]
    ]]);
    $response->assertStatus(200)->assertJson(["message" => "Order placed successfully."]);
});

