<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    protected $fillable = ['name', 'total_stock', 'current_stock', 'low_stock_alert_sent'];

    protected $casts = [
        'total_stock' => 'decimal:2',
        'current_stock' => 'decimal:2',
        'low_stock_alert_sent' => 'boolean',
    ];

    public const LOW_STOCK_THRESHOLD = 0.5;

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('quantity');
    }


    public function deductStock(int $amount): void
    {
        if ($amount > $this->current_stock) {
            throw new \RuntimeException('The current stock is not enough');
        }

        $this->current_stock -= $amount;
        $this->save();
    }

    public function triggerLowStockAlert(\Closure $callback): void
    {
        if ($this->current_stock <= $this->total_stock * Ingredient::LOW_STOCK_THRESHOLD && !$this->low_stock_alert_sent) {
            $callback();
            $this->low_stock_alert_sent = true;
            $this->save();
        }
    }

    public function getRemainingPercentage(): float
    {
        return round($this->current_stock / $this->total_stock, 2);
    }

    public function increaseStock(int $increase): void
    {
        $this->current_stock = $this->current_stock + $increase;
    }
}
