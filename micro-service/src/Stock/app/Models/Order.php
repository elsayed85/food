<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    protected $fillable = ['total', 'status'];

    protected $casts = [
        'status' => OrderStatus::class
    ];

    public function setStatusAsProcessing() : void
    {
        $this->status = OrderStatus::Processing;
        $this->save();
    }

    public function setStatusAsCanceled() : void
    {
        $this->status = OrderStatus::Canceled;
        $this->save();
    }

    public function products()
    {
        return $this->belongsToMany(Product::class , (new OrderProduct())->getTable())
            ->withPivot(['id', 'quantity']);
    }
}
