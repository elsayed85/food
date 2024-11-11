<?php

namespace App\Jobs\Ingredient;

use App\Models\Ingredient;
use App\Notifications\LowStockAlert;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Notification;

class SendLowStockAlertJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Ingredient $ingredient)
    {
        $this->onQueue('low_stock_alerts');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Notification::route('mail', config('mail.admin_email'))->notify(new LowStockAlert($this->ingredient));
    }
}
