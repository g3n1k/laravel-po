<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionSummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'customer_id',
        'total_bill',
        'total_dp',
        'additional_payment',
        'remaining_payment',
        'status',
        'notes',
        'completed_at'
    ];

    protected $casts = [
        'total_bill' => 'decimal:2',
        'total_dp' => 'decimal:2',
        'additional_payment' => 'decimal:2',
        'remaining_payment' => 'decimal:2',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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
