<?php

namespace App\Observers;

use App\Models\Ingredient;
use App\Services\Ingredient\NotifyIngredientService;

class IngredientObserver
{
    /**
     * Handle the Ingredient "updated" event.
     */
    public function updated(Ingredient $ingredient): void
    {
        (new NotifyIngredientService($ingredient))->notify();
    }
}
