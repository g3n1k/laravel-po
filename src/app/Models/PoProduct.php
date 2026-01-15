<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'name',
        'price',
        'stock',
        'free_stock',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'free_stock' => 'integer',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function orders()
    {
        return $this->hasMany(PoOrder::class);
    }
}
