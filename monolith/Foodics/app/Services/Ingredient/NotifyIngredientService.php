<?php

namespace App\Services\Ingredient;

use App\Jobs\Ingredient\SendLowStockAlertJob;
use App\Models\Ingredient;
use Illuminate\Support\Facades\DB;

class NotifyIngredientService
{
    public function __construct(public Ingredient $ingredient)
    {
        //
    }

    public function notify(): void
    {
        $ingredient = $this->ingredient;
        if ($ingredient->getRemainingPercentage() < Ingredient::LOW_STOCK_THRESHOLD && !$ingredient->low_stock_alert_sent) {
            DB::beginTransaction();
            try {
                dispatch(new SendLowStockAlertJob($this->ingredient));
                $ingredient->low_stock_alert_sent = true;
                $ingredient->save();
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        }
    }
}
