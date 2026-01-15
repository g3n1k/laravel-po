<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
    ];

    public function poCustomers()
    {
        return $this->hasMany(PoCustomer::class);
    }

    public function downPayments()
    {
        return $this->hasMany(DownPayment::class);
    }
}
