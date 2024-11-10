<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    protected $fillable = ['name', 'initial_stock', 'current_stock', 'low_stock_alert_sent'];

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('quantity');
    }

    public function deductStock(int $deduct): void
    {
        $newStock = $this->current_stock - $deduct;

        if ($newStock < 0) {
            throw new \RuntimeException('The current stock not enough');
        }

        $this->current_stock = $newStock;
    }

    public function getRemainingPercentage(): float
    {
        return round($this->current_stock / $this->initial_stock, 2);
    }

    public function increaseStock(int $increase): void
    {
        $this->current_stock = $this->current_stock + $increase;
    }
}
