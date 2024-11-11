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
        dispatch(new SendLowStockAlertJob($this->ingredient));
    }
}
