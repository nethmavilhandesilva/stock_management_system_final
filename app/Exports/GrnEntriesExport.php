<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GrnEntriesExport implements FromCollection, WithHeadings
{
    protected $entries;

    public function __construct($entries)
    {
        $this->entries = $entries;
    }

    public function collection()
    {
        return collect($this->entries);
    }

    public function headings(): array
    {
        return [
            'ID', 'Code', 'Supplier Code', 'Item Code', 'Item Name', 
            'Packs', 'Weight (kg)', 'Per KG Price', 'Txn Date', 'GRN No'
        ];
    }
}
