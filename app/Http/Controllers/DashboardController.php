<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\GRNEntry;

class DashboardController extends Controller
{
    public function index()
{
    $customers = Customer::select('short_name','name')->get();
    $entries = GrnEntry::orderBy('txn_date','desc')->get();
    $sales = \App\Models\Sale::whereNull('Processed')->orWhere('Processed','N')->get(); // or whatever selection you want
    $totalSum = $sales->sum(function($s){ return ($s->weight * $s->price_per_kg); });

    return view('reactdashboard.sales.entry', compact('customers','entries','sales','totalSum'));
}                       
}