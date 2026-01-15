<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $dates = [
        'start_date',
        'end_date',
        'created_at',
        'updated_at',
    ];

    public function poItems()
    {
        return $this->hasMany(PoItem::class);
    }

    public function poCustomers()
    {
        return $this->hasMany(PoCustomer::class);
    }

    public function downPayments()
    {
        return $this->hasMany(DownPayment::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'po_items');
    }
}
