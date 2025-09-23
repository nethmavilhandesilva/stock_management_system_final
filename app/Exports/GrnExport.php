<?php

namespace App\Exports;

use App\Models\GrnEntry;
use App\Models\Sale;
use App\Models\SalesHistory;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GrnExport implements FromArray, WithHeadings
{
    protected $code;

    public function __construct($code)
    {
        $this->code = $code;
    }

    public function array(): array
    {
        $entries = GrnEntry::when($this->code, fn($q) => $q->where('code', $this->code))->get();

        $data = [];

        foreach ($entries as $entry) {
            $sales = Sale::where('code', $entry->code)->get();
            if ($sales->isEmpty()) {
                $sales = SalesHistory::where('code', $entry->code)->get();
            }

            foreach ($sales as $sale) {
                $data[] = [
                    'Code' => $entry->code,
                    'Item Name' => $sale->item_name,
                    'Date' => $sale->Date,
                    'Bill No' => $sale->bill_no,
                    'Customer' => $sale->customer_code,
                    'Weight' => $sale->weight,
                    'Price per kg' => $sale->price_per_kg,
                    'Total' => $sale->total,
                    'Packs' => $sale->packs,
                    'Profit' => $entry->total_grn - $sales->sum('total') - ($entry->wasted_weight * $entry->PerKGPrice),
                ];
            }
        }

        return $data;
    }

    public function headings(): array
    {
        return ['Code','Item Name','Date','Bill No','Customer','Weight','Price per kg','Total','Packs','Profit'];
    }
}
