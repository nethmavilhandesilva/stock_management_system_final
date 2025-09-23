<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class GrnSalesOverviewExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected Collection $reportData;
    protected array $grandTotals;

    public function __construct(Collection $reportData)
    {
        $this->reportData = $reportData;

        // ✅ Calculate grand totals (use sum, not avg, for price if you want total price)
        $this->grandTotals = [
            'price'             => $reportData->sum('price'),
            'original_weight'   => $reportData->sum('original_weight'),
            'original_packs'    => $reportData->sum('original_packs'),
            'sold_weight'       => $reportData->sum('sold_weight'),
            'sold_packs'        => $reportData->sum('sold_packs'),
            'total_sales_value' => $reportData->sum('total_sales_value'),
            'remaining_weight'  => $reportData->sum('remaining_weight'),
            'remaining_packs'   => $reportData->sum('remaining_packs'),
        ];
    }

    /**
     * Return the full collection including totals row
     */
    public function collection(): Collection
    {
        // Add totals row at the end
        return $this->reportData->push([
            'grn_code'          => 'TOTAL',
            'item_name'         => '',
            'price'             => $this->grandTotals['price'],
            'original_weight'   => $this->grandTotals['original_weight'],
            'original_packs'    => $this->grandTotals['original_packs'],
            'sold_weight'       => $this->grandTotals['sold_weight'],
            'sold_packs'        => $this->grandTotals['sold_packs'],
            'total_sales_value' => $this->grandTotals['total_sales_value'],
            'remaining_weight'  => $this->grandTotals['remaining_weight'],
            'remaining_packs'   => $this->grandTotals['remaining_packs'],
        ]);
    }

    /**
     * Headings for Excel columns
     */
    public function headings(): array
    {
        return [
            'GRN Code',
            'Item Name',
            'Price',
            'Purchased Weight',
            'Purchased Packs',
            'Sold Weight',
            'Sold Packs',
            'Total Sales Value',
            'Remaining Weight',
            'Remaining Packs',
        ];
    }

    /**
     * Map each row into Excel format
     */
    public function map($row): array
    {
        return [
            $row['grn_code'],
            $row['item_name'],
            number_format($row['price'], 2),
            number_format($row['original_weight'], 2),
            $row['original_packs'],
            number_format($row['sold_weight'], 2),
            $row['sold_packs'],
            number_format($row['total_sales_value'], 2),
            number_format($row['remaining_weight'], 2),
            $row['remaining_packs'],
        ];
    }
}

