<?php

namespace App\Http\Controllers;

use App\Models\PoCustomer;
use App\Models\PurchaseOrder;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;

class PoCustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(PurchaseOrder $purchaseOrder)
    {
        $poCustomers = $purchaseOrder->poCustomers()->with(['customer', 'product'])->paginate(10);
        return view('po-customers.index', compact('poCustomers', 'purchaseOrder'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(PurchaseOrder $purchaseOrder)
    {
        $customers = Customer::all();
        // Ambil produk-produk yang terkait dengan PO ini melalui relasi
        $products = $purchaseOrder->products;
        return view('po-customers.create', compact('purchaseOrder', 'customers', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'product_id' => 'required|exists:products,id',
            'item_quantity' => 'required|integer|min:1',
        ]);

        $poCustomer = new PoCustomer();
        $poCustomer->purchase_order_id = $purchaseOrder->id;
        $poCustomer->customer_id = $request->customer_id;
        $poCustomer->product_id = $request->product_id;
        $poCustomer->item_quantity = $request->item_quantity;
        $poCustomer->ordered_at = now(); // Gunakan waktu lokal saat ini
        $poCustomer->save();

        return redirect()->route('po.customers.index', $purchaseOrder)->with('success', 'Pesanan pelanggan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchaseOrder $purchaseOrder, PoCustomer $poCustomer)
    {
        // Pastikan PoCustomer ini milik PO yang sedang diakses
        if ($poCustomer->purchase_order_id !== $purchaseOrder->id) {
            abort(404);
        }

        return view('po-customers.show', compact('poCustomer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PurchaseOrder $purchaseOrder, PoCustomer $poCustomer)
    {
        // Pastikan PoCustomer ini milik PO yang sedang diakses
        if ($poCustomer->purchase_order_id !== $purchaseOrder->id) {
            abort(404);
        }

        $customers = Customer::all();
        // Ambil produk-produk yang terkait dengan PO ini
        $products = $purchaseOrder->products;
        return view('po-customers.edit', compact('poCustomer', 'customers', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder, PoCustomer $poCustomer)
    {
        // Pastikan PoCustomer ini milik PO yang sedang diakses
        if ($poCustomer->purchase_order_id !== $purchaseOrder->id) {
            abort(404);
        }

        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'product_id' => 'required|exists:products,id',
            'item_quantity' => 'required|integer|min:1',
        ]);

        $poCustomer->customer_id = $request->customer_id;
        $poCustomer->product_id = $request->product_id;
        $poCustomer->item_quantity = $request->item_quantity;
        $poCustomer->save();

        return redirect()->route('po.customers.index', $purchaseOrder)->with('success', 'Pesanan pelanggan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseOrder $purchaseOrder, PoCustomer $poCustomer)
    {
        // Pastikan PoCustomer ini milik PO yang sedang diakses
        if ($poCustomer->purchase_order_id !== $purchaseOrder->id) {
            abort(404);
        }

        $poCustomer->delete();

        return redirect()->route('po.customers.index', $purchaseOrder)->with('success', 'Pesanan pelanggan berhasil dihapus.');
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
            $totalBill += $product->price * $order->item_quantity;
        }

        $totalDP = $purchaseOrder->downPayments()->where('customer_id', $customer->id)->sum('amount');

        // Periksa apakah pelanggan sudah membayar lunas
        if ($totalDP >= $totalBill) {
            // Update status semua pesanan pelanggan menjadi complete
            foreach ($customerOrders as $order) {
                $order->status = 'complete';
                $order->received_quantity = $order->item_quantity; // Terima semua item
                $order->save();

                // Kurangi stok produk
                $product = $order->product;
                $product->stock -= $order->item_quantity;
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

    /**
     * Show the form for completing transaction for a specific customer in a purchase order
     */
    public function showCompleteTransaction(PurchaseOrder $purchaseOrder, Customer $customer)
    {
        // Ambil semua pesanan pelanggan dalam PO ini dan kelompokkan berdasarkan produk
        $customerOrders = $purchaseOrder->poCustomers()
            ->where('customer_id', $customer->id)
            ->with('product')
            ->get()
            ->groupBy('product_id'); // Kelompokkan berdasarkan produk

        if ($customerOrders->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ditemukan pesanan untuk pelanggan ini dalam PO ini.');
        }

        // Hitung total tagihan berdasarkan pesanan yang dikelompokkan
        $totalBill = 0;
        $aggregatedOrders = collect();

        foreach ($customerOrders as $productId => $orders) {
            $product = $orders->first()->product;

            // Agregasi jumlah pesanan dan jumlah diterima
            $totalItemQuantity = $orders->sum('item_quantity');
            $totalReceivedQuantity = $orders->sum('received_quantity');

            // Buat objek agregasi
            $aggregatedOrder = (object)[
                'product' => $product,
                'total_item_quantity' => $totalItemQuantity,
                'total_received_quantity' => $totalReceivedQuantity ?: $totalItemQuantity,
                'individual_orders' => $orders // Simpan pesanan individu untuk referensi
            ];

            $aggregatedOrders->push($aggregatedOrder);
            $totalBill += $product->price * $totalItemQuantity;
        }

        // Ambil total DP
        $totalDP = $purchaseOrder->downPayments()
            ->where('customer_id', $customer->id)
            ->sum('amount');

        // Hitung sisa pembayaran
        $remainingPayment = $totalBill - $totalDP;

        return view('po-customers.complete-transaction', compact('purchaseOrder', 'customer', 'aggregatedOrders', 'totalBill', 'totalDP', 'remainingPayment'));
    }
}
