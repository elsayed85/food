<?php

namespace App\Services;

use Elsayed85\LmsRedis\Services\Event;
use Elsayed85\LmsRedis\Services\NotificationService\NotificationRedisService;
use Elsayed85\LmsRedis\Services\StockService\Enum\IngredientEvent;
use Elsayed85\LmsRedis\Services\StockService\Event\Ingredient\QuantityDeductedEvent;

class RedisService extends NotificationRedisService
{
    private ProcessIngredientService $processIngredientService;
    public function __construct()
    {
        $this->processIngredientService = new ProcessIngredientService();
    }

    public function processEvent(Event $event): void
    {
        match ($event->type) {
            IngredientEvent::DEDUCTED => $this->processIngredientDeductedEvent($event),
            default => null,
        };
    }

    private function processIngredientDeductedEvent(QuantityDeductedEvent $event): void
    {
        $this->processIngredientService->processIngredient($event);
    }
}
