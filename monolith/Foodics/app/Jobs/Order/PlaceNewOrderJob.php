<?php

namespace App\Jobs\Order;

use App\Services\Order\CreateOrderService;
use App\Services\Order\ProcessOrderService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class PlaceNewOrderJob implements ShouldQueue
{
    use Queueable;

    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(public array $products)
    {
        $this->onConnection('redis')->onQueue('new_orders');
    }

    /**
     * Execute the job.
     * @throws \Exception
     */
    public function handle(): void
    {
        Log::info('New order placed');
        $createService = new CreateOrderService($this->products);
        $order = $createService->createOrder();

        Log::info('Order created', ['order_id' => $order->id]);

        $processService = new ProcessOrderService($order);
        $processService->process();

        Log::info('Order processed', ['order_id' => $order->id]);
    }
}
