<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoCustomer extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'customer_id',
        'product_id',
        'item_quantity',
        'received_quantity',
        'status',
        'payment_status',
        'payment_amount',
        'payment_product_price',
        'transaction_summary_id',
        'ordered_at',
    ];

    protected $casts = [
        'item_quantity' => 'integer',
        'received_quantity' => 'integer',
        'ordered_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $dates = [
        'ordered_at',
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

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function transactionSummary()
    {
        return $this->belongsTo(TransactionSummary::class, 'transaction_summary_id');
    }
}
