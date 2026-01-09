<?php

namespace App\Http\Controllers;

use App\Models\StockAdjustment;
use App\Models\PurchaseOrder;
use App\Models\Product;
use Illuminate\Http\Request;

class StockAdjustmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(PurchaseOrder $purchaseOrder)
    {
        $stockAdjustments = $purchaseOrder->products()->with('stockAdjustments')->paginate(10);
        return view('stock-adjustments.index', compact('stockAdjustments', 'purchaseOrder'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(PurchaseOrder $purchaseOrder)
    {
        $products = $purchaseOrder->products; // Ambil produk-produk yang terkait dengan PO ini
        return view('stock-adjustments.create', compact('purchaseOrder', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'adjustment' => 'required|integer', // bisa positif atau negatif
            'reason' => 'required|string|max:255',
        ]);

        $product = Product::findOrFail($request->product_id);
        $initialStock = $product->stock;
        $finalStock = $initialStock + $request->adjustment;

        $stockAdjustment = new StockAdjustment();
        $stockAdjustment->product_id = $request->product_id;
        $stockAdjustment->initial_stock = $initialStock;
        $stockAdjustment->adjustment = $request->adjustment;
        $stockAdjustment->final_stock = $finalStock;
        $stockAdjustment->reason = $request->reason;
        $stockAdjustment->save();

        // Update stok produk
        $product->stock = $finalStock;
        $product->save();

        return redirect()->route('po.stock-adjustments.index', $purchaseOrder)->with('success', 'Penyesuaian stok berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(StockAdjustment $stockAdjustment)
    {
        return view('stock-adjustments.show', compact('stockAdjustment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StockAdjustment $stockAdjustment)
    {
        $products = Product::all();
        return view('stock-adjustments.edit', compact('stockAdjustment', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StockAdjustment $stockAdjustment)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'adjustment' => 'required|integer',
            'reason' => 'required|string|max:255',
        ]);

        $product = Product::findOrFail($request->product_id);
        $oldAdjustment = $stockAdjustment->adjustment;
        $newAdjustment = $request->adjustment;
        $difference = $newAdjustment - $oldAdjustment;

        $stockAdjustment->product_id = $request->product_id;
        $stockAdjustment->initial_stock = $product->stock - $oldAdjustment; // Kembalikan ke stok sebelumnya
        $stockAdjustment->adjustment = $newAdjustment;
        $stockAdjustment->final_stock = $product->stock + $difference; // Tambahkan perbedaan ke stok saat ini
        $stockAdjustment->reason = $request->reason;
        $stockAdjustment->save();

        // Update stok produk
        $product->stock = $product->stock + $difference;
        $product->save();

        return redirect()->route('po.stock-adjustments.index', $stockAdjustment->product->purchase_order_id)->with('success', 'Penyesuaian stok berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockAdjustment $stockAdjustment)
    {
        $purchaseOrderId = $stockAdjustment->product->purchase_order_id;

        // Kembalikan stok produk ke kondisi sebelum adjustment
        $product = $stockAdjustment->product;
        $product->stock -= $stockAdjustment->adjustment;
        $product->save();

        $stockAdjustment->delete();

        return redirect()->route('po.stock-adjustments.index', $purchaseOrderId)->with('success', 'Penyesuaian stok berhasil dihapus.');
    }
}
