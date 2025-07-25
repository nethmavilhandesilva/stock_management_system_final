<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use Illuminate\Support\Carbon;

class BillController extends Controller
{
    public function finalizeAndPrint(Request $request)
    {
        $saleIds = $request->input('sale_ids');
       
        $transportCost = $request->input('transport_cost', 0.00);
        $laborCost = $request->input('labor_cost', 0.00);

        // 1. Generate Automatic 4-Digit Bill Number
        $lastSaleWithBillNo = Sale::whereNotNull('bill_no')
                                    ->orderBy('bill_no', 'desc')
                                    ->first();

        $lastBillNumber = $lastSaleWithBillNo ? (int) $lastSaleWithBillNo->bill_no : 0;
        $newBillNumber = str_pad($lastBillNumber + 1, 4, '0', STR_PAD_LEFT);

        // 2. Update Sales Records: bill_no, is_printed, and is_processed
        $salesToUpdate = Sale::whereIn('id', $saleIds)->get();

        if ($salesToUpdate->isEmpty()) {
            return response()->json(['error' => 'No sales found to finalize and print.'], 404);
        }

        Sale::whereIn('id', $saleIds)->update([
            'bill_no' => $newBillNumber,
            'bill_printed' => 'Y', // Set is_printed to 'Y'
            'Processed' => 'Y', // Set is_processed to 'Y'
            'updated_at' => Carbon::now() // Update timestamp
        ]);

        // Re-fetch the updated sales to get the latest data including the bill_no for the template
        $salesForTemplate = Sale::whereIn('id', $saleIds)->get();

        // Calculate overall total for the bill from the fetched sales data
        $totalBillValue = $salesForTemplate->sum('total');

        // 3. Prepare data for the print template
        $billDataForTemplate = (object) [
            'bill_number' => $newBillNumber,
          
            'date' => Carbon::now(),
            'transport_cost' => $transportCost,
            'labor_cost' => $laborCost,
            'total_value' => $totalBillValue,
            'items' => $salesForTemplate->map(function($sale) {
                return (object) [
                    'item_name' => $sale->item_name,
                    'kilograms' => $sale->weight,
                    'rate' => $sale->price_per_kg,
                    'value' => $sale->total,
                    'additional_note' => "(" . $sale->item_name . " " . number_format($sale->weight, 0) . "/" . number_format($sale->packs, 0) . ")"
                ];
            })
        ];

        // 4. Render the print template and return its HTML
        $html = view('dashboard.bills.print_template', ['bill' => $billDataForTemplate])->render();

        return response()->json(['html' => $html, 'bill_number' => $newBillNumber, 'message' => 'Bill finalized and sales marked as printed and processed.']);
    }
}