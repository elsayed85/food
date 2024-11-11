<?php

namespace App\Observers;

use App\Models\Ingredient;
use App\Services\Ingredient\NotifyIngredientService;
use Illuminate\Support\Facades\DB;

class IngredientObserver
{
    /**
     * Handle the Ingredient "updated" event.
     */
    public function updated(Ingredient $ingredient): void
    {
        DB::beginTransaction();
        try {
            $ingredient->triggerLowStockAlert(function () use ($ingredient) {
                (new NotifyIngredientService($ingredient))->notify();
            });
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
        }
    }
}
