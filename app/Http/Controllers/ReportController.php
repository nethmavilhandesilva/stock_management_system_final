<?php
// app/Http/Controllers/ReportController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\GrnEntry;// Replace with your actual model name
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf; 
use App\Exports\DynamicReportExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Salesadjustment;

class ReportController extends Controller
{
    public function index()
    {

        $suppliers = Sale::select('supplier_code')->distinct()->pluck('supplier_code');
        return view('dashboard.reports.salesbasedonsuppliers', compact('suppliers'));
    }

   public function fetch(Request $request)
{
    // Log the incoming request data to check what values are being sent
    Log::info('Report Fetch Request:', $request->all());

    $query = Sale::query();

    // Filter by supplier_code (optional)
    if ($request->filled('supplier_code') && $request->supplier_code !== 'all') {
        $query->where('supplier_code', $request->supplier_code);
    }

    // Filter by GRN code if selected
    if ($request->filled('code')) {
        $query->where('code', $request->code);
    }

    // Determine date range for filtering
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    if ($startDate && $endDate) {
        // Use provided date range
        $query->whereBetween('created_at', [
            Carbon::parse($startDate)->startOfDay(),
            Carbon::parse($endDate)->endOfDay()
        ]);
    } 
    

    // Log the final built query
    Log::info('Report Fetch Final Query:', ['sql' => $query->toSql(), 'bindings' => $query->getBindings()]);

    $records = $query->get([
        'supplier_code',
        'code',
        'bill_no',
        'packs',
        'weight',
        'price_per_kg',
        'item_code',
        'total',
        'customer_code',
        'created_at'
    ]);

    return view('dashboard.reports.resultsalesbasedonsuppliers', [
        'records' => $records,
        'shop_no' => 'C11',
         'filters' => $request->all() 
    ]);
}



