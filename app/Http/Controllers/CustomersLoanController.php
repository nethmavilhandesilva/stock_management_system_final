<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\CustomersLoan; // Corrected model name if it was 'CustomersLoan' in your DB

class CustomersLoanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
   public function index(Request $request)
{
    // Fetch all customers for the dropdown/search
    $customers = Customer::all();

    // Start the query to fetch loans with related customers, ordered by latest
    $query = CustomersLoan::with('customer')->latest();

    // If a filter_customer query param exists and is not empty, filter the loans by that customer
    if ($request->filled('filter_customer')) {
        $query->where('customer_id', $request->filter_customer);
    }

    // Execute the query to get the loans
    $loans = $query->get();

    // Return the view with customers and filtered loans
    return view('dashboard.customers_loans.index', compact('customers', 'loans'));
}


 public function store(Request $request)
{
    // Base validation rules
    $rules = [
        'loan_type' => 'required|string|in:old,today',
        'settling_way' => 'nullable|string|in:cash,cheque',
        'customer_id' => 'required|exists:customers,id',
        'amount' => 'required|numeric|min:0.01',
        'description' => 'required|string|max:255',
        'bill_no' => 'nullable|string|max:255',
        'cheque_no' => 'nullable|string|max:255',
        'bank' => 'nullable|string|max:255',
        'cheque_date' => 'nullable|date',
    ];

    // Conditional required fields if settling_way is given
    if ($request->input('settling_way') === 'cheque') {
        $rules['cheque_no'] = 'required|string|max:255';
        $rules['bank'] = 'required|string|max:255';
        $rules['cheque_date'] = 'required|date';
        $rules['bill_no'] = 'nullable';
    } elseif ($request->filled('settling_way')) {
        // Only require bill_no if settling_way is set and is not cheque
        $rules['bill_no'] = 'required|string|max:255';
        $rules['cheque_no'] = 'nullable';
        $rules['bank'] = 'nullable';
        $rules['cheque_date'] = 'nullable';
    }

    $validated = $request->validate($rules);

    // Fetch the customer
    $customer = Customer::find($validated['customer_id']);

    // Create new loan
    $loan = new CustomersLoan();
    $loan->loan_type = $validated['loan_type'];
    $loan->settling_way = $validated['settling_way'] ?? null; // default to null
    $loan->customer_id = $validated['customer_id'];
    $loan->amount = $validated['amount'];
    $loan->description = $validated['description'];

    // Assign customer's short name
    $loan->customer_short_name = $customer->short_name;

    // Assign fields based on settling way
    if (($validated['settling_way'] ?? null) === 'cheque') {
        $loan->cheque_no = $validated['cheque_no'];
        $loan->bank = $validated['bank'];
        $loan->cheque_date = $validated['cheque_date'];
        $loan->bill_no = null;
    } else {
        $loan->bill_no = $validated['bill_no'] ?? null;
        $loan->cheque_no = null;
        $loan->bank = null;
        $loan->cheque_date = null;
    }

    $loan->save();

    return redirect()->route('customers-loans.index');
}

  
    public function edit(CustomersLoan $loan)
    {
        // Fetch all customers for the dropdown/search in the edit form
        $customers = Customer::all();
        // Return the edit view with the specific loan and all customers
        return view('dashboard.customers_loans.edit', compact('loan', 'customers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CustomersLoan  $loan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, CustomersLoan $loan)
{
    // Base validation rules
    $rules = [
        'loan_type' => 'required|string|in:old,today',
        'settling_way' => 'nullable|string|in:cash,cheque',
        'customer_id' => 'required|exists:customers,id',
        'amount' => 'required|numeric|min:0.01',
        'bill_no' => 'nullable|string|max:255',
        'description' => 'required|string|max:255',
        'cheque_no' => 'nullable|string|max:255',
        'bank' => 'nullable|string|max:255',
        'cheque_date' => 'nullable|date',
    ];

    // Conditional validation if settling_way is provided
    if ($request->input('settling_way') === 'cheque') {
        $rules['cheque_no'] = 'required|string|max:255';
        $rules['bank'] = 'required|string|max:255';
        $rules['cheque_date'] = 'required|date';
        $rules['bill_no'] = 'nullable'; // not required if cheque
    } elseif ($request->filled('settling_way')) {
        // Require bill_no only if settling_way is set and not cheque
        $rules['bill_no'] = 'required|string|max:255';
        $rules['cheque_no'] = 'nullable';
        $rules['bank'] = 'nullable';
        $rules['cheque_date'] = 'nullable';
    }

    $validated = $request->validate($rules);

    // Force null if settling_way is not provided
    $validated['settling_way'] = $validated['settling_way'] ?? null;

    // Assign fields manually for better control
    $loan->loan_type = $validated['loan_type'];
    $loan->settling_way = $validated['settling_way'];
    $loan->customer_id = $validated['customer_id'];
    $loan->amount = $validated['amount'];
    $loan->description = $validated['description'];

    if (($validated['settling_way'] ?? null) === 'cheque') {
        $loan->cheque_no = $validated['cheque_no'];
        $loan->bank = $validated['bank'];
        $loan->cheque_date = $validated['cheque_date'];
        $loan->bill_no = null;
    } else {
        $loan->bill_no = $validated['bill_no'] ?? null;
        $loan->cheque_no = null;
        $loan->bank = null;
        $loan->cheque_date = null;
    }

    $loan->save();

    return redirect()->route('customers-loans.index')
        ->with('success', 'Loan updated successfully!');
}

    public function destroy(CustomersLoan $loan)
{
    try {
        $loan->delete();
        return redirect()->back()->with('success', 'Loan deleted successfully!');
    } catch (\Exception $e) {
        return redirect()->back()->withErrors('Failed to delete loan: ' . $e->getMessage());
    }
}
public function getTotalLoanAmount($customerId)
{
    $totalAmount = CustomersLoan::where('customer_id', $customerId)->sum('amount');
    return response()->json(['total_amount' => $totalAmount]);
}

}