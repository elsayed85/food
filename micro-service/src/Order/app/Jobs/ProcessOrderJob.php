<?php

namespace App\Jobs;

use App\Actions\Order\CreateOrderAction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessOrderJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public array $products)
    {
        $this->onConnection('redis')->onQueue('orders');
    }

    /**
     * Execute the job.
     */
    public function handle(CreateOrderAction $createOrderAction): void
    {
        $createOrderAction->setProducts($this->products)->execute();
    }
}
