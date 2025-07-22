<?php
// app/Models/GrnEntry.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrnEntry extends Model
{
    protected $fillable = [
        'auto_purchase_no', 'code', 'supplier_code', 'item_code', 'item_name',
        'packs', 'weight', 'txn_date', 'grn_no','warehouse_no'
    ];
}
