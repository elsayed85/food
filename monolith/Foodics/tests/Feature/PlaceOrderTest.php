<?php

use App\Enums\OrderStatus;
use App\Jobs\Ingredient\SendLowStockAlertJob;
use App\Jobs\Order\PlaceNewOrderJob;
use Illuminate\Support\Facades\Queue;
use App\Enums\ProductIngredientStatus;
use App\Models\Product;
use App\Models\Ingredient;

beforeEach(function () {
    $this->quantity = 50; // Max allowed quantity for this test setup
    $this->product = Product::find(1);
    $this->payload = [
        'products' => [
            ['product_id' => $this->product->id, 'quantity' => $this->quantity]
        ]
    ];
});


it('places an order successfully', function () {
    $response = $this->postJson('/api/order', $this->payload);

    $response->assertStatus(201)
        ->assertJson(['message' => 'Order placed successfully']);
});

it('dispatches PlaceNewOrderJob when placing an order', function () {
    Queue::fake();
    $this->postJson('/api/order', $this->payload);
    Queue::assertPushed(PlaceNewOrderJob::class);
});

it('stores the order with "Processing" status', function () {
    $this->postJson('/api/order', $this->payload);

    $this->assertDatabaseHas('orders', [
        'status' => OrderStatus::Processing->value
    ]);
});

it('attaches products to the order', function () {
    $this->postJson('/api/order', $this->payload);

    $this->assertDatabaseCount('order_product', 1);
});

it('reserves product ingredients for the order', function () {
    $this->postJson('/api/order', $this->payload);

    $this->assertDatabaseHas('order_product_ingredients', [
        'status' => ProductIngredientStatus::Reserved->value
    ]);
});

it('deducts ingredient stock based on order quantity', function () {
    $this->postJson('/api/order', $this->payload);

    foreach ($this->product->ingredients as $ingredient) {
        $this->assertDatabaseHas('ingredients', [
            'id' => $ingredient->id,
            'current_stock' => $ingredient->total_stock - ($this->quantity * $ingredient->pivot->quantity)
        ]);
    }
});

it('triggers a low stock alert when stock is low', function () {
    $this->postJson('/api/order', $this->payload);

    $this->assertEquals(1, Ingredient::where('low_stock_alert_sent', true)->count());
});


// when 51 is the quantity status will be canceled
it('cancels the order when stock is not enough', function () {
    $this->quantity = 51;
    $this->payload = [
        'products' => [
            ['product_id' => $this->product->id, 'quantity' => $this->quantity]
        ]
    ];
    $this->postJson('/api/order', $this->payload);
    $this->assertDatabaseHas('orders', [
        'status' => OrderStatus::Canceled->value
    ]);
});

it('dispatches SendLowStockAlertJob when an ingredient is low in stock', function () {
    Queue::fake();
    $ingredient = Ingredient::where('name', 'Onion')->first();
    $ingredient->current_stock = 500;
    $ingredient->save();
    $this->postJson('/api/order', $this->payload);
    Queue::assertPushed(SendLowStockAlertJob::class);
});

// we need to test if multiple deductions are made , SendLowStockAlertJob should be called only once
// use Onion as the ingredient
it('dispatches SendLowStockAlertJob only once when multiple orders are placed', function () {
    Queue::fake();
    $ingredient = Ingredient::where('name', 'Onion')->first();
    $ingredient->current_stock = 500;
    $ingredient->save();
    $this->postJson('/api/order', $this->payload);
    $count = Queue::pushed(SendLowStockAlertJob::class)->count();
    $this->assertEquals(1, $count);
});
