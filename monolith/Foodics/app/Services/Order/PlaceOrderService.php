<?php

namespace App\Services\Order;

use App\Jobs\Order\PlaceNewOrderJob;
use App\Models\Order;

class PlaceOrderService
{
    public function __construct(public array $products)
    {
        //
    }

    public function placeOrder(): void
    {
        dispatch(new PlaceNewOrderJob($this->products));
    }
}
