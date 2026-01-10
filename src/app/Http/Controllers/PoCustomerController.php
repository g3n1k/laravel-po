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
        $customer = Customer::find($request->customer_id);
        $product = Product::find($request->product_id);

        $poCustomer->customer_id = $request->customer_id;
        $poCustomer->product_id = $request->product_id;
        $poCustomer->item_quantity = $request->item_quantity;
        $poCustomer->ordered_at = now(); // Gunakan waktu lokal saat ini
        $poCustomer->save();

        // Log activity when customer places an order
        tulis_log_activity("{$customer->name} memesan {$poCustomer->item_quantity} {$product->name}", Customer::class, $customer->id);

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

        $customer = $poCustomer->customer;
        $product = $poCustomer->product;
        $poCustomer->customer_id = $request->customer_id;
        $poCustomer->product_id = $request->product_id;
        $poCustomer->item_quantity = $request->item_quantity;
        $poCustomer->save();

        // Log activity when updating customer's order
        tulis_log_activity("mengedit pesanan {$customer->name} untuk {$poCustomer->item_quantity} {$product->name}", Customer::class, $customer->id);

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

        $customer = $poCustomer->customer;
        $product = $poCustomer->product;
        $poCustomer->delete();

        // Log activity when deleting customer's order
        tulis_log_activity("menghapus pesanan {$customer->name} untuk {$product->name}", Customer::class, $customer->id);

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
                // 'total_received_quantity' => $totalReceivedQuantity ?: $totalItemQuantity,
                'total_received_quantity' => $totalReceivedQuantity ?: 0,
                'individual_orders' => $orders // Simpan pesanan individu untuk referensi
            ];

            $aggregatedOrders->push($aggregatedOrder);
            $totalBill += $product->price * $totalReceivedQuantity;
        }

        // Ambil total DP
        $totalDP = $purchaseOrder->downPayments()
            ->where('customer_id', $customer->id)
            ->sum('amount');

        // Hitung sisa pembayaran
        $remainingPayment = $totalBill - $totalDP;

        return view('po-customers.complete-transaction', compact('purchaseOrder', 'customer', 'aggregatedOrders', 'totalBill', 'totalDP', 'remainingPayment'));
    }

    /**
     * Show the form for distributing stock for a purchase order
     */
    public function distributeStock(PurchaseOrder $purchaseOrder)
    {
        // Ambil semua pesanan pelanggan dalam PO ini dan kelompokkan berdasarkan customer
        $poCustomersGrouped = $purchaseOrder->poCustomers()
            ->with(['customer', 'product'])
            ->orderBy('customer_id')
            ->orderBy('ordered_at')
            ->get()
            ->groupBy('customer_id');

        return view('po-customers.distribute-stock', compact('purchaseOrder', 'poCustomersGrouped'));
    }

    /**
     * Process the stock distribution for a purchase order
     */
    public function processDistributeStock(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate([
            'received_quantities' => 'required|array',
            'received_quantities.*' => 'required|integer|min:0',
        ]);

        $receivedQuantities = $request->received_quantities;

        foreach ($receivedQuantities as $poCustomerId => $receivedQuantity) {
            $poCustomer = $purchaseOrder->poCustomers()->find($poCustomerId);

            if ($poCustomer) {
                // Update received quantity
                $poCustomer->received_quantity = $receivedQuantity;

                // Update status berdasarkan received quantity
                if ($receivedQuantity == 0) {
                    $poCustomer->status = 'out_of_stock';
                } elseif ($receivedQuantity < $poCustomer->item_quantity) {
                    $poCustomer->status = 'not_complete';
                } elseif ($receivedQuantity >= $poCustomer->item_quantity) {
                    $poCustomer->status = 'complete';
                }

                $poCustomer->save();
            }
        }

        return redirect()->route('po.customers.index', $purchaseOrder)
            ->with('success', 'Distribusi stok berhasil disimpan.');
    }

    /**
     * Show the form for distributing stock for a specific product in a purchase order
     */
    public function distributeProductStock(PurchaseOrder $purchaseOrder, Product $product)
    {
        // Ambil semua pesanan pelanggan untuk produk ini dalam PO ini dan urutkan berdasarkan tanggal pemesanan
        $poCustomers = $purchaseOrder->poCustomers()
            ->where('product_id', $product->id)
            ->with(['customer', 'product'])
            ->orderBy('ordered_at')
            ->get();

        return view('po-customers.distribute-product-stock', compact('purchaseOrder', 'product', 'poCustomers'));
    }

    /**
     * Process the stock distribution for a specific product in a purchase order
     */
    public function processDistributeProductStock(Request $request, PurchaseOrder $purchaseOrder, Product $product)
    {
        $request->validate([
            'received_quantities' => 'required|array',
            'received_quantities.*' => 'required|integer|min:0',
        ]);

        $receivedQuantities = $request->received_quantities;

        foreach ($receivedQuantities as $poCustomerId => $receivedQuantity) {
            $poCustomer = $purchaseOrder->poCustomers()->find($poCustomerId);

            // Pastikan poCustomer ini milik produk yang benar dan PO yang benar
            if ($poCustomer && $poCustomer->product_id == $product->id && $poCustomer->purchase_order_id == $purchaseOrder->id) {
                // Update received quantity
                $poCustomer->received_quantity = $receivedQuantity;

                // Update status berdasarkan received quantity
                if ($receivedQuantity == 0) {
                    $poCustomer->status = 'out_of_stock';
                } elseif ($receivedQuantity < $poCustomer->item_quantity) {
                    $poCustomer->status = 'not_complete';
                } elseif ($receivedQuantity >= $poCustomer->item_quantity) {
                    $poCustomer->status = 'complete';
                }

                $poCustomer->save();
            }
        }

        return redirect()->route('master.purchase-orders.show', $purchaseOrder)
            ->with('success', 'Distribusi stok produk berhasil disimpan.');
    }
}

