<?php

use App\Jobs\Order\PlaceNewOrderJob;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderProductIngredient;
use App\Enums\OrderStatus;
use App\Enums\ProductIngredientStatus;
use Illuminate\Support\Facades\Queue;

test('order is placed successfully', function () {
    Queue::fake();

    // Arrange: Get two sample products
    $products = Product::take(2)->get();
    $products = $products->map(function ($product) {
        return [
            "product_id" => $product->id,
            "quantity" => 1
        ];
    })->toArray();

    // Act: Place the order via the API endpoint
    $response = $this->postJson('/api/order', ["products" => $products]);

    // Assert: Response status and message
    $response->assertStatus(201);
    $response->assertJson(["message" => "Order placed successfully"]);

    // Assert: Job dispatched to queue
    Queue::assertPushed(PlaceNewOrderJob::class);

    // Delay to allow job processing in background by Supervisord workers

    // Assert: Order is created and stored in the database with status 'pending' first, then updated
    $order = Order::latest('id')->first();
    $this->assertNotNull($order);
    $this->assertEquals(OrderStatus::Pending, $order->status);

    // Allow time for job processing to complete
    $order->refresh();
    $this->assertEquals(OrderStatus::Processing, $order->status);

    // Assert: Stock deducted correctly in `order_product_ingredients`
    foreach ($products as $productData) {
        $orderProductIngredient = OrderProductIngredient::where('order_product_id', $order->id)
            ->where('ingredient_id', $productData['product_id'])
            ->first();

        $this->assertNotNull($orderProductIngredient);
        $this->assertEquals($productData['quantity'], $orderProductIngredient->quantity);
        $this->assertEquals(ProductIngredientStatus::Reserved, $orderProductIngredient->status);
    }
});

