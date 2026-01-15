<?php

namespace App\Http\Controllers;

use App\Models\DownPayment;
use App\Models\PurchaseOrder;
use App\Models\Customer;
use Illuminate\Http\Request;

class DownPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(PurchaseOrder $purchaseOrder)
    {
        $downPayments = $purchaseOrder->downPayments()->with('customer')->paginate(10);
        return view('down-payments.index', compact('downPayments', 'purchaseOrder'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(PurchaseOrder $purchaseOrder)
    {
        // Ambil pelanggan-pelanggan yang sudah melakukan pesanan dalam PO ini
        $customers = $purchaseOrder->poCustomers()
            ->with('customer')
            ->get()
            ->pluck('customer')
            ->unique('id')
            ->values();
        return view('down-payments.create', compact('purchaseOrder', 'customers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $downPayment = new DownPayment();
        $downPayment->purchase_order_id = $purchaseOrder->id;
        $downPayment->customer_id = $request->customer_id;
        $downPayment->amount = $request->amount;
        $downPayment->notes = $request->notes;
        $downPayment->save();

        return redirect()->route('po.down-payments.index', $purchaseOrder)->with('success', 'Down payment berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchaseOrder $purchaseOrder, DownPayment $downPayment)
    {
        // Pastikan DownPayment ini milik PO yang sedang diakses
        if ($downPayment->purchase_order_id !== $purchaseOrder->id) {
            abort(404);
        }

        // Cek apakah DP ini sudah terkait dengan transaksi yang selesai
        $isLinkedToCompletedTransaction = $downPayment->transaction_summary_id !== null;

        return view('down-payments.show', compact('downPayment', 'isLinkedToCompletedTransaction'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PurchaseOrder $purchaseOrder, DownPayment $downPayment)
    {
        // Pastikan DownPayment ini milik PO yang sedang diakses
        if ($downPayment->purchase_order_id !== $purchaseOrder->id) {
            abort(404);
        }

        // Cek apakah DP ini sudah terkait dengan transaksi yang selesai
        if ($downPayment->transaction_summary_id !== null) {
            return redirect()->back()->with('error', 'Down payment ini sudah terkait dengan transaksi yang selesai dan tidak bisa diedit.');
        }

        $customers = Customer::all();
        return view('down-payments.edit', compact('downPayment', 'customers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder, DownPayment $downPayment)
    {
        // Pastikan DownPayment ini milik PO yang sedang diakses
        if ($downPayment->purchase_order_id !== $purchaseOrder->id) {
            abort(404);
        }

        // Cek apakah DP ini sudah terkait dengan transaksi yang selesai
        if ($downPayment->transaction_summary_id !== null) {
            return redirect()->back()->with('error', 'Down payment ini sudah terkait dengan transaksi yang selesai dan tidak bisa diupdate.');
        }

        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $downPayment->customer_id = $request->customer_id;
        $downPayment->amount = $request->amount;
        $downPayment->notes = $request->notes;
        $downPayment->save();

        return redirect()->route('po.down-payments.index', $purchaseOrder)->with('success', 'Down payment berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseOrder $purchaseOrder, DownPayment $downPayment)
    {
        // Pastikan DownPayment ini milik PO yang sedang diakses
        if ($downPayment->purchase_order_id !== $purchaseOrder->id) {
            abort(404);
        }

        // Cek apakah DP ini sudah terkait dengan transaksi yang selesai
        if ($downPayment->transaction_summary_id !== null) {
            return redirect()->back()->with('error', 'Down payment ini sudah terkait dengan transaksi yang selesai dan tidak bisa dihapus.');
        }

        $downPayment->delete();

        return redirect()->route('po.down-payments.index', $purchaseOrder)->with('success', 'Down payment berhasil dihapus.');
    }
}
