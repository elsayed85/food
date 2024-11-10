<?php

namespace App\Services;

use App\Models\Order;
use Elsayed85\LmsRedis\Services\Event;
use Elsayed85\LmsRedis\Services\OrderService\DTO\OrderData;
use Elsayed85\LmsRedis\Services\OrderService\Enum\OrderEvent;
use Elsayed85\LmsRedis\Services\OrderService\Event\OrderCreatedEvent;
use Elsayed85\LmsRedis\Services\OrderService\OrderRedisService;
use Illuminate\Support\Facades\Log;

class RedisService extends OrderRedisService
{
    public function publishOrderCreatedEvent(OrderData|Order $payload): void
    {
        $this->publish(new OrderCreatedEvent($payload instanceof Order ? $payload->toDto() : $payload));
    }

    public function processEvent(Event $event) : void
    {
        match ($event->type) {
            OrderEvent::CREATED => $this->processOrderCreatedEvent($event),
            default => null,
        };
    }

    private function processOrderCreatedEvent(OrderCreatedEvent $event) : void
    {
        Log::info('Order created event received');
    }
}
