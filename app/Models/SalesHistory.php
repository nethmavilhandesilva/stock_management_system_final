<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesHistory extends Model
{
    // Table name (optional if follows Laravel naming convention)
    protected $table = 'sales_history';

    // Mass assignable attributes
    protected $fillable = [
        'customer_id',
        'supplier_code',
        'code',
        'item_code',
        'item_name',
        'weight',
        'price_per_kg',
        'total',
        'packs',
    ];
     public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
}
