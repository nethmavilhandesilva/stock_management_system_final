<?php
// app/Exports/BillSummaryExport.php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class BillSummaryExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithTitle
{
    private $salesByBill;
    private $dataForExport;

    public function __construct(Collection $salesByBill)
    {
        $this->salesByBill = $salesByBill;
        $this->dataForExport = $this->prepareExportData();
    }

    private function prepareExportData()
    {
        $data = new Collection();
        $grandTotal = 0;

        foreach ($this->salesByBill as $billNo => $sales) {
            $billTotal = 0;
            $data->push([
                'Bill No: ' . $billNo,
                'First Printed: ' . ($sales->first()->FirstTimeBillPrintedOn ? \Carbon\Carbon::parse($sales->first()->FirstTimeBillPrintedOn)->format('Y-m-d') : ''),
                'Reprinted: ' . ($sales->first()->BillReprintAfterchanges ? \Carbon\Carbon::parse($sales->first()->BillReprintAfterchanges)->format('Y-m-d') : ''),
            ]);

            foreach ($sales as $sale) {
                $billTotal += $sale->total;
                $data->push([
                    $sale->code,
                    $sale->customer_code,
                    $sale->supplier_code,
                    $sale->item_name,
                    $sale->weight,
                    $sale->price_per_kg,
                    $sale->total,
                    $sale->packs,
                ]);
            }

            $data->push(['', '', '', '', '', 'Bill Total:', $billTotal, '']);
            $data->push(['']); // Blank row for separation
            $grandTotal += $billTotal;
        }

        $data->push(['', '', '', '', '', '', '', '']); // Final blank row for formatting
        $data->push(['', '', '', '', '', 'Grand Total:', $grandTotal, '']);

        return $data;
    }

    public function collection()
    {
        return $this->dataForExport;
    }

    public function headings(): array
    {
        return [
            'කේතය',
            'පාරිභෝගික කේතය',
            'සැපයුම්කරු කේතය',
            'භාණ්ඩ නාමය',
            'බර',
            'කිලෝවකට මිල',
            'එකතුව',
            'පැකේජ'
        ];
    }

    public function map($row): array
    {
        // Since data is pre-processed, just return the row
        return $row;
    }

    public function title(): string
    {
        return 'Bill Summary Report';
    }
}