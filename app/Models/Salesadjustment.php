<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salesadjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_code',
        'supplier_code',
        'code',
        'item_code',
        'item_name',
        'weight',
        'price_per_kg',
        'total',
        'packs',
        'bill_no',
        'type'
    ];
}
