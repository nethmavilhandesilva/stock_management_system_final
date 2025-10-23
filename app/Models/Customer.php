<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['short_name','name', 'address', 'telephone_no', 'credit_limit','ID_NO'];
     public function salesHistory()
    {
        return $this->hasMany(SalesHistory::class, 'customer_id', 'id');
    }
      public function incomeExpenses()
    {
        return $this->hasMany(IncomeExpenses::class, 'customer_short_name', 'short_name');
    }
}