<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Elsayed85\LmsRedis\Services\OrderService\DTO\OrderData;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['status'];

    protected $casts = [
        'status' => OrderStatus::class
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->status = OrderStatus::Pending;
        });
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('quantity');
    }

    public function toDto() : OrderData
    {
        return OrderData::fromArray([
            'id' => $this->id,
            'items' => $this->products->map(function ($product) {
                return [
                    'product_id' => $product->id,
                    'quantity' => $product->pivot->quantity
                ];
            })->toArray()
        ]);
    }
}
