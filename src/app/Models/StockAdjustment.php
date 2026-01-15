<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'initial_stock',
        'adjustment',
        'final_stock',
        'adjusted_at',
        'reason',
    ];

    protected $casts = [
        'initial_stock' => 'integer',
        'adjustment' => 'integer',
        'final_stock' => 'integer',
        'adjusted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $dates = [
        'adjusted_at',
        'created_at',
        'updated_at',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