 public function fetchItemReport(Request $request)
{
    $validated = $request->validate([
        'item_code' => 'required',
        'supplier_code' => 'nullable|string',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date',
    ]);

    $query = \App\Models\Sale::where('item_code', $validated['item_code']);

    if (!empty($request->supplier_code) && $request->supplier_code !== 'all') {
        $query->where('supplier_code', $request->supplier_code);
    }

    // Use today's date if start or end date is missing
    if ($request->start_date && $request->end_date) {
        $query->whereDate('created_at', '>=', $request->start_date)
            ->whereDate('created_at', '<=', $request->end_date);
    } 

    $sales = $query->get([
        'item_code',
        'packs',
        'weight',
        'price_per_kg',
        'total',
        'customer_code',
        'supplier_code',
        'bill_no',
        'item_name'
    ]);

    // Corrected line: Pass the 'sales' and 'filters' data to the view
    return view('dashboard.reports.item-wise-report', [
        'sales' => $sales,
        'filters' => $request->all()
    ]);
}

 
public function getweight(Request $request)
{
    $grnCode = $request->input('grn_code');
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    // If no GRN code is selected, return an error. This is a good practice.
    if (!$grnCode) {
        return redirect()->back()->withErrors('Please select a GRN code.');
    }

    $query = Sale::query();
    $query->where('code', $grnCode);

    // Apply the date filter only if dates are provided
    if ($startDate && $endDate) {
        $query->whereDate('created_at', '>=', $startDate)
              ->whereDate('created_at', '<=', $endDate);
    }
    
    $sales = $query->orderBy('created_at', 'asc')->get();
    $selectedGrnEntry = GrnEntry::where('code', $grnCode)->first();

    return view('dashboard.reports.weight-based-report', [
        'sales' => $sales,
        'selectedGrnCode' => $grnCode,
        'selectedGrnEntry' => $selectedGrnEntry,
        'startDate' => $startDate,
        'endDate' => $endDate,
        'filters' => $request->all(), // Add this line to pass all filters
    ]);
} public function getGrnSalecodereport(Request $request)
{
    $grnCode = $request->input('grn_code');
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    if (!$grnCode) {
        return redirect()->back()->withErrors('Please select a GRN code.');
    }

    $query = Sale::query();
    $query->where('code', $grnCode);

    // Apply the date filter only if both dates are provided
    if ($startDate && $endDate) {
        $query->whereDate('created_at', '>=', $startDate)
              ->whereDate('created_at', '<=', $endDate);
    }
    
    $sales = $query->orderBy('created_at', 'asc')->get();
    $selectedGrnEntry = GrnEntry::where('code', $grnCode)->first();

    return view('dashboard.reports.grn_sale_code_report', [
        'sales' => $sales,
        'selectedGrnCode' => $grnCode,
        'selectedGrnEntry' => $selectedGrnEntry,
        'startDate' => $startDate,
        'endDate' => $endDate,
        'filters' => $request->all(), // Add this line to pass all filters
    ]);
}  public function getSalesFilterReport(Request $request)
{
    // Start with all sales data
    $query = Sale::query();

    // Apply filters if present
    if ($request->filled('supplier_code')) {
        $query->where('supplier_code', $request->input('supplier_code'));
    }

    if ($request->filled('customer_code')) {
        $query->where('customer_code', $request->input('customer_code'));
    }

    if ($request->filled('item_code')) {
        $query->where('item_code', $request->input('item_code'));
    }

    // Apply a date range filter only if a start or end date is provided
    if ($request->filled('start_date')) {
        $query->whereDate('created_at', '>=', $request->input('start_date'));
    }

    if ($request->filled('end_date')) {
        $query->whereDate('created_at', '<=', $request->input('end_date'));
    }
    
    // Apply ordering
    switch ($request->input('order_by', 'id_desc')) { // Default to id_desc
        case 'id_asc':
            $query->orderBy('id', 'asc');
            break;
        case 'customer_code_asc':
            $query->orderBy('customer_code', 'asc');
            break;
        case 'customer_code_desc':
            $query->orderBy('customer_code', 'desc');
            break;
        case 'item_name_asc':
            $query->orderBy('item_name', 'asc');
            break;
        case 'item_name_desc':
            $query->orderBy('item_name', 'desc');
            break;
        case 'total_desc':
            $query->orderBy('total', 'desc');
            break;
        case 'total_asc':
            $query->orderBy('total', 'asc');
            break;
        case 'weight_desc':
            $query->orderBy('weight', 'desc');
            break;
        case 'weight_asc':
            $query->orderBy('weight', 'asc');
            break;
        case 'id_desc':
        default:
            $query->orderBy('id', 'desc');
            break;
    }

    $sales = $query->get([
        'code',
        'packs',
        'item_name',
        'weight',
        'price_per_kg',
        'total',
        'bill_no',
        'customer_code',
        'created_at'
    ]);

    // Calculate grand total
    $grandTotal = $sales->sum('total');

    // Pass data to the report view
    return view('dashboard.reports.sales_filter_report', compact('sales', 'grandTotal', 'request'));
}
    public function getGrnSalesOverviewReport()
    {
        // Fetch all GRN entries
        $grnEntries = GrnEntry::all();

        $reportData = [];

        foreach ($grnEntries as $grnEntry) {
            // Fetch sales related to this GRN entry's code
            // IMPORTANT: Confirm 'code' in GrnEntry matches the foreign key in Sale.
            // If Sale has a 'grn_entry_code' column that stores GrnEntry->code,
            // then use: $relatedSales = Sale::where('grn_entry_code', $grnEntry->code)->get();
            $relatedSales = Sale::where('code', $grnEntry->code)->get();

            $totalSoldPacks = $relatedSales->sum('packs');
            $totalSoldWeight = $relatedSales->sum('weight');
            $totalSalesValueForGrn = $relatedSales->sum('total'); // NEW: Sum of total for related sales

            $remainingPacks = $grnEntry->original_packs - $totalSoldPacks;
            $remainingWeight = $grnEntry->original_weight - $totalSoldWeight;

            $reportData[] = [
                'date' => Carbon::parse($grnEntry->created_at)->timezone('Asia/Colombo')->format('Y-m-d H:i:s'),
                'grn_code' => $grnEntry->code,
                'item_name' => $grnEntry->item_name,
                'original_packs' => $grnEntry->original_packs,
                'original_weight' => $grnEntry->original_weight,
                'sold_packs' => $totalSoldPacks,
                'sold_weight' => $totalSoldWeight,
                'total_sales_value' => $totalSalesValueForGrn, // NEW: Add to report data
                'remaining_packs' => $remainingPacks,
                'remaining_weight' => number_format($remainingWeight, 2),
            ];
        }

        // Pass the processed data to the view
        return view('dashboard.reports.grn_sales_overview_report', [
            'reportData' => collect($reportData)
        ]);
    }
    public function getGrnSalesOverviewReport2()
    {
        // Fetch all GRN entries
        $grnEntries = GrnEntry::all();

        $reportData = [];

        foreach ($grnEntries as $grnEntry) {
            // Fetch sales related to this GRN entry's code
            // IMPORTANT: Confirm 'code' in GrnEntry matches the foreign key in Sale.
            // If Sale has a 'grn_entry_code' column that stores GrnEntry->code,
            // then use: $relatedSales = Sale::where('grn_entry_code', $grnEntry->code)->get();
            $relatedSales = Sale::where('code', $grnEntry->code)->get();

            $totalSoldPacks = $relatedSales->sum('packs');
            $totalSoldWeight = $relatedSales->sum('weight');
            $totalSalesValueForGrn = $relatedSales->sum('total'); // NEW: Sum of total for related sales

            $remainingPacks = $grnEntry->original_packs - $totalSoldPacks;
            $remainingWeight = $grnEntry->original_weight - $totalSoldWeight;

            $reportData[] = [
                'date' => Carbon::parse($grnEntry->created_at)->timezone('Asia/Colombo')->format('Y-m-d H:i:s'),
                'grn_code' => $grnEntry->code,
                'item_name' => $grnEntry->item_name,
                'original_packs' => $grnEntry->original_packs,
                'original_weight' => $grnEntry->original_weight,
                'sold_packs' => $totalSoldPacks,
                'sold_weight' => $totalSoldWeight,
                'total_sales_value' => $totalSalesValueForGrn, // NEW: Add to report data
                'remaining_packs' => $remainingPacks,
                'remaining_weight' => number_format($remainingWeight, 2),
            ];
        }

        // Pass the processed data to the view
        return view('dashboard.reports.grn_sales_overview_report2', [
            'reportData' => collect($reportData)
        ]);
    }
    public function downloadReport(Request $request, $reportType, $format)
{
    // Pass the request inputs (filters) to the getReportData method
    list($reportData, $headings, $reportTitle) = $this->getReportData($reportType, $request->all());

    // Handle the download based on the requested format
    if ($format === 'excel') {
        // You'll need to create a DynamicReportExport class that handles this data
        $filename = str_replace(' ', '-', $reportTitle) . '_' . Carbon::now()->format('Y-m-d') . '.xlsx';
        return Excel::download(new DynamicReportExport($reportData, $headings), $filename);
    }

    if ($format === 'pdf') {
        // You'll need to create a generic_report_pdf view that displays this data
        $filename = str_replace(' ', '-', $reportTitle) . '_' . Carbon::now()->format('Y-m-d') . '.pdf';
        $pdf = Pdf::loadView('reports.generic_report_pdf', compact('reportData', 'headings', 'reportTitle'));
        return $pdf->download($filename);
    }

    abort(404, 'Invalid report format.');
}

