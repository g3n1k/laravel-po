<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'customer_id',
        'product_id',
        'quantity',
        'received_quantity',
        'status',
        'created_by',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'received_quantity' => 'integer',
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
        return $this->belongsTo(PoProduct::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }
}
