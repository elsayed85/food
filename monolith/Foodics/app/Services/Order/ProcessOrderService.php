<?php

namespace App\Services\Order;

use App\Enums\ProductIngredientStatus;
use App\Models\Order;
use App\Models\OrderProductIngredient;
use Illuminate\Support\Facades\DB;

class ProcessOrderService
{
    public function __construct(public Order $order)
    {
        //
    }

    public function process(): void
    {
        DB::beginTransaction();

        try {
            $this->processOrderProducts();
            $this->order->setStatusAsProcessing();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->order->setStatusAsCanceled();
            report($e);
        }
    }

    private function processOrderProducts(): void
    {
        $this->order->products
            ->map(function ($product) {
                $product->ingredients->map(function ($ingredient) use ($product) {
                    $this->reserveIngredient($product, $ingredient);
                });
            })->flatMap(function ($productResults) {
                return $productResults;
            });
    }

    private function reserveIngredient($product, $ingredient): void
    {
        $quantityToDeduct = $ingredient->pivot->quantity * $product->pivot->quantity;

        OrderProductIngredient::create([
            'order_product_id' => $product->pivot->id,
            'ingredient_id' => $ingredient->id,
            'quantity' => $quantityToDeduct,
            'status' => ProductIngredientStatus::Reserved,
        ]);

        $ingredient->deductStock($quantityToDeduct);
        $ingredient->save();
    }
}
