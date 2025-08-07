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


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Define validation rules based on the 'settling_way'
        $rules = [
            'loan_type' => 'required|string|in:old,today', // Ensure specific values
            'settling_way' => 'required|string|in:cash,cheque', // Ensure specific values
            'customer_id' => 'required|exists:customers,id',
            'amount' => 'required|numeric|min:0.01', // Amount should be positive
            'bill_no' => 'required|string|max:255', // Bill No is now always required as per your Blade JS
            'description' => 'required|string|max:255',
            // Conditional validation for cheque fields
            'cheque_no' => 'nullable|string|max:255',
            'bank' => 'nullable|string|max:255',
            'cheque_date' => 'nullable|date',
        ];

        // Apply conditional requiredness for cheque fields
        if ($request->input('settling_way') === 'cheque') {
            $rules['cheque_no'] = 'required|string|max:255';
            $rules['bank'] = 'required|string|max:255';
            $rules['cheque_date'] = 'required|date';
        }

        $validated = $request->validate($rules);

        CustomersLoan::create($validated);

        // Redirect with a success message
        return redirect()->route('customers-loans.index')->with('success', 'Loan added successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     * This method might not be directly used if editing is done via AJAX/Blade population.
     *
     * @param  \App\Models\CustomersLoan  $loan
     * @return \Illuminate\View\View
     */
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
        // Define validation rules similar to store, considering update scenario
        $rules = [
            'loan_type' => 'required|string|in:old,today',
            'settling_way' => 'required|string|in:cash,cheque',
            'customer_id' => 'required|exists:customers,id',
            'amount' => 'required|numeric|min:0.01',
            'bill_no' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            // Conditional validation for cheque fields
            'cheque_no' => 'nullable|string|max:255',
            'bank' => 'nullable|string|max:255',
            'cheque_date' => 'nullable|date',
        ];

        // Apply conditional requiredness for cheque fields
        if ($request->input('settling_way') === 'cheque') {
            $rules['cheque_no'] = 'required|string|max:255';
            $rules['bank'] = 'required|string|max:255';
            $rules['cheque_date'] = 'required|date';
        }

        $validated = $request->validate($rules);

        $loan->update($validated);

        // Redirect with a success message
        return redirect()->route('customers-loans.index')->with('success', 'Loan updated successfully!');
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

}