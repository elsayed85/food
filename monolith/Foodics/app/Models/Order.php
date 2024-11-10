<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['total', 'status'];

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

    public function setStatusAsProcessing(): void
    {
        $this->status = OrderStatus::Processing;
        $this->save();
    }

    public function setStatusAsCanceled(): void
    {
        $this->status = OrderStatus::Canceled;
        $this->save();
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, (new OrderProduct())->getTable())
            ->withPivot(['id', 'quantity']);
    }
}
