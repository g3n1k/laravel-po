<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DownPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'customer_id',
        'amount',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $dates = [
        'paid_at',
        'created_at',
        'updated_at',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
