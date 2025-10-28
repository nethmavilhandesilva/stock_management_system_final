<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\GrnEntry; // Make sure to import the related model

class Supplier2 extends Model
{
    // Assuming your table name is 'supplier2s' (Laravel default pluralization)
    protected $table = 'supplier2s'; 
    
    protected $fillable = ['supplier_code', 'supplier_name', 'grn_id', 'total_amount', 'description'];

    /**
     * Get the GRN entry associated with the supplier record.
     */
    public function grn()
    {
        // Assuming your GRN foreign key is 'grn_id' and the model is GrnEntry
        return $this->belongsTo(GrnEntry::class, 'grn_id');
    }
}