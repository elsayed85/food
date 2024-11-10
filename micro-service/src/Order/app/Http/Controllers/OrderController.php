<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\Order\PlaceOrderRequest;
use App\Jobs\ProcessOrderJob;

class OrderController extends Controller
{
    public function placeOrder(PlaceOrderRequest $request)
    {
        dispatch(new ProcessOrderJob($request->getProducts()));
        return response()->json(["message" => "Order placed successfully"] , 201);
    }
}
