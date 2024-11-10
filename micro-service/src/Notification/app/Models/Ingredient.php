<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    protected $fillable = ['name', 'initial_stock', 'current_stock', 'low_stock_alert_sent'];
    public function markLowStockAlertSent(): void
    {
        $this->low_stock_alert_sent = true;
        $this->save();
    }

    public function canNotifyStockAlert() : bool
    {
        return $this->low_stock_alert_sent == false;
    }
}
