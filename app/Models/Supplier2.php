<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier2 extends Model
{
    protected $fillable = ['supplier_code', 'supplier_name', 'grn_id','total_amount'];

    public function grn()
    {
        return $this->belongsTo(GrnEntry::class, 'grn_id');
    }
}
