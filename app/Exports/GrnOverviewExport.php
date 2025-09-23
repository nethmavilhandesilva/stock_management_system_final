namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class GrnOverviewExport implements FromCollection, WithHeadings, WithMapping
{
    private $reportData;

    public function __construct(array $reportData)
    {
        $this->reportData = $reportData;
    }

    public function collection()
    {
        return new Collection($this->reportData);
    }

    public function headings(): array
    {
        return [
            'Item Name',
            'Original Weight',
            'Original Packs',
            'Sold Weight',
            'Sold Packs',
            'Total Sales Value',
            'Remaining Weight',
            'Remaining Packs'
        ];
    }

    public function map($data): array
    {
        return [
            $data['item_name'],
            $data['original_weight'],
            $data['original_packs'],
            $data['sold_weight'],
            $data['sold_packs'],
            $data['total_sales_value'],
            $data['remaining_weight'],
            $data['remaining_packs']
        ];
    }
}