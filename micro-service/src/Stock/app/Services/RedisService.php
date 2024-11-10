<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderProductIngredient;
use App\Models\Product;
use Elsayed85\LmsRedis\Services\Event;
use Elsayed85\LmsRedis\Services\OrderService\Enum\OrderEvent;
use Elsayed85\LmsRedis\Services\OrderService\Event\OrderCreatedEvent;
use Elsayed85\LmsRedis\Services\StockService\DTO\ProductIngredientData;
use Elsayed85\LmsRedis\Services\StockService\DTO\StockData;
use Elsayed85\LmsRedis\Services\StockService\Event\Ingredient\QuantityDeductedEvent;
use Elsayed85\LmsRedis\Services\StockService\Event\StockUpdatedEvent;
use Elsayed85\LmsRedis\Services\StockService\StockRedisService;
use Illuminate\Support\Facades\Log;

class RedisService extends StockRedisService
{
    public function processEvent(Event $event): void
    {
        match ($event->type) {
            OrderEvent::CREATED => $this->processOrderCreatedEvent($event),
            default => null,
        };
    }

    private function processOrderCreatedEvent(OrderCreatedEvent $event): void
    {
        $payload = $event->payload;

        $orderService = new ProcessOrderService();
        $orderService->processOrder(
            orderId: $payload->id,
            productIds: array_map(fn($product) => $product->product_id, $payload->items)
        );

        $orderService->getEvents()->each(function ($event) {
            $this->publish(new QuantityDeductedEvent(ProductIngredientData::fromArray($event)));
        });
    }
}
