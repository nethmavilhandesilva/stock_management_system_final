<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DynamicReportExport implements FromCollection, WithHeadings
{
    protected Collection $data;
    protected array $headings;
    protected array $meta;

    /**
     * @param Collection $data The report data, including totals row if added
     * @param array $headings Column headings
     * @param array $meta Optional metadata to display at the top of the Excel
     */
    public function __construct(Collection $data, array $headings, array $meta = [])
    {
        $this->data = $data;
        $this->headings = $headings;
        $this->meta = $meta;
    }

    public function collection(): Collection
    {
        $metaRows = collect();

        if (!empty($this->meta)) {
            foreach ($this->meta as $label => $value) {
                if ($value) {
                    // Put label in first column, value in second, leave rest empty
                    $metaRows->push(array_merge([$label, $value], array_fill(0, count($this->headings) - 2, '')));
                }
            }
            // Add an empty row after metadata for spacing
            $metaRows->push(array_fill(0, count($this->headings), ''));
        }

        // Combine metadata rows with main data
        return $metaRows->concat($this->data);
    }

    /**
     * Return the headings
     */
    public function headings(): array
    {
        return $this->headings;
    }
}
