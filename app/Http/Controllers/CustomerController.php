<?php

// app/Http/Controllers/CustomerController.php
namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::all();
        return view('dashboard.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('dashboard.customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'short_name' => 'nullable',
            'ID_NO' => 'nullable',
            'name' => 'nullable',
            'telephone_no' => 'nullable',
            'credit_limit' => 'nullable',
        ]);

        // Transform short_name to uppercase before saving
        $data = $request->all();
        if (!empty($data['short_name'])) {
            $data['short_name'] = strtoupper($data['short_name']);
        }

        Customer::create($data);

        return redirect()->route('customers.index')->with('success', 'Customer added successfully.');
    }

    public function edit(Customer $customer)
    {
        return view('dashboard.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'short_name' => 'required',
            'name' => 'required',
            'telephone_no' => 'nullable',
            'ID_NO' => 'nullable',
            'credit_limit' => 'required|numeric',
        ]);

        $customer->update($request->all());
        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }
}
