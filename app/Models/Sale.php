<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    // If your table name is not "sales", uncomment and change this
    // protected $table = 'sales';

    // Columns you want to be mass assignable
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

    // If you're using timestamps (created_at and updated_at), keep this
    public $timestamps = true;

    // Relationships (Optional)
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
