<?php

namespace App\Contracts;

use App\Models\Order;

interface BaseOrderServiceContract
{
    public function placeOrder(array $products): Order;
}
