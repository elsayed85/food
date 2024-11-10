<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\Order\PlaceOrderRequest;
use App\Services\Order\PlaceOrderService;

class OrderController extends Controller
{
    public function placeOrder(PlaceOrderRequest $request)
    {
        (new PlaceOrderService($request->getProducts()))->placeOrder();
        return response()->json(["message" => "Order placed successfully"], 201);
    }
}
