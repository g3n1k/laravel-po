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

        // Cek apakah pesanan ini sudah terkait dengan transaksi yang selesai
        $isLinkedToCompletedTransaction = $poCustomer->payment_status === 'paid' || $poCustomer->transaction_summary_id !== null;

        return view('po-customers.show', compact('poCustomer', 'isLinkedToCompletedTransaction'));
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

        // Cek apakah pesanan ini sudah terkait dengan transaksi yang selesai
        if ($poCustomer->payment_status === 'paid' || $poCustomer->transaction_summary_id !== null) {
            return redirect()->back()->with('error', 'Pesanan ini sudah terkait dengan transaksi yang selesai dan tidak bisa diedit.');
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

        // Cek apakah pesanan ini sudah terkait dengan transaksi yang selesai
        if ($poCustomer->payment_status === 'paid' || $poCustomer->transaction_summary_id !== null) {
            return redirect()->back()->with('error', 'Pesanan ini sudah terkait dengan transaksi yang selesai dan tidak bisa diupdate.');
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

        // Cek apakah pesanan ini sudah terkait dengan transaksi yang selesai
        if ($poCustomer->payment_status === 'paid' || $poCustomer->transaction_summary_id !== null) {
            return redirect()->back()->with('error', 'Pesanan ini sudah terkait dengan transaksi yang selesai dan tidak bisa dihapus.');
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
    public function completeTransaction(Request $request, PurchaseOrder $purchaseOrder, Customer $customer)
    {
        // Ambil semua pesanan pelanggan dalam PO ini
        $customerOrders = $purchaseOrder->poCustomers()->where('customer_id', $customer->id)->where('purchase_order_id', $purchaseOrder->id)->get();

        if ($customerOrders->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ditemukan pesanan untuk pelanggan ini dalam PO ini.');
        }

        // Cek apakah semua pesanan pelanggan sudah dalam status complete
        $allComplete = $customerOrders->every(function ($order) {
            return $order->payment_status === 'paid';
        });

        if ($allComplete) {
            return redirect()->back()->with('error', 'Transaksi pelanggan ' . $customer->name . ' sudah diselesaikan sebelumnya.');
        }

        // Update status berdasarkan received quantity yang sudah ada
        foreach ($customerOrders as $order) {
            // Simpan status sebelumnya untuk membandingkan perubahan
            $previousPaymentStatus = $order->payment_status;

            // Update status berdasarkan received quantity yang sudah ada
            if ($order->received_quantity == 0) {
                $order->status = 'out_of_stock';
            } elseif ($order->received_quantity < $order->item_quantity) {
                $order->status = 'not_complete';
            } elseif ($order->received_quantity >= $order->item_quantity) {
                $order->status = 'complete';
            }

            // Update payment status dan kolom terkait jika status berubah dari unpaid ke paid
            if ($previousPaymentStatus === 'unpaid') {
                $order->payment_status = 'paid';
                $order->payment_product_price = $order->product->price; // Harga produk saat pembayaran
                $order->payment_amount = $order->product->price * $order->received_quantity; // Jumlah pembayaran
            }

            $order->save();
        }

        // Hitung total tagihan berdasarkan received quantity
        $totalBill = 0;
        foreach ($customerOrders as $order) {
            $product = $order->product;
            $totalBill += $product->price * $order->received_quantity;
        }

        $totalDP = $purchaseOrder->downPayments()->where('customer_id', $customer->id)->where('purchase_order_id', $purchaseOrder->id)->sum('amount');
        $additionalPayment = $request->additional_payment ?? 0;
        $totalPaid = $totalDP + $additionalPayment;

        // Update status semua pesanan pelanggan menjadi complete jika pembayaran cukup
        foreach ($customerOrders as $order) {
            // Update status berdasarkan received quantity (sudah diupdate di atas)
            // Jika pembayaran cukup, maka status tetap atau diupdate ke complete
            if ($totalPaid >= $totalBill) {
                // Update payment status ke paid karena transaksi selesai
                $order->payment_status = 'paid';
                $order->payment_product_price = $order->product->price; // Harga produk saat pembayaran
                $order->payment_amount = $order->product->price * $order->received_quantity; // Jumlah pembayaran
            }
            $order->save();

            // Kurangi stok produk jika status complete
            if ($order->payment_status === 'paid') {
                // Baca ulang jumlah stock yang ada di table product
                $jumlah_stock_real = \App\Models\Product::find($order->product_id)->stock;

                \Log::info('Processing stock reduction', [
                    'customer_name' => $customer->name,
                    'product_name' => $order->product->name,
                    'received_quantity' => $order->received_quantity,
                    'current_stock_before' => $jumlah_stock_real,
                    'order_id' => $order->id,
                    'purchase_order_id' => $purchaseOrder->id
                ]);

                $product = $order->product;
                $product->stock = $jumlah_stock_real - $order->received_quantity;
                $product->save();

                \Log::info('Stock after reduction', [
                    'product_name' => $product->name,
                    'new_stock' => $product->stock,
                    'reduced_by' => $order->received_quantity
                ]);

                // Log activity when completing customer's order
                tulis_log_activity("menyelesaikan pesanan {$customer->name} untuk {$order->item_quantity} {$product->name}", Customer::class, $customer->id);
            }
        }

        // Buat entri ringkasan transaksi
        $transactionSummary = \App\Models\TransactionSummary::updateOrCreate(
            [
                'purchase_order_id' => $purchaseOrder->id,
                'customer_id' => $customer->id,
            ],
            [
                'total_bill' => $totalBill,
                'total_dp' => $totalDP,
                'additional_payment' => $additionalPayment,
                'remaining_payment' => max(0, $totalBill - $totalPaid),
                'status' => $totalPaid >= $totalBill ? 'completed' : 'partial',
                'notes' => $request->notes ?? '',
                'completed_at' => now(),
            ]
        );

        // Update semua pesanan pelanggan dengan transaction_summary_id
        foreach ($customerOrders as $order) {
            $order->transaction_summary_id = $transactionSummary->id;
            $order->save();
        }

        // Update down payments yang terkait dengan customer ini dan PO ini agar terkait dengan transaction summary
        $downPayments = $purchaseOrder->downPayments()
            ->where('customer_id', $customer->id)
            ->whereNull('transaction_summary_id')
            ->get();

        foreach ($downPayments as $downPayment) {
            $downPayment->transaction_summary_id = $transactionSummary->id;
            $downPayment->save();
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

            // Log activity when completing the purchase order
            tulis_log_activity("menyelesaikan purcase order \"{$purchaseOrder->title}\"", PurchaseOrder::class, $purchaseOrder->id);
        }

        return redirect()->route('po.customers.show-transaction-detail', [$purchaseOrder, $customer])->with('success', 'Transaksi pelanggan ' . $customer->name . ' telah diselesaikan.');
    }

    /**
     * Show the form for completing transaction for a specific customer in a purchase order
     */
    public function showCompleteTransaction(PurchaseOrder $purchaseOrder, Customer $customer)
    {
        // Ambil pesanan pelanggan dalam PO ini yang BELUM SELESAI (tanpa transaction_summary_id) dan kelompokkan berdasarkan produk
        $customerOrders = $purchaseOrder->poCustomers()
            ->where('customer_id', $customer->id)
            ->whereNull('transaction_summary_id') // Hanya ambil pesanan yang belum diselesaikan
            ->with('product')
            ->get()
            ->groupBy('product_id'); // Kelompokkan berdasarkan produk

        if ($customerOrders->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ditemukan pesanan yang belum selesai untuk pelanggan ini dalam PO ini.');
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

        // Ambil total DP untuk pesanan yang belum selesai
        $totalDP = $purchaseOrder->downPayments()
            ->where('customer_id', $customer->id)
            ->whereNull('transaction_summary_id') // Hanya DP yang belum terkait dengan transaksi selesai
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
     * Show the transaction details for a specific customer in a purchase order
     */
    public function showTransactionDetail(PurchaseOrder $purchaseOrder, Customer $customer)
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

        // Ambil pesanan pelanggan dalam PO ini yang BELUM SELESAI (tanpa transaction_summary_id) dan kelompokkan berdasarkan produk
        $uncompletedOrders = $purchaseOrder->poCustomers()
            ->where('customer_id', $customer->id)
            ->whereNull('transaction_summary_id')
            ->with('product')
            ->get()
            ->groupBy('product_id'); // Kelompokkan berdasarkan produk

        // Ambil pesanan pelanggan dalam PO ini yang SUDAH SELESAI (dengan transaction_summary_id) dan kelompokkan berdasarkan produk
        $completedOrders = $purchaseOrder->poCustomers()
            ->where('customer_id', $customer->id)
            ->whereNotNull('transaction_summary_id')
            ->with('product', 'transactionSummary')
            ->get()
            ->groupBy('product_id'); // Kelompokkan berdasarkan produk

        // Hitung total tagihan untuk pesanan yang belum selesai
        $totalUncompletedBill = 0;
        $aggregatedUncompletedOrders = collect();

        foreach ($uncompletedOrders as $productId => $orders) {
            $product = $orders->first()->product;

            // Agregasi jumlah pesanan dan jumlah diterima
            $totalItemQuantity = $orders->sum('item_quantity');
            $totalReceivedQuantity = $orders->sum('received_quantity');

            // Buat objek agregasi
            $aggregatedOrder = (object)[
                'product' => $product,
                'total_item_quantity' => $totalItemQuantity,
                'total_received_quantity' => $totalReceivedQuantity ?: 0,
                'individual_orders' => $orders, // Simpan pesanan individu untuk referensi
                'is_completed' => false
            ];

            $aggregatedUncompletedOrders->push($aggregatedOrder);
            $totalUncompletedBill += $product->price * $totalReceivedQuantity;
        }

        // Hitung total tagihan untuk pesanan yang sudah selesai
        $totalCompletedBill = 0;
        $aggregatedCompletedOrders = collect();

        foreach ($completedOrders as $productId => $orders) {
            $product = $orders->first()->product;

            // Agregasi jumlah pesanan dan jumlah diterima
            $totalItemQuantity = $orders->sum('item_quantity');
            $totalReceivedQuantity = $orders->sum('received_quantity');

            // Buat objek agregasi
            $aggregatedOrder = (object)[
                'product' => $product,
                'total_item_quantity' => $totalItemQuantity,
                'total_received_quantity' => $totalReceivedQuantity ?: 0,
                'individual_orders' => $orders, // Simpan pesanan individu untuk referensi
                'is_completed' => true,
                'transaction_summary' => $orders->first()->transactionSummary
            ];

            $aggregatedCompletedOrders->push($aggregatedOrder);
            $totalCompletedBill += $product->price * $totalReceivedQuantity;
        }

        // Ambil total DP untuk pesanan yang belum selesai
        $totalUncompletedDP = $purchaseOrder->downPayments()
            ->where('customer_id', $customer->id)
            ->whereNull('transaction_summary_id') // Hanya DP yang belum terkait dengan transaksi selesai
            ->sum('amount');

        // Ambil total DP untuk pesanan yang sudah selesai
        $totalCompletedDP = $purchaseOrder->downPayments()
            ->where('customer_id', $customer->id)
            ->whereNotNull('transaction_summary_id') // Hanya DP yang terkait dengan transaksi selesai
            ->sum('amount');

        // Hitung sisa pembayaran untuk pesanan yang belum selesai
        $remainingUncompletedPayment = $totalUncompletedBill - $totalUncompletedDP;

        return view('po-customers.transaction-detail', compact(
            'purchaseOrder',
            'customer',
            'aggregatedUncompletedOrders',
            'aggregatedCompletedOrders',
            'totalUncompletedBill',
            'totalCompletedBill',
            'totalUncompletedDP',
            'totalCompletedDP',
            'remainingUncompletedPayment'
        ));
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

            if ($poCustomer &&
                $poCustomer->transaction_summary_id === null &&
                $poCustomer->payment_status !== 'paid') {

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
        // Ambil pesanan pelanggan untuk produk ini dalam PO ini yang SUDAH DIBAYAR dan urutkan berdasarkan tanggal pemesanan
        $paidPoCustomers = $purchaseOrder->poCustomers()
            ->where('product_id', $product->id)
            ->where(function($query) {
                $query->where('payment_status', 'paid')       // Hanya pesanan yang sudah dibayar
                      ->orWhereNotNull('transaction_summary_id');  // Atau memiliki transaction_summary_id
            })
            ->with(['customer', 'product'])
            ->orderBy('ordered_at')
            ->get();

        // Ambil pesanan pelanggan untuk produk ini dalam PO ini yang BELUM DIBAYAR dan urutkan berdasarkan tanggal pemesanan
        $unpaidPoCustomers = $purchaseOrder->poCustomers()
            ->where('product_id', $product->id)
            ->where('payment_status', '!=', 'paid')  // Hanya pesanan yang belum dibayar
            ->whereNull('transaction_summary_id')   // Dan belum memiliki transaction_summary_id
            ->with(['customer', 'product'])
            ->orderBy('ordered_at')
            ->get();

        return view('po-customers.distribute-product-stock', compact('purchaseOrder', 'product', 'paidPoCustomers', 'unpaidPoCustomers'));
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
            // Juga pastikan pesanan belum diselesaikan (tidak memiliki transaction_summary_id dan payment_status != 'paid')
            if ($poCustomer &&
                $poCustomer->product_id == $product->id &&
                $poCustomer->purchase_order_id == $purchaseOrder->id &&
                $poCustomer->transaction_summary_id === null &&
                $poCustomer->payment_status !== 'paid') {

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

