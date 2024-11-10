<?php

namespace App\Services;

use App\Contracts\BaseOrderServiceContract;
use App\Models\Order;

class OrderService implements BaseOrderServiceContract
{
    public function placeOrder(array $products): Order
    {
        $order = Order::create([]);
        $order->products()->attach($products);
        return $order;
    }
}
