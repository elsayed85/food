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
        $order = Order::create();
        $order->products()->attach($this->products);
        return $order;

        DB::beginTransaction();
        try {
            $order = Order::create();
            $order->products()->attach($this->products);
            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::log('error', $e->getMessage());
            throw new OrderCreationException();
        }
    }
}
