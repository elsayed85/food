<?php

namespace App\Models;

use App\Enums\ProductIngredientStatus;
use Elsayed85\LmsRedis\Services\StockService\DTO\ProductIngredientData;
use Illuminate\Database\Eloquent\Model;

class OrderProductIngredient extends Model
{
    protected $table = 'order_product_ingredients';
    protected $guarded = [];
    public $timestamps = false;
    protected $casts = [
        'status' => ProductIngredientStatus::class
    ];

    public float $remaining_percentage = 0;

    public function setRemainingPercentage(float $percentage) : self
    {
        $this->remaining_percentage = $percentage;
        return $this;
    }

    public function toDto() : ProductIngredientData
    {
        return ProductIngredientData::fromArray([
            'order_product_id' => $this->order_product_id,
            'ingredient_id' => $this->ingredient_id,
            'quantity' => $this->quantity,
            'remaining_percentage' => $this->remaining_percentage,
            'status' => $this->status->value
        ]);
    }
}
