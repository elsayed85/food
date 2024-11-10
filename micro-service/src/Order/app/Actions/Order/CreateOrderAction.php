<?php

namespace App\Actions\Order;

use App\Contracts\BaseActionContract;
use App\Contracts\BaseOrderServiceContract;
use App\Models\Order;
use App\Services\RedisService;

class CreateOrderAction implements BaseActionContract
{
    private BaseOrderServiceContract $service;
    private array $products;
    private RedisService $redisService;

    public function __construct(BaseOrderServiceContract $service)
    {
        $this->service = $service;
        $this->redisService = new RedisService();
    }

    public function execute() : Order
    {
        $order = $this->service->placeOrder($this->products);
        $this->redisService->publishOrderCreatedEvent($order);
        return $order;
    }

    public function getProducts(): array
    {
        return $this->products;
    }

    public function setProducts(array $products): self
    {
        $this->products = $products;
        return $this;
    }
}
