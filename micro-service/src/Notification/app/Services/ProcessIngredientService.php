<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Notifications\SendEmailNotification;
use Elsayed85\LmsRedis\Services\StockService\Event\Ingredient\QuantityDeductedEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class ProcessIngredientService
{
    private const THRESHOLD = 0.5;

    public function processIngredient(QuantityDeductedEvent $event): void
    {
        $payload = $event->payload;
        $ingredient = Ingredient::find($payload->ingredient_id);

        if(!$ingredient) {
            return;
        }

        if(!$ingredient->canNotifyStockAlert()){
            return;
        }

        DB::beginTransaction();
        try {
            if ($payload->remaining_percentage < self::THRESHOLD) {
                Notification::routes(['mail' => ['barrett@example.com' => 'Barrett Blair']])->notify(new SendEmailNotification($ingredient));
                Log::info('Low stock alert sent for ingredient: ' . $ingredient->name);
            }
            DB::commit();
        } catch (\Exception $e) {
            Log::error('Error processing ingredient: ' . $e->getMessage());
            DB::rollBack();
        }
    }
}
