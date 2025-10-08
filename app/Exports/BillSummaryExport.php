<?php
namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;

class BillSummaryExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithTitle, WithEvents
{
    private $salesData;
    private $groupedData;
    private $grandTotal;
    private $exportData;

    public function __construct(Collection $salesData)
    {
        $this->salesData = $salesData;
        $this->grandTotal = $salesData->sum('total');
        
        // Group data by customer code and bill number
        $this->groupedData = $salesData->groupBy(function($item) {
            return $item->customer_code . '|' . ($item->bill_no ?: 'NO_BILL');
        });
        
        $this->exportData = $this->prepareExportData();
    }

    private function prepareExportData()
    {
        $data = new Collection();
        $grandTotal = 0;

        foreach ($this->groupedData as $groupKey => $customerSales) {
            list($customerCode, $billNo) = explode('|', $groupKey);
            $hasBill = $billNo !== 'NO_BILL';
            $customerTotal = $customerSales->sum('total');
            $grandTotal += $customerTotal;

            // Add section header
            $data->push([
                'Customer: ' . $customerCode . ($hasBill ? ' | Bill No: ' . $billNo : ' '),
                '', '', '', '', '', '', '', '', '', '', '' // Empty cells for other columns
            ]);

            // Add column headers for this section
            $data->push([
                'Date',
                'Supplier Code',
                'Item Code',
                'Item Name',
                'Weight',
                'Price per Kg',
                'Total',
                'Packs',
                'First Printed',
                'Reprinted',
                '' // Empty cell for alignment
            ]);

            // Add sales data
            foreach ($customerSales as $sale) {
                $data->push([
                    $sale->Date ? Carbon::parse($sale->Date)->format('Y-m-d') : '',
                    $sale->supplier_code,
                    $sale->item_code,
                    $sale->item_name,
                    $sale->weight,
                    number_format($sale->price_per_kg, 2),
                    number_format($sale->total, 2),
                    $sale->packs,
                    $sale->FirstTimeBillPrintedOn ? Carbon::parse($sale->FirstTimeBillPrintedOn)->format('Y-m-d') : '',
                    $sale->BillReprintAfterchanges ? Carbon::parse($sale->BillReprintAfterchanges)->format('Y-m-d') : '',
                    '' // Empty cell for alignment
                ]);
            }

            // Add section total
            $data->push([
                ($hasBill ? 'Bill Total:' : 'Customer Total:'),
                '', '', '', '', '',
                number_format($customerTotal, 2),
                'Records: ' . $customerSales->count(),
                '', '', ''
            ]);

            // Add empty row for separation
            $data->push(['', '', '', '', '', '', '', '', '', '', '']);
        }

        // Add grand total
        $data->push([
            'Grand Total:',
            '', '', '', '', '',
            number_format($grandTotal, 2),
            'Total Records: ' . $this->salesData->count(),
            'Total Groups: ' . $this->groupedData->count(),
            '', ''
        ]);

        return $data;
    }

    public function collection()
    {
        return $this->exportData;
    }

    public function headings(): array
    {
        return [
            'Customer / Bill Information',
            'Date',
            'Supplier Code',
            'Item Code',
            'Item Name',
            'Weight',
            'Price per Kg',
            'Total',
            'Packs',
            'First Printed',
            'Reprinted'
        ];
    }

    public function map($row): array
    {
        // Since data is pre-processed, just return the row as array
        return is_array($row) ? $row : $row->toArray();
    }

    public function title(): string
    {
        return 'Sales Report';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $lastRow = $this->exportData->count();
                
                // Style section headers (every time we have a customer/bill header)
                $rowIndex = 1; // Start after main heading
                foreach ($this->groupedData as $groupKey => $customerSales) {
                    // Style section header (customer/bill info)
                    $event->sheet->getStyle('A' . $rowIndex)->applyFromArray([
                        'font' => ['bold' => true, 'size' => 12],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'color' => ['rgb' => 'E0E0E0']
                        ]
                    ]);
                    $rowIndex++;
                    
                    // Style column headers for this section
                    $event->sheet->getStyle('A' . $rowIndex . ':K' . $rowIndex)->applyFromArray([
                        'font' => ['bold' => true],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'color' => ['rgb' => 'F2F2F2']
                        ]
                    ]);
                    $rowIndex++;
                    
                    // Skip data rows
                    $rowIndex += $customerSales->count();
                    
                    // Style section total row
                    $event->sheet->getStyle('A' . $rowIndex . ':K' . $rowIndex)->applyFromArray([
                        'font' => ['bold' => true],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'color' => ['rgb' => 'F5F5F5']
                        ]
                    ]);
                    $rowIndex += 2; // Skip total row and empty row
                }
                
                // Style grand total row
                $event->sheet->getStyle('A' . $lastRow . ':K' . $lastRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['rgb' => 'D3D3D3']
                    ]
                ]);

                // Merge cells for section headers
                $rowIndex = 1;
                foreach ($this->groupedData as $groupKey => $customerSales) {
                    $event->sheet->mergeCells('A' . $rowIndex . ':K' . $rowIndex);
                    $rowIndex += (3 + $customerSales->count() + 2); // header + col header + data rows + total + empty
                }
            },
        ];
    }
}
