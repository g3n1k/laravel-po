<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::paginate(10);
        return view('master.customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master.customers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
        ]);

        $customer = Customer::create($request->all());

        // Log activity when creating a customer
        tulis_log_activity("menambah pelanggan baru {$customer->name}", Customer::class, $customer->id);

        return redirect()->route('master.customers.index')->with('success', 'Customer created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        return view('master.customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        return view('master.customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
        ]);

        $oldName = $customer->name;
        $customer->update($request->all());

        // Log activity when updating a customer
        tulis_log_activity("mengedit pelanggan {$oldName} menjadi {$customer->name}", Customer::class, $customer->id);

        return redirect()->route('master.customers.index')->with('success', 'Customer updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        $customerName = $customer->name;
        $customer->delete();

        // Log activity when deleting a customer
        tulis_log_activity("menghapus pelanggan {$customerName}", Customer::class, $customer->id);

        return redirect()->route('master.customers.index')->with('success', 'Customer deleted successfully.');
    }
}
