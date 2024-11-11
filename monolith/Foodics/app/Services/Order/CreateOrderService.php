<?php

namespace App\Services\Order;

use App\Exceptions\Order\OrderCreationException;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateOrderService
{
    public function __construct(public array $products)
    {
        //
    }

    public function createOrder(): Order
    {
        return DB::transaction(function () {
            $order = Order::create();
            $order->products()->attach($this->products);
            return $order;
        });
    }
}
