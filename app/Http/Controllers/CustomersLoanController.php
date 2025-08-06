<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\CustomersLoan;

class CustomersLoanController extends Controller
{
  public function index()
{
    $customers = Customer::all();
    $loans = CustomersLoan::with('customer')->latest()->get();

    return view('dashboard.customers_loans.index', compact('customers', 'loans'));
}

public function store(Request $request)
{
    $validated = $request->validate([
        'loan_type' => 'required',
        'settling_way' => 'required',
        'customer_id' => 'required|exists:customers,id',
        'amount' => 'required|numeric',
        'bill_no' => 'nullable|string',
        'description' => 'required|string',
        'cheque_no' => 'nullable|string',
        'bank' => 'nullable|string',
        'cheque_date' => 'nullable|date',
    ]);

    CustomersLoan::create($validated);

    return redirect()->route('customers-loans.index')->with('success', 'Loan added successfully!');
}
public function edit(CustomersLoan $loan)
{
    $customers = Customer::all();
    return view('customers_loans.edit', compact('loan', 'customers'));
}

public function update(Request $request, CustomersLoan $loan)
{
    $validated = $request->validate([
        'loan_type' => 'required',
        'settling_way' => 'required',
        'customer_id' => 'required',
        'amount' => 'required',
        'description' => 'required',
        // ...other fields
    ]);

    $loan->update($validated);
    return redirect()->route('customers-loans.index')->with('success', 'Loan updated successfully');
}

public function destroy(CustomersLoan $loan)
{
    $loan->delete();
    return redirect()->route('customers-loans.index')->with('success', 'Loan deleted successfully');
}

}
