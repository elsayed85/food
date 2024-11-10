<?php

namespace App\Services;

use App\Enums\ProductIngredientStatus;
use App\Models\Order;
use App\Models\OrderProductIngredient;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProcessOrderService
{
    private Collection $events;

    public function __construct()
    {
        $this->events = collect();
    }

    public function processOrder(int $orderId, array $productIds): void
    {
        $order = $this->getOrderWithProducts($orderId, $productIds);

        if (!$order) {
            return;
        }

        DB::beginTransaction();

        try {
            $this->events = $this->processOrderProducts($order);
            $order->setStatusAsProcessing();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $order->setStatusAsCanceled();
        }
    }

    private function getOrderWithProducts(int $orderId, array $productIds)
    {
        return Order::with([
            'products' => function ($query) use ($productIds) {
                $query->whereIn('products.id', $productIds)->with('ingredients');
            }
        ])->find($orderId);
    }

    private function processOrderProducts($order): Collection
    {
        return $order->products->map(function ($product) {
            return $product->ingredients->map(function ($ingredient) use ($product) {
                return $this->reserveIngredient($product, $ingredient);
            });
        })->flatMap(function ($productResults) {
            return $productResults;
        });
    }

    private function reserveIngredient($product, $ingredient): array
    {
        $quantityToDeduct = $ingredient->pivot->quantity * $product->pivot->quantity;

        $orderProductIngredient = OrderProductIngredient::create([
            'order_product_id' => $product->pivot->id,
            'ingredient_id' => $ingredient->id,
            'quantity' => $quantityToDeduct,
            'status' => ProductIngredientStatus::Reserved,
        ]);

        $ingredient->deductStock($quantityToDeduct);
        $ingredient->save();

        return [
            'order_product_id' => $product->pivot->id,
            'product_id' => $product->id,

            'product_ingredient_id' => $orderProductIngredient->id,
            'ingredient_id' => $ingredient->id,

            'quantity' => $quantityToDeduct,
            'status' => $orderProductIngredient->status->value,
            'remaining_percentage' => $ingredient->getRemainingPercentage()
        ];
    }

    public function getEvents(): Collection
    {
        return $this->events;
    }
}
