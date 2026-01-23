<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $purchaseOrders = PurchaseOrder::paginate(10);
        return view('master.purchase-orders.index', compact('purchaseOrders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = \App\Models\Product::all();
        return view('master.purchase-orders.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:open,closed,completed',
            'products' => 'array',
            'products.*' => 'exists:products,id',
        ]);

        $purchaseOrder = PurchaseOrder::create($request->except('products'));

        // Jika ada produk yang dipilih, hubungkan dengan PO
        if ($request->has('products') && !empty($request->products)) {
            foreach ($request->products as $productId) {
                // Ambil quantity dari input, jika tidak ada maka default ke 0
                $quantity = isset($request->quantities[$productId]) ? $request->quantities[$productId] : 0;
                $purchaseOrder->products()->attach($productId, ['quantity' => $quantity]);
            }
        }

        // Log activity when creating a purchase order
        tulis_log_activity("membuat purcase order \"{$purchaseOrder->title}\"", PurchaseOrder::class, $purchaseOrder->id);

        return redirect()->route('master.purchase-orders.index')->with('success', 'Purchase Order created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        // G3N1K
        // http://localhost:8000/master/purchase-orders/1

        $purchaseOrder->load(['products', 'poCustomers.customer', 'downPayments']);
        return view('master.purchase-orders.show', compact('purchaseOrder'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PurchaseOrder $purchaseOrder)
    {
        $products = \App\Models\Product::all();
        $currentProducts = $purchaseOrder->products;
        return view('master.purchase-orders.edit', compact('purchaseOrder', 'products', 'currentProducts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:open,closed,completed',
            'products' => 'array',
            'products.*' => 'exists:products,id',
        ]);

        $purchaseOrder->update($request->except('products'));

        // Sinkronkan produk-produk yang terkait dengan PO
        if ($request->has('products')) {
            // Hapus semua kaitan lama
            $purchaseOrder->products()->detach();

            foreach ($request->products as $productId) {
                // Ambil quantity dari input, jika tidak ada maka default ke 0
                $quantity = isset($request->quantities[$productId]) ? $request->quantities[$productId] : 0;
                $purchaseOrder->products()->attach($productId, ['quantity' => $quantity]);
            }
        } else {
            // Jika tidak ada produk yang dipilih, hapus semua kaitan
            $purchaseOrder->products()->detach();
        }

        // Log activity when updating a purchase order
        tulis_log_activity("mengedit purcase order \"{$purchaseOrder->title}\"", PurchaseOrder::class, $purchaseOrder->id);

        return redirect()->route('master.purchase-orders.index')->with('success', 'Purchase Order updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $title = $purchaseOrder->title;
        $purchaseOrder->delete();

        // Log activity when deleting a purchase order
        tulis_log_activity("menghapus purcase order \"{$title}\"", PurchaseOrder::class, $purchaseOrder->id);

        return redirect()->route('master.purchase-orders.index')->with('success', 'Purchase Order deleted successfully.');
    }

    /**
     * Complete transaction for a specific customer in a purchase order
     */
    public function completeTransaction(PurchaseOrder $purchaseOrder, Customer $customer)
    {
        // Ambil semua pesanan pelanggan dalam PO ini
        $customerOrders = $purchaseOrder->poCustomers()->where('customer_id', $customer->id)->get();

        if ($customerOrders->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ditemukan pesanan untuk pelanggan ini dalam PO ini.');
        }

        // Hitung total tagihan dan total DP
        $totalBill = 0;
        foreach ($customerOrders as $order) {
            $product = $order->product;
            $totalBill += $product->price * $order->received_quantity;
        }

        $totalDP = $purchaseOrder->downPayments()->where('customer_id', $customer->id)->sum('amount');

        // Periksa apakah pelanggan sudah membayar lunas
        if ($totalDP >= $totalBill) {
            // Update status semua pesanan pelanggan menjadi complete
            foreach ($customerOrders as $order) {
                $order->status = 'complete';

                // kenyataannya tidak semua item akan diterima, tergantung dari stock
                // $order->received_quantity = $order->item_quantity; // Terima semua item

                $order->save();

                // Kurangi stok produk
                $product = $order->product;
                $product->stock -= $order->received_quantity;
                $product->save();
            }

            // Update status PO jika semua pelanggan telah melunasi pembayaran
            $remainingCustomers = $purchaseOrder->poCustomers()
                ->whereHas('customer', function($query) use ($purchaseOrder) {
                    $query->whereHas('poCustomers', function($subQuery) use ($purchaseOrder) {
                        $subQuery->where('purchase_order_id', $purchaseOrder->id)
                                 ->where('status', '!=', 'complete');
                    });
                })
                ->count();

            if ($remainingCustomers === 0) {
                $purchaseOrder->status = 'completed';
                $purchaseOrder->save();
            }

            return redirect()->back()->with('success', 'Transaksi pelanggan ' . $customer->name . ' telah diselesaikan.');
        } else {
            return redirect()->back()->with('error', 'Pembayaran pelanggan ' . $customer->name . ' belum lunas. Mohon lengkapi pembayaran terlebih dahulu.');
        }
    }
}
