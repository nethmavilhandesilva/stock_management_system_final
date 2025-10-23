<?php


namespace App\Http\Controllers;
use Mpdf\Mpdf;
use App\Models\CustomersLoan;
use App\Models\IncomeExpenses;
use App\Models\SalesHistory;
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
use App\Mail\DailyReportMail;
use Illuminate\Support\Facades\Mail;
use App\Models\Setting;
use App\Mail\ChangeReportMail;
use App\Mail\TotalSalesReportMail;
use App\Mail\BillSummaryReportMail;
use App\Mail\CreditReportMail;
use App\Mail\ItemWiseReportMail;
use App\Mail\GrnSalesReportMail;
use App\Mail\SupplierSalesReportMail;
use App\Mail\GrnSalesOverviewMail;
use App\Mail\SalesReportMail;
use App\Mail\FinancialReportMail;
use App\Mail\LoanReportMail;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use App\Exports\SalesAdjustmentsExport;
use App\Mail\GrnbladeReportMail;
use App\Exports\GrnExport;
use App\Mail\CombinedReportsMail;
use App\Mail\CombinedReportsMail2;
use App\Models\GrnEntry2;
use App\Exports\GrnOverviewExport;
use App\Models\Item;
use App\Exports\ItemsExport;



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

        // Determine date range for filtering
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($startDate && $endDate) {
            // If a date range is provided, query the Salesadjustment table
            $query = SalesHistory::query();

            // Apply date range filter
            $query->whereBetween('Date', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);

            // Filter by supplier_code (optional) for Salesadjustment
            if ($request->filled('supplier_code') && $request->supplier_code !== 'all') {
                $query->where('supplier_code', $request->supplier_code);
            }

            // Filter by GRN code if selected (assuming 'code' applies to Salesadjustment as well)
            if ($request->filled('code')) {
                $query->where('code', $request->code);
            }

        } else {
            // If no date range, continue to query the Sale table
            $query = Sale::query();

            // Filter by supplier_code (optional) for Sale
            if ($request->filled('supplier_code') && $request->supplier_code !== 'all') {
                $query->where('supplier_code', $request->supplier_code);
            }

            // Filter by GRN code if selected for Sale
            if ($request->filled('code')) {
                $query->where('code', $request->code);
            }
        }

        // Log the final built query
        Log::info('Report Fetch Final Query:', ['sql' => $query->toSql(), 'bindings' => $query->getBindings()]);

        // Select common fields that exist in both models, or adjust based on which model is queried
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

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Determine which model to query based on the presence of a date range
        if ($startDate && $endDate) {
            // If both start_date and end_date are provided, query Salesadjustment
            $query = SalesHistory::query();

            // Apply date range filter
            $query->whereBetween('Date', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);

        } else {
            // Otherwise, query Sale (default behavior)
            $query = Sale::query();
        }

        // Apply common filters to the selected query
        $query->where('item_code', $validated['item_code']);

        if (!empty($request->supplier_code) && $request->supplier_code !== 'all') {
            $query->where('supplier_code', $request->supplier_code);
        }

        $sales = $query->get([
            'Date',
            'item_code',
            'packs',
            'weight',
            'price_per_kg',
            'total',
            'customer_code',
            'supplier_code',
            'bill_no',
            'item_name',
            'created_at',
            'code',
            // Include created_at for consistency and potential display
        ]);

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
        $supplierCode = $request->input('supplier_code');

        if ($startDate && $endDate) {
            $model = SalesHistory::query();
            $table = 'sales_histories';
            $dateColumn = 'Date';
        } else {
            $model = Sale::query();
            $table = 'sales';
            $dateColumn = null;
        }

        // Aggregate first to avoid duplicates
        $query = $model->selectRaw("
        item_code,
        item_name,
        SUM(packs) as packs,
        SUM(weight) as weight,
        SUM(total) as total
    ");

        if ($dateColumn) {
            $query->whereBetween($dateColumn, [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
        }

        if (!empty($supplierCode)) {
            $query->where('supplier_code', $supplierCode);
        }

        if (!empty($grnCode)) {
            $query->where('code', $grnCode);
        }

        // Group by item_code and item_name to aggregate duplicates
        $sales = $query
            ->orderBy('item_code', 'asc')
            ->groupBy('item_code', 'item_name')
            ->get();

        // Join items separately to avoid multiplication
        $sales = $sales->map(function ($sale) {
            $item = Item::where('no', $sale->item_code)->first();
            $sale->pack_due = $item ? $item->pack_due : null;
            return $sale;
        });

        $final_total = $sales->sum('total');

        $selectedGrnEntry = !empty($grnCode)
            ? GrnEntry::where('code', $grnCode)->first()
            : null;

        return view('dashboard.reports.weight-based-report', [
            'sales' => $sales,
            'selectedGrnCode' => $grnCode,
            'selectedGrnEntry' => $selectedGrnEntry,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'supplierCode' => $supplierCode,
            'filters' => $request->all(),
            'final_total' => $final_total,
        ]);
    }

    public function getGrnSalecodereport(Request $request)
    {
        $grnCode = $request->input('grn_code');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if (!$grnCode) {
            return redirect()->back()->withErrors('Please select a GRN code.');
        }

        // Determine which model to query based on the presence of a date range
        if ($startDate && $endDate) {
            // If both start_date and end_date are provided, query Salesadjustment
            $query = SalesHistory::query();

            // Apply the date filter using Carbon for precision
            $query->whereBetween('Date', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);

        } else {
            // Otherwise, query Sale (default behavior)
            $query = Sale::query();
        }

        // Apply the GRN code filter to the selected query
        $query->where('code', $grnCode);

        $sales = $query->orderBy('created_at', 'asc')->get();
        $selectedGrnEntry = GrnEntry::where('code', $grnCode)->first();

        return view('dashboard.reports.grn_sale_code_report', [
            'sales' => $sales,
            'selectedGrnCode' => $grnCode,
            'selectedGrnEntry' => $selectedGrnEntry,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'filters' => $request->all(),
        ]);
    }

    public function getGrnSalesOverviewReport()
    {
        // Fetch all GRN entries
        $grnEntries = GrnEntry::all();

        $reportData = [];

        foreach ($grnEntries->groupBy('code') as $code => $entries) {
            // --- GRN Totals ---
            $totalOriginalPacks = $entries->sum('original_packs');
            $totalOriginalWeight = $entries->sum('original_weight');

            // --- Total sales value ---
            $currentSales = Sale::where('code', $code)->get();
            $historicalSales = SalesHistory::where('code', $code)->get();
            $relatedSales = $currentSales->merge($historicalSales);
            $totalSalesValueForGrn = $relatedSales->sum('total');

            // Sum packs & weight from sales tables
            $soldPacksFromSales = $relatedSales->sum('packs');
            $soldWeightFromSales = $relatedSales->sum('weight');

            // --- Remaining Packs ---
            $remainingPacks = $totalOriginalPacks - $soldPacksFromSales;

            // --- Remaining Weight ---
            $remainingWeight = $totalOriginalWeight - $soldWeightFromSales;

            $reportData[] = [
                'date' => Carbon::parse($entries->first()->created_at)
                    ->timezone('Asia/Colombo')
                    ->format('Y-m-d H:i:s'),
                'grn_code' => $code,
                'item_name' => $entries->first()->item_name,
                'sp' => $entries->first()->SalesKGPrice,
                'original_packs' => $totalOriginalPacks,
                'original_weight' => $totalOriginalWeight,
                'total_sales_value' => $totalSalesValueForGrn,
                'remaining_packs' => $remainingPacks,
                'remaining_weight' => $remainingWeight,
                'sold_packs' => $soldPacksFromSales,
                'sold_weight' => $soldWeightFromSales,
            ];
        }

        // Sort the report data alphabetically by grn_code
        $reportData = collect($reportData)->sortBy('grn_code')->values();

        return view('dashboard.reports.grn_sales_overview_report', [
            'reportData' => $reportData
        ]);
    }



    public function getGrnSalesOverviewReport2()
    {
        // Fetch all GRN entries
        $grnEntries = GrnEntry::all();

        $reportData = [];

        // Group entries by item_name
        $grouped = $grnEntries->groupBy('item_name');

        foreach ($grouped as $itemName => $entries) {
            $originalPacks = 0;
            $originalWeight = 0;
            $soldPacks = 0;
            $soldWeight = 0;
            $totalSalesValue = 0;

            foreach ($entries as $grnEntry) {
                // Fetch all sales (current + history) for this GRN code
                $currentSales = Sale::where('code', $grnEntry->code)->get();
                $historicalSales = SalesHistory::where('code', $grnEntry->code)->get();
                $relatedSales = $currentSales->merge($historicalSales);

                // Sold quantities and values
                $totalSoldWeight = $relatedSales->sum('weight');
                $totalSoldPacks = $relatedSales->sum('packs');
                $totalSalesValueForGrn = $relatedSales->sum('total');

                // Add to totals
                $originalPacks += $grnEntry->original_packs;
                $originalWeight += $grnEntry->original_weight;
                $soldPacks += $totalSoldPacks;
                $soldWeight += $totalSoldWeight;
                $totalSalesValue += $totalSalesValueForGrn;
            }

            // Compute remaining after summing everything
            $remainingPacks = $originalPacks - $soldPacks;
            $remainingWeight = $originalWeight - $soldWeight;

            // Add to report
            $reportData[] = [
                'item_name' => $itemName,
                'original_packs' => $originalPacks,
                'original_weight' => $originalWeight,
                'sold_packs' => $soldPacks,
                'sold_weight' => $soldWeight,
                'remaining_packs' => $remainingPacks,
                'remaining_weight' => $remainingWeight,
                'total_sales_value' => $totalSalesValue,
            ];
        }

        // Group and sum by item_name to combine duplicates
        $finalReportData = collect($reportData)->groupBy('item_name')->map(function ($group) {
            return [
                'item_name' => $group->first()['item_name'],
                'original_packs' => $group->sum('original_packs'),
                'original_weight' => $group->sum('original_weight'),
                'sold_packs' => $group->sum('sold_packs'),
                'sold_weight' => $group->sum('sold_weight'),
                'remaining_packs' => $group->sum('remaining_packs'),
                'remaining_weight' => $group->sum('remaining_weight'),
                'total_sales_value' => $group->sum('total_sales_value'),
            ];
        })->values();

        return view('dashboard.reports.grn_sales_overview_report2', [
            'reportData' => $finalReportData
        ]);
    }
    public function downloadReport(Request $request, $reportType, $format)
    {
        // Fetch report data
        list($reportData, $headings, $reportTitle, $meta) = $this->getReportData($reportType, $request->all());

        // ------------------ EXCEL ------------------
        if ($format === 'excel') {
            $filename = str_replace(' ', '-', $reportTitle) . '_' . Carbon::now()->format('Y-m-d') . '.xlsx';
            return Excel::download(new DynamicReportExport($reportData, $headings), $filename);
        }

        // ------------------ PDF (mPDF) ------------------
        if ($format === 'pdf') {
            $filename = str_replace(' ', '-', $reportTitle) . '_' . Carbon::now()->format('Y-m-d') . '.pdf';

            try {
                // Initialize mPDF with Sinhala font
                $mpdf = new Mpdf([
                    'mode' => 'utf-8',
                    'format' => 'A4',
                    'default_font' => 'notosanssinhala',
                    'margin_top' => 15,
                    'margin_bottom' => 15,
                    'margin_left' => 10,
                    'margin_right' => 10,
                    'fontDir' => [public_path('fonts')],
                    'fontdata' => [
                        'notosanssinhala' => [
                            'R' => 'NotoSansSinhala-Regular.ttf',
                            'B' => 'NotoSansSinhala-Bold.ttf',
                        ]
                    ],
                ]);

                // Render Blade view as HTML
                $html = view('reports.generic_report_pdf', compact('reportData', 'headings', 'reportTitle', 'meta'))->render();

                $mpdf->WriteHTML($html);
                return $mpdf->Output($filename, 'D'); // Download PDF

            } catch (\Exception $e) {
                Log::error("PDF generation failed: " . $e->getMessage(), [
                    'reportType' => $reportType,
                    'filename' => $filename,
                    'error_file' => $e->getFile(),
                    'error_line' => $e->getLine(),
                ]);
                return back()->with('error', 'PDF generation failed: ' . $e->getMessage());
            }
        }

        abort(404, 'Invalid report format.');
    }
    protected function getReportData($reportType, $filters = [])
    {
        $reportData = collect();
        $headings = [];
        $reportTitle = 'Report';
        $meta = []; // ✅ Always initialize


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
                    ->when(isset($filters['item_code']), fn($q) => $q->where('item_code', $filters['item_code']))
                    ->when(isset($filters['supplier_code']) && $filters['supplier_code'] !== 'all', fn($q) => $q->where('supplier_code', $filters['supplier_code']))
                    ->when(isset($filters['start_date']), fn($q) => $q->whereDate('created_at', '>=', $filters['start_date']))
                    ->when(isset($filters['end_date']), fn($q) => $q->whereDate('created_at', '<=', $filters['end_date']));

                $records = $query->get();

                $meta = [
                    'Item' => $records->first() ? $records->first()->item_name . ' (' . $records->first()->item_code . ')' : null,
                ];

                $headings = ['බිල් අංකය', 'මලු', 'බර', 'මිල', 'එකතුව', 'ගෙණුම්කරු', 'GRN අංකය'];

                $reportData = $records->map(fn($row) => [
                    $row->bill_no,
                    $row->packs,
                    $row->weight,
                    $row->price_per_kg,
                    $row->total,
                    $row->customer_code,
                    $row->code,
                ]);

                // Totals row
                $numericIndexes = [1, 2, 4]; // packs, weight, total
                $totalsRow = [];
                foreach ($reportData->first() ?? [] as $index => $val) {
                    $totalsRow[$index] = in_array($index, $numericIndexes) ? $reportData->sum(fn($r) => $r[$index]) : ($index === 0 ? 'TOTAL' : '');
                }
                if ($reportData->isNotEmpty()) {
                    $reportData->push($totalsRow);
                }

                break;

            case 'grn-sales-report':
                $reportTitle = 'GRN-based Sales Report';

                // 1. Determine Model and table/column names
                // Note: The $filters array is passed into the report generation logic
                $startDate = $filters['start_date'] ?? null;
                $endDate = $filters['end_date'] ?? null;
                $supplierCode = $filters['supplier_code'] ?? null;
                $grnCode = $filters['grn_code'] ?? null;

                if ($startDate && $endDate) {
                    $model = SalesHistory::query();
                    $dateColumn = 'Date';
                } else {
                    $model = Sale::query();
                    $dateColumn = null;
                }

                // 2. Aggregate Sales Data (similar to getweight logic)
                $query = $model->selectRaw("
        item_code,
        MAX(item_name) as item_name,
        SUM(packs) as packs,
        SUM(weight) as weight,
        SUM(total) as total
    ");

                // Apply date range filter
                if ($dateColumn) {
                    $query->whereBetween($dateColumn, [
                        Carbon::parse($startDate)->startOfDay(),
                        Carbon::parse($endDate)->endOfDay()
                    ]);
                }

                // Apply supplier filter
                if (!empty($supplierCode)) {
                    $query->where('supplier_code', $supplierCode);
                }

                // Apply GRN code filter
                if (!empty($grnCode)) {
                    // The original logic had a complex OR for both tables.
                    // Since we determine the model first, we can simply filter on its 'code' column.
                    $query->where('code', $grnCode);
                }

                // Group and get results
                $records = $query
                    ->orderBy('item_code', 'asc')
                    ->groupBy('item_code', 'item_name')
                    ->get();

                // 3. Post-processing: Calculate price per kg and map pack_due
                $records = $records->map(function ($record) {
                    // Calculate price per kg based on aggregated total and weight
                    $record->price_per_kg = ($record->weight > 0) ? $record->total / $record->weight : 0;

                    // Fetch pack_due from 'items' table separately (requires the Item model)
                    // Ensure the Item model is correctly imported/namespaced: use App\Models\Item;
                    $item = \App\Models\Item::where('no', $record->item_code)->first();

                    // The column in the item table must be correct, using 'pack_due' as per original code
                    $record->pack_due = $item ? $item->pack_due : 0;

                    return $record;
                });


                // 4. Headings and Initialization (Updated)
                // Removed 'මිල (Rs/kg)' column as requested
                $headings = ['අයිතම කේතය', 'වර්ගය', 'බර (kg)', 'මලු', 'මලු ගාස්තුව (Rs)', 'ශුද්ධ එකතුව (Rs)'];

                $reportData = collect();
                $totalWeight = 0;
                $totalPacks = 0;
                $totalAmountNet = 0;      // Total after deducting pack due
                $totalPackDueCost = 0;
                $totalAmountGross = 0;    // Total from DB (Total Net + Total Pack Due)

                foreach ($records as $record) {
                    $itemCode = $record->item_code;
                    $itemName = $record->item_name;
                    $weight = $record->weight ?? 0;
                    // $pricePerKg = $record->price_per_kg ?? 0; // Removed from output
                    $packs = $record->packs ?? 0;
                    $packDue = $record->pack_due ?? 0;
                    $itemTotalGross = $record->total ?? 0; // Total as recorded in Sales/SalesHistory

                    // Calculate pack due cost
                    $packDueCost = $packs * $packDue;

                    // Net total per item = total from DB (Gross) - pack due cost
                    $netTotal = $itemTotalGross - $packDueCost;

                    // Push data (Removed pricePerKg column)
                    $reportData->push([
                        $itemCode,
                        $itemName,
                        number_format($weight, 2),
                        number_format($packs, 0),
                        number_format($packDueCost, 2),
                        number_format($netTotal, 2),
                    ]);

                    // Update totals
                    $totalWeight += $weight;
                    $totalPacks += $packs;
                    $totalPackDueCost += $packDueCost;
                    $totalAmountNet += $netTotal;
                    $totalAmountGross += $itemTotalGross; // Sum of the raw 'total' column
                }

                // 5. Add Totals (Adjusted for 6 columns)
                // Add empty row for separation
                $reportData->push(['', '', '', '', '', '']);

                // Totals row
                $reportData->push([
                    '',
                    'මුළු එකතුව:', // This is the descriptive label
                    number_format($totalWeight, 2),
                    number_format($totalPacks, 0),
                    number_format($totalPackDueCost, 2),
                    number_format($totalAmountNet, 2),
                ]);
                $reportData->push([
                    '',
                    '',
                    '',
                    '',
                    'අවසන් මුළු එකතුව:', // Moved to align with the final value
                    number_format($totalAmountGross, 2),
                ]);

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
                $meta = [
                    'තෝරාගත් GRN කේතය' => $records->first() ? $records->first()->code : null,
                ];

                $headings = ['දිනය', 'බිල් අංකය', 'ගෙණුම්කරු කේතය', 'බර', 'මිල (1kg)', 'මලු', 'මුළු මුදල'];

                $reportData = $records->map(function ($row) {
                    return [
                        $row->Date,
                        $row->bill_no,
                        $row->customer_code,
                        $row->weight,
                        $row->price_per_kg,
                        $row->packs,
                        $row->total,
                    ];
                });
                // Totals row
                $numericIndexes = [3, 5, 6]; // packs, weight, total
                $totalsRow = [];
                foreach ($reportData->first() ?? [] as $index => $val) {
                    $totalsRow[$index] = in_array($index, $numericIndexes) ? $reportData->sum(fn($r) => $r[$index]) : ($index === 0 ? 'TOTAL' : '');
                }
                if ($reportData->isNotEmpty()) {
                    $reportData->push($totalsRow);
                }
                break;
        }

        return [$reportData, $headings, $reportTitle, $meta ?? []];
    }
    public function salesAdjustmentReport(Request $request)
    {
        $code = $request->input('code');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $settingDate = Setting::value('value');

        $query = Salesadjustment::query();

        // If user did NOT select a date range → use settingDate filter
        if (!$startDate && !$endDate) {
            $query->whereDate('Date', $settingDate);
        } else {
            // If date range is provided → filter using Date column
            if ($startDate) {
                $query->whereDate('Date', '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate('Date', '<=', $endDate);
            }
        }

        // Apply code filter if provided
        if ($code) {
            $query->where('code', $code);
        }

        // Final results
        $entries = $query->orderBy('created_at', 'desc')->get();


        return view('dashboard.reports.salesadjustment', compact('entries', 'code', 'startDate', 'endDate'));
    }
    
    public function financialReport()
    {
        // Fetch today's Income/Expenses records
        $records = IncomeExpenses::select('customer_short_name', 'bill_no', 'description', 'amount', 'loan_type')
            ->whereIn('Date', Setting::pluck('value')) // compare Date with all values from Setting table
            ->get();

        $reportData = [];
        $totalDr = 0;
        $totalCr = 0;

        // 🆕 Fetch last_day_started_date row (contains Balance column)
        $balanceRow = Setting::where('key', 'last_day_started_date')
            ->whereColumn('value', '<>', 'Date_of_balance')
            ->first();

        if ($balanceRow) {
            $reportData[] = [
                'description' => 'Balance As At ' . \Carbon\Carbon::parse($balanceRow->value)->format('Y-m-d'),
                'dr' => $balanceRow->Balance, // Use the Balance column
                'cr' => null
            ];
            $totalDr += $balanceRow->Balance; // Add Balance to total DR
        }

        // Loop through Income/Expenses records
        foreach ($records as $record) {
            $dr = null;
            $cr = null;

            // Build description
            $desc = $record->customer_short_name;
            if (!empty($record->bill_no)) {
                $desc .= " ({$record->bill_no})";
            }
            $desc .= " - {$record->description}";

            // Determine DR or CR based on loan_type
            if (in_array($record->loan_type, ['old', 'ingoing'])) {
                $dr = $record->amount;
                $totalDr += $record->amount;
            } elseif (in_array($record->loan_type, ['today', 'outgoing'])) {
                $cr = $record->amount;
                $totalCr += $record->amount;
            }

            $reportData[] = [
                'description' => $desc,
                'dr' => $dr,
                'cr' => $cr
            ];
        }

        // Add Sales total
        $salesTotal = Sale::sum('total');
        $totalDr += $salesTotal;
        $reportData[] = [
            'description' => 'Sales Total',
            'dr' => $salesTotal,
            'cr' => null
        ];

        // Get Profit from SellingKGTotal
        $profitTotal = Sale::sum('SellingKGTotal');

        // Calculate Total Damages
        $totalDamages = GrnEntry::select(DB::raw('SUM(wasted_weight * PerKGPrice)'))
            ->value(DB::raw('SUM(wasted_weight * PerKGPrice)'));
        $totalDamages = $totalDamages ?? 0;

        return view('dashboard.reports.financial', compact(
            'reportData',
            'totalDr',
            'totalCr',
            'salesTotal',
            'profitTotal',
            'totalDamages'
        ));
    }
    public function salesReport(Request $request)
{
    // Determine which table to query
    $useHistory = $request->filled('start_date') || $request->filled('end_date');

    // Use SalesHistory if date range is provided, otherwise Sale
    $query = $useHistory
        ? SalesHistory::query()
        : Sale::query();


    // Supplier filter
    if ($request->filled('supplier_code')) {
        $query->where('supplier_code', $request->supplier_code);
    }

    // Item filter
    if ($request->filled('item_code')) {
        $query->where('item_code', $request->item_code);
    }

    // Customer short name filter
    if ($request->filled('customer_short_name')) {
        $search = $request->customer_short_name;
        $query->where(function ($q) use ($search) {
            $q->where('customer_code', 'like', '%' . $search . '%')
                ->orWhereIn('customer_code', function ($sub) use ($search) {
                    $sub->select('short_name')
                        ->from('customers')
                        ->where('name', 'like', '%' . $search . '%');
                });
        });
    }

    // Customer code filter
    if ($request->filled('customer_code')) {
        $query->where('customer_code', $request->customer_code);
    }

    // Bill No filter
    if ($request->filled('bill_no')) {
        $query->where('bill_no', $request->bill_no);
    }

    // Date range filter
    if ($request->filled('start_date') && $request->filled('end_date')) {
        $query->whereBetween('Date', [$request->start_date, $request->end_date]);
    } elseif ($request->filled('start_date')) {
        $query->where('Date', '>=', $request->start_date);
    } elseif ($request->filled('end_date')) {
        $query->where('Date', '<=', $request->end_date);
    }

    // Order based on dropdown selection
    if ($request->filled('order_by')) {
        if ($request->order_by == 'bill_no') {
            $query->orderBy('bill_no', 'asc'); // smallest → largest
        } elseif ($request->order_by == 'customer_code') {
            $query->orderBy('customer_code', 'asc'); // A → Z
        }
    } else {
        $query->orderBy('id', 'desc'); // default
    }

    // Fetch all sales
    $salesData = $query->get();

    return view('dashboard.reports.new_sales_report', compact('salesData'));
}

    public function grnReport(Request $request)
    {
        $code = $request->input('code');

        $grnQuery = GrnEntry::query();
        if ($code) {
            $grnQuery->where('code', $code);
        }
        $grnEntries = $grnQuery->get();

        $groupedData = [];

        foreach ($grnEntries as $entry) {
            // --- Current + Historical Sales ---
            $currentSales = Sale::where('code', $entry->code)->get([
                'code',
                'customer_code',
                'item_code',
                'supplier_code',
                'weight',
                'price_per_kg',
                'total',
                'packs',
                'item_name',
                'Date',
                'bill_no',
            ])->map(function ($sale) {
                $sale->type = 'Sales';
                return $sale;
            });

            $historicalSales = SalesHistory::where('code', $entry->code)->get([
                'code',
                'customer_code',
                'item_code',
                'supplier_code',
                'weight',
                'price_per_kg',
                'total',
                'packs',
                'item_name',
                'Date',
                'bill_no',
            ])->map(function ($sale) {
                $sale->type = 'Sales';
                return $sale;
            });

            $sales = $currentSales->concat($historicalSales);

            // --- GRN Transactions ---
            $grnTransactions = GrnEntry2::where('code', $entry->code)->get()->map(function ($txn) {
                return (object) [
                    'Date' => $txn->txn_date,
                    'type' => 'GRN',
                    'bill_no' => '-',
                    'customer_code' => '-',
                    'weight' => $txn->weight,
                    'price_per_kg' => '-',
                    'packs' => $txn->packs,
                    'total' => '-',
                ];
            });

            // --- Merge and mix Sales + GRN by date ---
            $allRows = $sales->concat($grnTransactions)
                ->map(function ($row) {
                    $row->sort_date = $row->Date ?? now();
                    return $row;
                })
                ->sortByDesc('sort_date')
                ->values();

            // --- Totals ---
            $totalSales = $sales->sum('total');
            $damageValue = $entry->wasted_weight * $entry->PerKGPrice;

            $groupedData[$entry->code] = [
                'purchase_price' => $entry->total_grn,
                'item_name' => $entry->item_name,
                'all_rows' => $allRows,
                'damage' => [
                    'wasted_packs' => $entry->wasted_packs,
                    'wasted_weight' => $entry->wasted_weight,
                    'damage_value' => $damageValue,
                ],
                'profit' => $entry->total_grn - $totalSales - $damageValue,
                'updated_at' => $entry->updated_at,
                'remaining_packs' => $entry->packs,
                'remaining_weight' => $entry->weight,
                'totalOriginalPacks' => $entry->original_packs,
                'totalOriginalWeight' => $entry->original_weight,
            ];
        }

        return view('dashboard.reports.grn', [
            'groupedData' => $groupedData,
            'selectedCode' => $code,
        ]);
    }





    public function sendDailyReport()
    {
        // Group by item_code and aggregate
        $sales = Sale::select('item_code', 'item_name')
            ->selectRaw('SUM(packs) as packs')
            ->selectRaw('SUM(weight) as weight')
            ->selectRaw('SUM(total) as total')
            ->groupBy('item_code', 'item_name')
            ->get();

        $reportData = [
            'sales' => $sales,
            'settingDate' => now()->format('Y-m-d')
        ];

        // Send email to multiple recipients
        Mail::to(['nethmavilhan2005@gmail.com', 'thrcorner@gmail.com', 'wey.b32@gmail.com'])
            ->send(new DailyReportMail($reportData));

        // Stay on the same page with a success message
        return back()->with('success', 'Daily report email sent successfully!');
    }

    public function emailChangesReport()
    {
        $settingDate = Setting::value('value');

        $query = Salesadjustment::query();

        // Always filter by the setting date (like your web report default)
        $query->whereDate('Date', $settingDate);

        // Final results
        $entries = $query->orderBy('created_at', 'desc')->get();

        // Send the email to both recipients (no grouping)
        Mail::to(['thrcorner@gmail.com', 'nethmavilhan2005@gmail.com', 'wey.b32@gmail.com'])
            ->send(new ChangeReportMail($entries, $settingDate));

        return redirect()->back()->with('success', 'Changes report email sent successfully!');
    }

    public function emailTotalSalesReport()
    {
        // Fetch the same data you would for your web report.
        $sales = Sale::all(); // Or your filtered query
        $grandTotal = $sales->sum('total');

        // Send the email to both recipients
        Mail::to(['thrcorner@gmail.com', 'nethmavilhan2005@gmail.com', 'wey.b32@gmail.com'])
            ->send(new TotalSalesReportMail($sales, $grandTotal));

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Total sales report email sent successfully!');
    }

    public function emailBillSummaryReport(Request $request)
    {
        // Start with the base query, exactly as in your salesReport method
        $query = Sale::query()->whereNotNull('bill_no')->where('bill_no', '<>', '');

        // Apply all the same filters from the salesReport method
        if ($request->filled('supplier_code')) {
            $query->where('supplier_code', $request->supplier_code);
        }

        if ($request->filled('item_code')) {
            $query->where('item_code', $request->item_code);
        }

        if ($request->filled('customer_short_name')) {
            $search = $request->customer_short_name;
            $query->where(function ($q) use ($search) {
                $q->where('customer_code', 'like', '%' . $search . '%')
                    ->orWhereIn('customer_code', function ($sub) use ($search) {
                        $sub->select('short_name')
                            ->from('customers')
                            ->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($request->filled('customer_code')) {
            $query->where('customer_code', $request->customer_code);
        }

        if ($request->filled('bill_no')) {
            $query->where('bill_no', $request->bill_no);
        }

        // Fetch the filtered sales data and group it by bill number
        $salesByBill = $query->get()->groupBy('bill_no');

        // Calculate the grand total
        $grandTotal = $salesByBill->sum(function ($sales) {
            return $sales->sum('total');
        });

        // Send the email to both recipients
        Mail::to(['thrcorner@gmail.com', 'nethmavilhan2005@gmail.com', 'wey.b32@gmail.com'])
            ->send(new BillSummaryReportMail($salesByBill, $grandTotal));

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Bill summary report email sent successfully!');
    }

    public function emailCreditReport(Request $request)
    {
        // Fetch loans. You can add filtering logic here if your original report page has filters.
        $settingDate = Setting::value('value');
        $loans = CustomersLoan::query()
            ->whereDate('Date', $settingDate);

        // Calculate totals, replicating the logic from your Blade file
        $receivedTotal = 0;
        $paidTotal = 0;
        foreach ($loans as $loan) {
            if ($loan->loan_type === 'old') {
                $receivedTotal += $loan->amount;
            } elseif ($loan->loan_type === 'today') {
                $paidTotal += $loan->amount;
            }
        }

        $netBalance = $paidTotal - $receivedTotal;

        // Send the email to both recipients
        Mail::to(['thrcorner@gmail.com', 'nethmavilhan2005@gmail.com', 'wey.b32@gmail.com'])
            ->send(new CreditReportMail($loans, $receivedTotal, $paidTotal, $netBalance));

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Credit report email sent successfully!');
    }

    public function emailItemWiseReport(Request $request)
    {
        $query = Sale::query();

        // Apply any filters from the request to the query
        if ($request->filled('item_code')) {
            $query->where('item_code', $request->item_code);
        }

        $sales = $query->get();

        // Calculate totals
        $total_packs = $sales->sum('packs');
        $total_weight = $sales->sum('weight');
        $total_amount = $sales->sum('total');

        Mail::to(['thrcorner@gmail.com', 'nethmavilhan2005@gmail.com', 'wey.b32@gmail.com'])
            ->send(new ItemWiseReportMail($sales, $total_packs, $total_weight, $total_amount));

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Item-wise report email sent successfully!');
    }
    public function emailGrnSalesReport(Request $request)
    {
        // Fetch all sales as there are no filters
        $sales = Sale::all();

        // Calculate totals
        $total_packs = $sales->sum('packs');
        $total_weight = $sales->sum('weight');
        $total_amount = $sales->sum('total');

        // Send the email to both addresses
        Mail::to(['thrcorner@gmail.com', 'nethmavilhan2005@gmail.com', 'wey.b32@gmail.com'])
            ->send(new GrnSalesReportMail($sales, $total_packs, $total_weight, $total_amount));

        // Redirect back with a success message
        return back()->with('success', 'GRN sales report email sent successfully!');
    }

    public function emailSupplierSalesReport(Request $request)
    {
        // Fetch all GRN entries
        $grnEntries = GrnEntry::all();
        $reportData = [];

        foreach ($grnEntries->groupBy('code') as $code => $entries) {
            // --- GRN Totals ---
            $totalOriginalPacks = $entries->sum('original_packs');
            $totalOriginalWeight = $entries->sum('original_weight');

            $remainingPacks = $entries->sum('packs');
            $remainingWeight = $entries->sum('weight');

            // --- Sold quantities ---
            $totalSoldPacks = $totalOriginalPacks - $remainingPacks;
            $totalSoldWeight = $totalOriginalWeight - $remainingWeight;

            // --- Total sales value ---
            $currentSales = Sale::where('code', $code)->get();
            $historicalSales = SalesHistory::where('code', $code)->get();
            $relatedSales = $currentSales->merge($historicalSales);
            $totalSalesValueForGrn = $relatedSales->sum('total');

            $reportData[] = [
                'grn_code' => $code,
                'item_name' => $entries->first()->item_name,
                'original_packs' => $totalOriginalPacks,
                'original_weight' => $totalOriginalWeight,
                'sold_packs' => $totalSoldPacks,
                'sold_weight' => $totalSoldWeight,
                'total_sales_value' => $totalSalesValueForGrn,
                'remaining_packs' => $remainingPacks,
                'remaining_weight' => $remainingWeight,
            ];
        }

        // Send the email to both addresses
        Mail::to(['thrcorner@gmail.com', 'nethmavilhan2005@gmail.com', 'wey.b32@gmail.com'])
            ->send(new SupplierSalesReportMail(collect($reportData)));

        return back()->with('success', 'Supplier sales report email sent successfully!');
    }

    // Example method to get report data (adjust based on your logic)
    private function getSupplierReportData()
    {

        $reportData = []; // Replace with your actual data fetching logic.
        return $reportData;
    }
    public function emailOverviewReport(Request $request)
    {
        // Fetch all GRN entries
        $grnEntries = GrnEntry::all();
        $reportData = [];

        // Group by item_name
        $grouped = $grnEntries->groupBy('item_name');

        foreach ($grouped as $itemName => $entries) {
            $originalPacks = 0;
            $originalWeight = 0;
            $soldPacks = 0;
            $soldWeight = 0;
            $remainingPacks = 0;
            $remainingWeight = 0;

            foreach ($entries as $grnEntry) {
                // Fetch current and historical sales for this GRN code
                $currentSales = Sale::where('code', $grnEntry->code)->get();
                $historicalSales = SalesHistory::where('code', $grnEntry->code)->get();
                $relatedSales = $currentSales->merge($historicalSales);

                // Sum original packs and weight
                $originalPacks += $grnEntry->original_packs;
                $originalWeight += $grnEntry->original_weight;

                // Sum sold packs and weight
                $soldPacks += $grnEntry->original_packs - $grnEntry->packs;
                $soldWeight += $grnEntry->original_weight - $grnEntry->weight;

                // Sum remaining packs and weight (direct from GRN entry)
                $remainingPacks += $grnEntry->packs;
                $remainingWeight += $grnEntry->weight;
            }

            $reportData[] = [
                'item_name' => $itemName,
                'original_packs' => $originalPacks,
                'original_weight' => $originalWeight,
                'sold_packs' => $soldPacks,
                'sold_weight' => $soldWeight,
                'remaining_packs' => $remainingPacks,
                'remaining_weight' => $remainingWeight,
            ];
        }

        // Send the email to both addresses
        Mail::to(['thrcorner@gmail.com', 'nethmavilhan2005@gmail.com', 'wey.b32@gmail.com'])
            ->send(new GrnSalesOverviewMail(collect($reportData)));

        return back()->with('success', 'Overview report email sent successfully!');
    }

    public function salesfinalReport(Request $request)
    {
        $query = Sale::query()->whereNotNull('bill_no')->where('bill_no', '<>', '');

        // Supplier filter
        if ($request->filled('supplier_code')) {
            $query->where('supplier_code', $request->supplier_code);
        }

        // Item filter
        if ($request->filled('item_code')) {
            $query->where('item_code', $request->item_code);
        }

        // Customer short name filter
        if ($request->filled('customer_short_name')) {
            $search = $request->customer_short_name;
            $query->where(function ($q) use ($search) {
                $q->where('customer_code', 'like', '%' . $search . '%')
                    ->orWhereIn('customer_code', function ($sub) use ($search) {
                        $sub->select('short_name')
                            ->from('customers')
                            ->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        // Customer code filter
        if ($request->filled('customer_code')) {
            $query->where('customer_code', $request->customer_code);
        }

        // Bill No filter
        if ($request->filled('bill_no')) {
            $query->where('bill_no', $request->bill_no);
        }

        $salesByBill = $query->get()->groupBy('bill_no');

        // Calculate grand total
        $grandTotal = $salesByBill->sum(function ($billSales) {
            return $billSales->sum('total');
        });

        // Send the email to both addresses
        try {
            Mail::to(['thrcorner@gmail.com', 'nethmavilhan2005@gmail.com', 'wey.b32@gmail.com'])
                ->send(new SalesReportMail($salesByBill, $grandTotal));

            return back()->with('success', 'Sales report email sent successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send email. ' . $e->getMessage());
        }
    }

    public function sendFinancialReportEmail()
    {
        // Initialize all variables at the start
        $reportData = [];
        $totalDr = 0;
        $totalCr = 0;

        // Fetch records from the database
        $settingDate = Setting::value('value');

        $records = IncomeExpenses::select('customer_short_name', 'bill_no', 'description', 'amount', 'loan_type')
            ->whereDate('created_at', $settingDate)
            ->get();

        // The foreach loop will be skipped if $records is empty
        foreach ($records as $record) {
            $dr = null;
            $cr = null;

            // Use null coalescing to provide default values for potentially null fields
            $customerShortName = $record->customer_short_name ?? 'N/A';
            $billNo = $record->bill_no ?? '';
            $itemDescription = $record->description ?? 'No Description';
            $amount = $record->amount ?? 0;
            $loanType = $record->loan_type ?? '';

            $desc = $customerShortName;
            if (!empty($billNo)) {
                $desc .= " ({$billNo})";
            }
            $desc .= " - {$itemDescription}";

            if (in_array($loanType, ['old', 'ingoing'])) {
                $dr = $amount;
                $totalDr += $amount;
            } elseif (in_array($loanType, ['today', 'outgoing'])) {
                $cr = $amount;
                $totalCr += $amount;
            }

            $reportData[] = [
                'description' => $desc,
                'dr' => $dr,
                'cr' => $cr
            ];
        }

        // Add Sales total
        $salesTotal = Sale::sum('total') ?? 0;
        $totalDr += $salesTotal;
        $reportData[] = [
            'description' => 'Sales Total',
            'dr' => $salesTotal,
            'cr' => null
        ];

        // Get Profit and Damages, with fallbacks
        $profitTotal = Sale::sum('SellingKGTotal') ?? 0;
        $totalDamages = GrnEntry::sum(DB::raw('wasted_weight * PerKGPrice')) ?? 0;

        // Log the data to find the problematic array entry
        Log::info('Report Data for Email:', ['data' => $reportData]);

        $data = compact('reportData', 'totalDr', 'totalCr', 'salesTotal', 'profitTotal', 'totalDamages');

        // Send the email to both addresses
        Mail::to(['thrcorner@gmail.com', 'nethmavilhan2005@gmail.com', 'wey.b32@gmail.com'])
            ->send(new FinancialReportMail($data));

        return back()->with('success', 'Financial report emailed successfully!');
    }

    // NEW: Method to send the email without filters
    public function sendLoanReportEmail()
    {
        $settingDate = Setting::value('value');
        if (!$settingDate) {
            $settingDate = now()->toDateString();
        }

        // Fetch all loans for the specified date without additional filters
        $loans = CustomersLoan::whereDate('Date', $settingDate)
            ->orderBy('Date', 'desc')
            ->get();

        // Send to multiple email addresses
        Mail::to(['thrcorner@gmail.com', 'nethmavilhan2005@gmail.com', 'wey.b32@gmail.com'])
            ->send(new LoanReportMail($loans));

        return back()->with('success', 'Loan report emailed successfully!');
    }

    public function exportToExcel(Request $request)
    {
        $code = $request->input('code');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // ✅ Get the Setting date value
        $settingDate = Setting::value('value');
        if (!$settingDate) {
            $settingDate = now()->toDateString();
        }

        // ✅ Pass the setting date to the export class
        return Excel::download(new SalesAdjustmentsExport($code, $startDate, $endDate, $settingDate), 'sales-adjustments.xlsx');
    }

    public function exportToPdf(Request $request)
    {
        $code = $request->input('code');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // ✅ Get the Setting date value
        $settingDate = Setting::value('value');
        if (!$settingDate) {
            $settingDate = now()->toDateString();
        }

        $query = Salesadjustment::query();

        // ✅ Apply filters
        if ($code) {
            $query->where('code', $code);
        }
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        // ✅ Include only records where 'Date' = $settingDate
        $query->whereDate('Date', $settingDate);

        // Fetch the filtered entries
        $entries = $query->orderBy('created_at', 'desc')->get();

        $reportTitle = 'විකුණුම් වෙනස් කිරීම් වාර්තාව'; // Sales Adjustment Report

        try {
            // Configure mPDF
            $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
            $fontDirs = $defaultConfig['fontDir'];

            $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
            $fontData = $defaultFontConfig['fontdata'];

            $mpdf = new \Mpdf\Mpdf([
                'fontDir' => array_merge($fontDirs, [
                    public_path('fonts')
                ]),
                'fontdata' => $fontData + [
                    'notosanssinhala' => [
                        'R' => 'NotoSansSinhala-Regular.ttf',
                        'B' => 'NotoSansSinhala-Bold.ttf',
                    ]
                ],
                'default_font' => 'notosanssinhala',
                'mode' => 'utf-8',
                'format' => 'A4-L', // A4 Landscape for more columns
                'margin_top' => 15,
                'margin_bottom' => 15,
                'margin_left' => 10,
                'margin_right' => 10,
            ]);

            // Pass filtered entries to the PDF view
            $html = view('dashboard.reports.salesadjustment_pdf', compact('entries', 'reportTitle'))->render();

            $mpdf->WriteHTML($html);
            $filename = str_replace(' ', '-', $reportTitle) . '_' . \Carbon\Carbon::now()->format('Y-m-d') . '.pdf';

            return $mpdf->Output($filename, 'D'); // 'D' for download

        } catch (\Exception $e) {
            \Log::error("PDF generation failed: " . $e->getMessage(), [
                'filename' => $filename ?? 'N/A',
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
            ]);
            return back()->with('error', 'PDF generation failed: ' . $e->getMessage());
        }
    }

    private function prepareGrnSalesOverviewData()
    {
        $grnEntries = GrnEntry::all();
        $reportData = [];

        foreach ($grnEntries->groupBy('code') as $code => $entries) {
            $totalOriginalPacks = $entries->sum('original_packs');
            $totalOriginalWeight = $entries->sum('original_weight');

            // Get all related sales first
            $currentSales = Sale::where('code', $code)->get();
            $historicalSales = SalesHistory::where('code', $code)->get();
            $relatedSales = $currentSales->merge($historicalSales);

            // Now calculate sold totals
            $totalSoldPacks = $relatedSales->sum('packs');
            $totalSoldWeight = $relatedSales->sum('weight');
            $totalSalesValueForGrn = $relatedSales->sum('total');

            // Remaining
            $remainingPacks = $totalOriginalPacks - $totalSoldPacks;
            $remainingWeight = $totalOriginalWeight - $totalSoldWeight;

            $reportData[] = [
                'date' => Carbon::parse($entries->first()->created_at)->timezone('Asia/Colombo')->format('Y-m-d H:i:s'),
                'grn_code' => $code,
                'item_name' => $entries->first()->item_name,
                'price' => $entries->first()->SalesKGPrice,
                'original_packs' => $totalOriginalPacks,
                'original_weight' => $totalOriginalWeight,
                'sold_packs' => $totalSoldPacks,
                'sold_weight' => $totalSoldWeight,
                'total_sales_value' => $totalSalesValueForGrn,
                'remaining_packs' => $remainingPacks,
                'remaining_weight' => $remainingWeight,
            ];
        }
        $reportData = collect($reportData)->sortBy('grn_code')->values();


        return $reportData;
    }
    public function downloadGrnSalesOverviewReport(Request $request)
    {
        $reportData = $this->prepareGrnSalesOverviewData();
        $reportTitle = 'විකිණුම්/බර මත්තෙහි ඉතිරි වාර්තාව';

        // Handle PDF format
        if ($request->get('format') === 'pdf') {
            $filename = str_replace(' ', '-', $reportTitle) . '_' . Carbon::now()->format('Y-m-d') . '.pdf';

            try {
                $defaultConfig = (new ConfigVariables())->getDefaults();
                $fontDirs = $defaultConfig['fontDir'];
                $defaultFontConfig = (new FontVariables())->getDefaults();
                $fontData = $defaultFontConfig['fontdata'];

                $mpdf = new Mpdf([
                    'fontDir' => array_merge($fontDirs, [public_path('fonts')]),
                    'fontdata' => $fontData + [
                        'notosanssinhala' => [
                            'R' => 'NotoSansSinhala-Regular.ttf',
                            'B' => 'NotoSansSinhala-Bold.ttf',
                        ]
                    ],
                    'default_font' => 'notosanssinhala',
                    'mode' => 'utf-8',
                    'format' => 'A4-L',
                    'margin_top' => 15,
                    'margin_bottom' => 15,
                    'margin_left' => 10,
                    'margin_right' => 10,
                ]);

                $html = view('dashboard.reports.grn_sales_overview_pdf', compact('reportData', 'reportTitle'))->render();
                $mpdf->WriteHTML($html);
                return $mpdf->Output($filename, 'D');

            } catch (\Exception $e) {
                Log::error("PDF generation failed: " . $e->getMessage());
                return back()->with('error', 'PDF generation failed: ' . $e->getMessage());
            }
        }

        // Handle Excel format
        if ($request->get('format') === 'excel') {
            return Excel::download(new \App\Exports\GrnSalesOverviewExport($reportData), 'grn-sales-overview.xlsx');
        }

        return back()->with('error', 'Invalid export format.');
    }


    public function downloadGrnOverviewReport2(Request $request)
    {
        try {
            // Get the report data using the same logic as the controller method
            $grnEntries = GrnEntry::all();
            $reportData = [];

            // Group entries by item_name
            $grouped = $grnEntries->groupBy('item_name');

            foreach ($grouped as $itemName => $entries) {
                $originalPacks = 0;
                $originalWeight = 0;
                $soldPacks = 0;
                $soldWeight = 0;
                $totalSalesValue = 0;

                foreach ($entries as $grnEntry) {
                    // Fetch all sales (current + history) for this GRN code
                    $currentSales = Sale::where('code', $grnEntry->code)->get();
                    $historicalSales = SalesHistory::where('code', $grnEntry->code)->get();
                    $relatedSales = $currentSales->merge($historicalSales);

                    // Sold quantities and values
                    $totalSoldWeight = $relatedSales->sum('weight');
                    $totalSoldPacks = $relatedSales->sum('packs');
                    $totalSalesValueForGrn = $relatedSales->sum('total');

                    // Add to totals
                    $originalPacks += $grnEntry->original_packs;
                    $originalWeight += $grnEntry->original_weight;
                    $soldPacks += $totalSoldPacks;
                    $soldWeight += $totalSoldWeight;
                    $totalSalesValue += $totalSalesValueForGrn;
                }

                // Compute remaining after summing everything
                $remainingPacks = $originalPacks - $soldPacks;
                $remainingWeight = $originalWeight - $soldWeight;

                // Add to report
                $reportData[] = [
                    'item_name' => $itemName,
                    'original_packs' => $originalPacks,
                    'original_weight' => $originalWeight,
                    'sold_packs' => $soldPacks,
                    'sold_weight' => $soldWeight,
                    'remaining_packs' => $remainingPacks,
                    'remaining_weight' => $remainingWeight,
                    'total_sales_value' => $totalSalesValue,
                ];
            }

            // Group and sum by item_name to combine duplicates
            $finalReportData = collect($reportData)->groupBy('item_name')->map(function ($group) {
                return [
                    'item_name' => $group->first()['item_name'],
                    'original_packs' => $group->sum('original_packs'),
                    'original_weight' => $group->sum('original_weight'),
                    'sold_packs' => $group->sum('sold_packs'),
                    'sold_weight' => $group->sum('sold_weight'),
                    'remaining_packs' => $group->sum('remaining_packs'),
                    'remaining_weight' => $group->sum('remaining_weight'),
                    'total_sales_value' => $group->sum('total_sales_value'),
                ];
            })->values();

            $reportTitle = 'ඉතිරි වාර්තාව';

            // Excel export
            if ($request->get('format') === 'excel') {
                return Excel::download(new GrnOverviewExport($finalReportData->toArray()), 'stock-overview.xlsx');
            }

            // PDF export
            if ($request->get('format') === 'pdf') {
                $filename = str_replace(' ', '-', $reportTitle) . '_' . Carbon::now()->format('Y-m-d') . '.pdf';

                try {
                    // Use simpler mPDF configuration without complex font features
                    $mpdf = new Mpdf([
                        'mode' => 'utf-8',
                        'format' => 'A4',
                        'default_font' => 'freesans', // Use built-in font that supports UTF-8
                        'margin_top' => 15,
                        'margin_bottom' => 15,
                        'margin_left' => 10,
                        'margin_right' => 10,
                        'autoScriptToLang' => true,
                        'autoLangToFont' => true,
                    ]);

                    // Set metadata
                    $mpdf->SetTitle($reportTitle);
                    $mpdf->SetAuthor('Your System');

                    // Render the view with simpler styling
                    $html = view('dashboard.reports.grn_sales_overview_pdf2', [
                        'reportData' => $finalReportData,
                        'reportTitle' => $reportTitle
                    ])->render();

                    // Write HTML to PDF
                    $mpdf->WriteHTML($html);

                    // Output the PDF
                    return response($mpdf->Output($filename, 'D'), 200, [
                        'Content-Type' => 'application/pdf',
                        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                    ]);

                } catch (\Exception $e) {
                    Log::error("PDF generation failed: " . $e->getMessage());
                    Log::error("Stack trace: " . $e->getTraceAsString());
                    return back()->with('error', 'PDF generation failed: ' . $e->getMessage());
                }
            }

            return back()->with('error', 'Invalid export format.');

        } catch (\Exception $e) {
            Log::error("Download method failed: " . $e->getMessage());
            return back()->with('error', 'Report generation failed: ' . $e->getMessage());
        }
    }
    public function downloadSalesReport(Request $request)
    {
        // Determine which table to query - replicate logic from salesReport
        $useHistory = $request->filled('start_date') || $request->filled('end_date');

        // Use SalesHistory if date range is provided, otherwise Sale
        $query = $useHistory
            ? SalesHistory::query()
            : Sale::query();

        // Include only processed sales
        $query->where('Processed', 'Y');

        // Apply all filters from the salesReport method
        if ($request->filled('supplier_code')) {
            $query->where('supplier_code', $request->supplier_code);
        }

        if ($request->filled('item_code')) {
            $query->where('item_code', $request->item_code);
        }

        if ($request->filled('customer_short_name')) {
            $search = $request->customer_short_name;
            $query->where(function ($q) use ($search) {
                $q->where('customer_code', 'like', '%' . $search . '%')
                    ->orWhereIn('customer_code', function ($sub) use ($search) {
                        $sub->select('short_name')
                            ->from('customers')
                            ->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($request->filled('customer_code')) {
            $query->where('customer_code', $request->customer_code);
        }

        if ($request->filled('bill_no')) {
            $query->where('bill_no', $request->bill_no);
        }

        // Date range filter (only applied if provided)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('Date', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('start_date')) {
            $query->where('Date', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->where('Date', '<=', $request->end_date);
        }

        // Order by sales ID in descending order (highest first) - same as salesReport
        $query->orderBy('id', 'DESC');

        // Get all processed sales (no grouping by bill number) - same as salesReport
        $salesData = $query->get();
        $reportTitle = 'Sales Report';

        // Handle Excel format
        if ($request->get('format') === 'excel') {
            return Excel::download(new \App\Exports\BillSummaryExport($salesData), 'sales-report.xlsx');
        }

        // Handle PDF format
        if ($request->get('format') === 'pdf') {
            $filename = str_replace(' ', '-', $reportTitle) . '_' . Carbon::now()->format('Y-m-d') . '.pdf';

            try {
                $defaultConfig = (new ConfigVariables())->getDefaults();
                $fontDirs = $defaultConfig['fontDir'];
                $defaultFontConfig = (new FontVariables())->getDefaults();
                $fontData = $defaultFontConfig['fontdata'];

                $mpdf = new Mpdf([
                    'fontDir' => array_merge($fontDirs, [public_path('fonts')]),
                    'fontdata' => $fontData + [
                        'notosanssinhala' => [
                            'R' => 'NotoSansSinhala-Regular.ttf',
                            'B' => 'NotoSansSinhala-Bold.ttf',
                        ]
                    ],
                    'default_font' => 'notosanssinhala',
                    'mode' => 'utf-8',
                    'format' => 'A4-P',
                    'margin_top' => 15,
                    'margin_bottom' => 15,
                    'margin_left' => 10,
                    'margin_right' => 10,
                ]);

                // Use the same view as salesReport but for PDF
                $html = view('dashboard.reports.sales_report_pdf', compact('salesData'))->render();
                $mpdf->WriteHTML($html);
                return $mpdf->Output($filename, 'D');

            } catch (\Exception $e) {
                Log::error("PDF generation failed: " . $e->getMessage());
                return back()->with('error', 'PDF generation failed: ' . $e->getMessage());
            }
        }
        return back()->with('error', 'Invalid export format.');
    }
    public function sendGrnEmail(Request $request)
    {
        $code = $request->input('code');

        $grnEntries = GrnEntry::when($code, fn($q) => $q->where('code', $code))->get();

        $groupedData = [];

        foreach ($grnEntries as $entry) {
            $sales = Sale::where('code', $entry->code)->get([
                'code',
                'customer_code',
                'item_code',
                'supplier_code',
                'weight',
                'price_per_kg',
                'total',
                'packs',
                'item_name',
                'Date',
                'bill_no'
            ]);

            if ($sales->isEmpty()) {
                $sales = SalesHistory::where('code', $entry->code)->get([
                    'code',
                    'customer_code',
                    'item_code',
                    'supplier_code',
                    'weight',
                    'price_per_kg',
                    'total',
                    'packs',
                    'item_name',
                    'Date',
                    'bill_no'
                ]);
            }

            $damageValue = $entry->wasted_weight * $entry->PerKGPrice;

            $totalSoldPacks = $sales->sum('packs');
            $totalSoldWeight = $sales->sum('weight');

            $groupedData[$entry->code] = [
                'purchase_price' => $entry->total_grn,
                'item_name' => $entry->item_name,
                'sales' => $sales,
                'damage' => [
                    'wasted_packs' => $entry->wasted_packs,
                    'wasted_weight' => $entry->wasted_weight,
                    'damage_value' => $damageValue
                ],
                'profit' => $entry->total_grn - $sales->sum('total') - $damageValue,
                'updated_at' => $entry->updated_at,
                'remaining_packs' => $entry->original_packs - $totalSoldPacks,
                'remaining_weight' => $entry->original_weight - $totalSoldWeight,
                'totalOriginalPacks' => $entry->original_packs,
                'totalOriginalWeight' => $entry->original_weight,
            ];
        }

        Mail::to(['thrcorner@gmail.com', 'nethmavilhan2005@gmail.com', 'wey.b32@gmail.com'])
            ->send(new GrnbladeReportMail($groupedData));

        return back()->with('success', 'GRN Report has been sent successfully!');
    }
    public function exportPdf(Request $request)
    {
        $code = $request->input('code');
        $grnEntries = GrnEntry::when($code, fn($q) => $q->where('code', $code))->get();

        $groupedData = [];

        foreach ($grnEntries as $entry) {
            $sales = Sale::where('code', $entry->code)->get();
            if ($sales->isEmpty()) {
                $sales = SalesHistory::where('code', $entry->code)->get();
            }

            $damageValue = $entry->wasted_weight * $entry->PerKGPrice;
            $totalSoldPacks = $sales->sum('packs');
            $totalSoldWeight = $sales->sum('weight');

            $groupedData[$entry->code] = [
                'purchase_price' => $entry->total_grn,
                'item_name' => $entry->item_name,
                'sales' => $sales,
                'damage' => [
                    'wasted_packs' => $entry->wasted_packs,
                    'wasted_weight' => $entry->wasted_weight,
                    'damage_value' => $damageValue
                ],
                'profit' => $entry->total_grn - $sales->sum('total') - $damageValue,
                'updated_at' => $entry->updated_at,
                'remaining_packs' => $entry->original_packs - $totalSoldPacks,
                'remaining_weight' => $entry->original_weight - $totalSoldWeight,
                'totalOriginalPacks' => $entry->original_packs,
                'totalOriginalWeight' => $entry->original_weight,
            ];
        }

        // --- mPDF Setup for Sinhala ---
        $fontDirs = (new ConfigVariables())->getDefaults()['fontDir'];
        $fontData = (new FontVariables())->getDefaults()['fontdata'];

        $mpdf = new Mpdf([
            'fontDir' => array_merge($fontDirs, [public_path('fonts')]),
            'fontdata' => $fontData + [
                'notosanssinhala' => [
                    'R' => 'NotoSansSinhala-Regular.ttf',
                    'B' => 'NotoSansSinhala-Bold.ttf',
                ]
            ],
            'default_font' => 'notosanssinhala',
            'mode' => 'utf-8',
            'format' => 'A4-P',
            'margin_top' => 15,
            'margin_bottom' => 15,
            'margin_left' => 10,
            'margin_right' => 10,
        ]);

        // Load Blade HTML
        $html = view('dashboard.reports.grn_report_pdf', compact('groupedData'))->render();

        $mpdf->WriteHTML($html);
        $fileName = 'GRN_Report_' . date('Ymd_His') . '.pdf';

        return response($mpdf->Output($fileName, 'S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }
    public function exportExcel(Request $request)
    {
        $code = $request->input('code');
        return Excel::download(new GrnExport($code), 'GRN_Report_' . date('Ymd_His') . '.xlsx');
    }
    public function returnsReport()
    {
        $data = IncomeExpenses::select(
            'GRN_Code',
            'Item_Code',
            'bill_no',
            'weight',
            'packs',
            'Reason'
        )
            ->whereNotNull('Reason') // only records with a value in Reason
            ->get();

        return view('dashboard.reports.returns_report', compact('data'));
    }
    public function chequePaymentsReport(Request $request)
    {
        $query = IncomeExpenses::whereNotNull('cheque_no')
            ->where('cheque_no', '<>', '');

        // Apply date range filter if provided
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('cheque_date', [
                $request->start_date,
                $request->end_date
            ]);
        }

        $chequePayments = $query->orderBy('cheque_date', 'desc')->get();

        // Pass start/end dates back to the view to retain values
        return view('dashboard.reports.cheque_payments', [
            'chequePayments' => $chequePayments,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date
        ]);
    }
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Realized,Non realized,Return',
        ]);

        $payment = IncomeExpenses::findOrFail($id);
        $payment->status = $request->status;
        $payment->save();

        return response()->json(['success' => true, 'status' => $payment->status]);
    }
    public function generateReport()
    {
        $grnEntries = GrnEntry::all();
        $dayStartReportData = [];

        // --- Day Start Report ---
        foreach ($grnEntries->groupBy('code') as $code => $entries) {
            $totalOriginalPacks = $entries->sum('original_packs');
            $totalOriginalWeight = $entries->sum('original_weight');

            $remainingPacks = $entries->sum('packs');
            $remainingWeight = $entries->sum('weight');

            $totalSoldPacks = $totalOriginalPacks - $remainingPacks;
            $totalSoldWeight = $totalOriginalWeight - $remainingWeight;

            $currentSales = Sale::where('code', $code)->get();
            $historicalSales = SalesHistory::where('code', $code)->get();
            $relatedSales = $currentSales->merge($historicalSales);
            $totalSalesValue = $relatedSales->sum('total');

            $totalWastedPacks = $entries->sum('wasted_packs');
            $totalWastedWeight = $entries->sum('wasted_weight');

            $dayStartReportData[] = [
                'date' => Carbon::parse($entries->first()->created_at)
                    ->timezone('Asia/Colombo')
                    ->format('Y-m-d H:i:s'),
                'grn_code' => $code,
                'item_name' => $entries->first()->item_name,
                'original_packs' => $totalOriginalPacks,
                'original_weight' => $totalOriginalWeight,
                'sold_packs' => $totalSoldPacks,
                'sold_weight' => $totalSoldWeight,
                'total_sales_value' => $totalSalesValue,
                'remaining_packs' => $remainingPacks,
                'remaining_weight' => $remainingWeight,
                'totalWastedPacks' => $totalWastedPacks,
                'totalWastedWeight' => $totalWastedWeight,
            ];
        }

        // --- GRN Report ---
        $grnReportData = [];
        $grouped = $grnEntries->groupBy('item_name');
        foreach ($grouped as $itemName => $entries) {
            $originalPacks = $originalWeight = $soldPacks = $soldWeight = $totalSalesValue = $remainingPacks = $remainingWeight = 0;

            foreach ($entries as $grnEntry) {
                $currentSales = Sale::where('code', $grnEntry->code)->get();
                $historicalSales = SalesHistory::where('code', $grnEntry->code)->get();
                $relatedSales = $currentSales->merge($historicalSales);
                $totalSalesValueForGrn = $relatedSales->sum('total');

                $originalPacks += $grnEntry->original_packs;
                $originalWeight += $grnEntry->original_weight;

                $soldPacks += $grnEntry->original_packs - $grnEntry->packs;
                $soldWeight += $grnEntry->original_weight - $grnEntry->weight;

                $remainingPacks += $grnEntry->packs;
                $remainingWeight += $grnEntry->weight;

                $totalSalesValue += $totalSalesValueForGrn;
            }

            $grnReportData[] = [
                'item_name' => $itemName,
                'original_packs' => $originalPacks,
                'original_weight' => $originalWeight,
                'sold_packs' => $soldPacks,
                'sold_weight' => $soldWeight,
                'total_sales_value' => $totalSalesValue,
                'remaining_packs' => $remainingPacks,
                'remaining_weight' => $remainingWeight,
            ];
        }

        // --- Weight-Based Report ---
        $weightBasedReportData = Sale::selectRaw('item_name, item_code, SUM(packs) as packs, SUM(weight) as weight, SUM(total) as total')
            ->groupBy('item_name', 'item_code')
            ->orderBy('item_name', 'asc')
            ->get();

        $salesByBill = Sale::query()
            ->whereNotNull('bill_no')
            ->where('bill_no', '<>', '')
            ->get()
            ->groupBy('bill_no');

        $settingDate = Setting::value('value');

        // --- Sales Adjustments ---
        $salesadjustments = Salesadjustment::whereDate('Date', $settingDate)
            ->orderBy('created_at', 'desc')
            ->get();

        // --- Financial Report ---
        $financialRecords = IncomeExpenses::select('customer_short_name', 'bill_no', 'description', 'amount', 'loan_type')
            ->whereDate('Date', $settingDate)
            ->get() ?? collect([]);

        $financialReportData = [];
        $totalDr = $totalCr = 0;

        foreach ($financialRecords as $record) {
            $dr = $cr = null;
            $desc = $record->customer_short_name;
            if (!empty($record->bill_no))
                $desc .= " ({$record->bill_no})";
            $desc .= " - {$record->description}";

            if (in_array($record->loan_type, ['old', 'ingoing'])) {
                $dr = $record->amount;
                $totalDr += $record->amount;
            } elseif (in_array($record->loan_type, ['today', 'outgoing'])) {
                $cr = $record->amount;
                $totalCr += $record->amount;
            }

            $financialReportData[] = [
                'description' => $desc,
                'dr' => $dr,
                'cr' => $cr
            ];
        }

        $salesTotal = Sale::sum('total');
        $totalDr += $salesTotal;
        $financialReportData[] = [
            'description' => 'Sales Total',
            'dr' => $salesTotal,
            'cr' => null
        ];

        $profitTotal = Sale::sum('SellingKGTotal');
        $totalDamages = GrnEntry::select(DB::raw('SUM(wasted_weight * PerKGPrice)'))
            ->value(DB::raw('SUM(wasted_weight * PerKGPrice)')) ?? 0;

        // --- Loans ---
        $allLoans = CustomersLoan::all() ?? collect([]);
        $groupedLoans = $allLoans->groupBy('customer_short_name');
        $finalLoans = collect([]);

        foreach ($groupedLoans as $customerShortName => $loans) {
            $lastOldLoan = $loans->where('loan_type', 'old')
                ->sortByDesc(fn($l) => Carbon::parse($l->created_at))
                ->first();

            $firstTodayAfterOld = $loans->filter(function ($l) use ($lastOldLoan) {
                return $l->loan_type === 'today' &&
                    Carbon::parse($l->created_at) > ($lastOldLoan ? Carbon::parse($lastOldLoan->created_at) : Carbon::parse('1970-01-01'));
            })->sortBy(fn($l) => Carbon::parse($l->created_at))
                ->first();

            $highlightColor = null;

            if ($lastOldLoan && $firstTodayAfterOld) {
                $daysBetweenLoans = Carbon::parse($lastOldLoan->created_at)->diffInDays(Carbon::parse($firstTodayAfterOld->created_at));
                if ($daysBetweenLoans > 30)
                    $highlightColor = 'red-highlight';
                elseif ($daysBetweenLoans >= 14)
                    $highlightColor = 'blue-highlight';

                $extraTodayLoanExists = $loans->filter(fn($l) => $l->loan_type === 'today' && Carbon::parse($l->created_at) > Carbon::parse($firstTodayAfterOld->created_at))->count() > 0;
                if ($extraTodayLoanExists)
                    $highlightColor = null;
            } elseif ($lastOldLoan && !$firstTodayAfterOld) {
                $daysSinceLastOldLoan = Carbon::parse($lastOldLoan->created_at)->diffInDays(Carbon::now());
                if ($daysSinceLastOldLoan > 30)
                    $highlightColor = 'red-highlight';
                elseif ($daysSinceLastOldLoan >= 14)
                    $highlightColor = 'blue-highlight';
            }

            $totalToday = $loans->where('loan_type', 'today')->sum('amount');
            $totalOld = $loans->where('loan_type', 'old')->sum('amount');
            $totalAmount = $totalToday - $totalOld;

            $finalLoans->push((object) [
                'customer_short_name' => $customerShortName,
                'total_amount' => $totalAmount,
                'highlight_color' => $highlightColor,
            ]);
        }

        // --- Send Emails ---
        Mail::send(new CombinedReportsMail(
            $dayStartReportData,
            $grnReportData,
            $grnEntries,
            now(), // or your $dayStartDate
            $weightBasedReportData,
            salesByBill: $salesByBill,
            salesadjustments: $salesadjustments,
            financialReportData: $financialReportData,
            financialTotalDr: $totalDr,
            financialTotalCr: $totalCr,
            financialProfit: $profitTotal,
            financialDamages: $totalDamages,
            profitTotal: $profitTotal,
            totalDamages: $totalDamages,
            loans: $allLoans,
            finalLoans: $finalLoans,
        ));

        Mail::send(new CombinedReportsMail2(
            $dayStartReportData,
            $grnReportData,
            $grnEntries,
            now(),
            $weightBasedReportData,
            salesByBill: $salesByBill,
            salesadjustments: $salesadjustments,
            financialReportData: $financialReportData,
            financialTotalDr: $totalDr,
            financialTotalCr: $totalCr,
            financialProfit: $profitTotal,
            financialDamages: $totalDamages,
            profitTotal: $profitTotal,
            totalDamages: $totalDamages,
            loans: $allLoans,
            finalLoans: $finalLoans,
        ));

        return back()->with('success', 'Report generated and emails sent successfully!');
    }
    //items pdf and ecxel export
     public function exportItemsExcel(Request $request)
    {
        // Get items same as your index filtering if any
        $items = Item::query()
    ->orderBy('no', 'asc') // ascending (A → Z)
    ->get();


        // instantiate export with items
        return Excel::download(new ItemsExport($items), 'items_list.xlsx');
    }

    // PDF download
    public function exportItemsPdf(Request $request)
{
   $items = Item::query()
    ->orderBy('no', 'asc') // ascending (A → Z)
    ->get();


    // --- mPDF Setup for Sinhala ---
    $fontDirs = (new ConfigVariables())->getDefaults()['fontDir'];
    $fontData = (new FontVariables())->getDefaults()['fontdata'];

    $mpdf = new Mpdf([
        'fontDir' => array_merge($fontDirs, [public_path('fonts')]),
        'fontdata' => $fontData + [
            'notosanssinhala' => [
                'R' => 'NotoSansSinhala-Regular.ttf',
                'B' => 'NotoSansSinhala-Bold.ttf',
            ],
        ],
        'default_font' => 'notosanssinhala',
        'mode' => 'utf-8',
        'format' => 'A4-P', // portrait
        'margin_top' => 15,
        'margin_bottom' => 15,
        'margin_left' => 10,
        'margin_right' => 10,
    ]);

    // --- Load your Blade view ---
    $html = view('dashboard.reports.items_excelpdf', compact('items'))->render();

    $mpdf->WriteHTML($html);

    // --- File name ---
    $fileName = 'Items_List_' . date('Ymd_His') . '.pdf';

    // --- Return as download ---
    return response($mpdf->Output($fileName, 'S'), 200)
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
}
 public function showReport()
    {
        // Get the report using the optimized method
        $report = IncomeExpenses::generateReport();
        
        return view('dashboard.reports.loan-report2', compact('report'));
    }
    
    public function refreshReport(Request $request)
    {
        $report = IncomeExpenses::generateReport();
        
        if ($request->ajax()) {
            return view('reports.partials.report-table', compact('report'))->render();
        }
        
        return back();
    }



}