    /**
     * This function fetches and formats the data for a given report type.
     * You will need to customize this based on your report logic.
     */
     protected function getReportData($reportType, $filters = [])
{
    $reportData = collect();
    $headings = [];
    $reportTitle = 'Report';

    switch ($reportType) {
        case 'supplier-sales':
            $reportTitle = 'Supplier Sales Report';

            $records = Sale::query()
                ->when(isset($filters['code']), function ($query) use ($filters) {
                    return $query->where('code', $filters['code']);
                })
                ->when(isset($filters['date_from']), function ($query) use ($filters) {
                    return $query->whereDate('created_at', '>=', $filters['date_from']);
                })
                ->when(isset($filters['date_to']), function ($query) use ($filters) {
                    return $query->whereDate('created_at', '<=', $filters['date_to']);
                })
                ->get();
            
            $headings = ['Bill No', 'Packs', 'Weight (kg)', 'Price per kg', 'Total', 'Customer Code', 'Date', 'Shop No'];
            $reportData = $records->map(function ($row) {
                return [
                    $row->bill_no,
                    $row->packs,
                    $row->weight,
                    $row->price_per_kg,
                    $row->total,
                    $row->customer_code,
                    Carbon::parse($row->created_at)->format('Y-m-d H:i'),
                    'N/A',
                ];
            });
            break;

        case 'grn-sales-overview':
            $reportTitle = 'GRN Sales Overview Report';
            
            $records = GrnEntry::query()
                ->when(isset($filters['grn_code']), function ($query) use ($filters) {
                    return $query->where('grn_code', $filters['grn_code']);
                })
                ->get();
            
            $headings = ['GRN Code', 'Item Code', 'Item Name', 'Original Packs', 'Current Packs', 'Weight (kg)'];
            $reportData = $records->map(function ($row) {
                return [
                    $row->code,
                    $row->item_code,
                    $row->item_name,
                    $row->original_packs,
                    $row->packs,
                    $row->weight,
                ];
            });
            break;

        case 'item-wise-report':
            $reportTitle = 'Item-wise Report';
            
            $query = Sale::query()
                ->when(isset($filters['item_code']), function ($query) use ($filters) {
                    return $query->where('item_code', $filters['item_code']);
                })
                ->when(isset($filters['supplier_code']) && $filters['supplier_code'] !== 'all', function ($query) use ($filters) {
                    return $query->where('supplier_code', $filters['supplier_code']);
                })
                ->when(isset($filters['start_date']), function ($query) use ($filters) {
                    return $query->whereDate('created_at', '>=', $filters['start_date']);
                })
                ->when(isset($filters['end_date']), function ($query) use ($filters) {
                    return $query->whereDate('created_at', '<=', $filters['end_date']);
                });
            
            $records = $query->get();
            
            $headings = ['Bill No', 'Item Code', 'Item Name', 'Packs', 'Weight (kg)', 'Price per kg', 'Total', 'Customer Code', 'Supplier Code'];
            $reportData = $records->map(function ($row) {
                return [
                    $row->bill_no,
                    $row->item_code,
                    $row->item_name,
                    $row->packs,
                    $row->weight,
                    $row->price_per_kg,
                    $row->total,
                    $row->customer_code,
                    $row->supplier_code,
                ];
            });
            break;

        case 'grn-sales-report':
            $reportTitle = 'GRN-based Sales Report';

            $query = Sale::query()
                ->when(isset($filters['grn_code']), function ($query) use ($filters) {
                    return $query->where('code', $filters['grn_code']);
                })
                ->when(isset($filters['start_date']), function ($query) use ($filters) {
                    return $query->whereDate('created_at', '>=', $filters['start_date']);
                })
                ->when(isset($filters['end_date']), function ($query) use ($filters) {
                    return $query->whereDate('created_at', '<=', $filters['end_date']);
                });
            
            $records = $query->orderBy('created_at', 'asc')->get();
            
            $headings = ['Item Code', 'Item Name', 'Packs', 'Weight (kg)', 'Total'];
            $reportData = $records->map(function ($row) {
                return [
                    $row->item_code,
                    $row->item_name,
                    $row->packs,
                    $row->weight,
                    $row->total,
                ];
            });
            break;

        case 'grn-sale-code-report':
            $reportTitle = 'GRN Code-based Sales Report';

            $query = Sale::query()
                ->when(isset($filters['grn_code']), function ($query) use ($filters) {
                    return $query->where('code', $filters['grn_code']);
                })
                ->when(isset($filters['start_date']), function ($query) use ($filters) {
                    return $query->whereDate('created_at', '>=', $filters['start_date']);
                })
                ->when(isset($filters['end_date']), function ($query) use ($filters) {
                    return $query->whereDate('created_at', '<=', $filters['end_date']);
                });
            
            $records = $query->orderBy('created_at', 'asc')->get();
            
            $headings = ['Item Code', 'Item Name', 'Packs', 'Weight (kg)', 'Total'];
            $reportData = $records->map(function ($row) {
                return [
                    $row->item_code,
                    $row->item_name,
                    $row->packs,
                    $row->weight,
                    $row->total,
                ];
            });
            break;
    }

    return [$reportData, $headings, $reportTitle];
}
public function salesAdjustmentReport(Request $request)
{
    $code = $request->input('code');
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    $query = Salesadjustment::query();

    if ($code) {
        $query->where('code', $code);
    }
    if ($startDate) {
        $query->whereDate('created_at', '>=', $startDate);
    }
    if ($endDate) {
        $query->whereDate('created_at', '<=', $endDate);
    }

    $entries = $query->orderBy('created_at', 'desc')->paginate(20);

    return view('dashboard.reports.salesadjustment', compact('entries', 'code', 'startDate', 'endDate'));
}

}




