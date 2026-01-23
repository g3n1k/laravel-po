@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Detail Purchase Order</h1>
            
            <div class="card">
                <div class="card-header">
                    <h5>Informasi Purchase Order</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3"><strong>Judul:</strong></div>
                        <div class="col-md-9">{{ $purchaseOrder->title }}</div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3"><strong>Deskripsi:</strong></div>
                        <div class="col-md-9">{{ $purchaseOrder->description ?? '-' }}</div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3"><strong>Tanggal Mulai:</strong></div>
                        <div class="col-md-9">{{ $purchaseOrder->start_date->format('d M Y') }}</div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3"><strong>Tanggal Akhir:</strong></div>
                        <div class="col-md-9">{{ $purchaseOrder->end_date->format('d M Y') }}</div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3"><strong>Status:</strong></div>
                        <div class="col-md-9">
                            @if($purchaseOrder->status === 'open')
                                <span class="badge bg-success">Open</span>
                            @elseif($purchaseOrder->status === 'closed')
                                <span class="badge bg-warning">Closed</span>
                            @else
                                <span class="badge bg-secondary">Completed</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3"><strong>Dibuat:</strong></div>
                        <div class="col-md-9">{{ $purchaseOrder->created_at->format('d M Y H:i') }}</div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3"><strong>Diupdate:</strong></div>
                        <div class="col-md-9">{{ $purchaseOrder->updated_at->format('d M Y H:i') }}</div>
                    </div>

                    <!-- Summary Cards -->
                    <div class="row mt-4">
                        <div class="col-md-3 mb-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h5>Down Payment</h5>
                                    <h3>Rp {{ number_format($purchaseOrder->downPayments->sum('amount'), 0, ',', '.') }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h5>Pelanggan</h5>
                                    <h3>{{ $purchaseOrder->poCustomers->pluck('customer_id')->unique()->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h5>Produk</h5>
                                    <h3>{{ $purchaseOrder->products->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-warning text-dark">
                                <div class="card-body text-center">
                                    <h5>Pesanan Item</h5>
                                    <h3>{{ $purchaseOrder->poCustomers->sum('item_quantity') }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5>Produk dalam PO ini</h5>
                            @if($purchaseOrder->products->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Nama Produk</th>
                                                <th>Harga</th>
                                                <th>Stok</th>
                                                <th>Dipesan</th>
                                                <th>Selisih Stok</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($purchaseOrder->products as $product)
                                            <tr>
                                                <td>{{ $product->name }}</td>
                                                <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                                <td>{{ $product->stock }}</td>
                                                <td>{{ $purchaseOrder->poCustomers->where('product_id', $product->id)->sum('item_quantity') }}</td>
                                                <td>
                                                    @php
                                                        $totalOrdered = $purchaseOrder->poCustomers
                                                            ->where('product_id', $product->id)
                                                            ->where('payment_status', '==', 'unpaid')
                                                            ->sum('item_quantity');
                                                        $diff = $product->stock - $totalOrdered;
                                                    @endphp
                                                    {{ $diff }}
                                                    @if($diff < 0)
                                                        <span class="badge bg-danger">Kurang</span>
                                                    @elseif($diff == 0)
                                                        <span class="badge bg-warning">Pas</span>
                                                    @else
                                                        <span class="badge bg-success">stok masih ada</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('po.po.product.distribute-stock', ['purchaseOrder' => $purchaseOrder, 'product' => $product]) }}" class="btn btn-success btn-sm">
                                                            <i class="fas fa-boxes"></i> Distribusi Stock
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted">Tidak ada produk dalam PO ini</p>
                            @endif
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5>Ringkasan Pelanggan dalam PO ini</h5>
                            @if($purchaseOrder->poCustomers->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Nama</th>
                                                <th>Dipesan</th>
                                                <th>Diterima</th>
                                                <th>DP</th>
                                                <th>Bayar</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                // Pisahkan data menjadi transaksi yang sudah selesai dan request baru
                                                $completedTransactions = [];
                                                $newRequests = [];

                                                foreach($purchaseOrder->poCustomers as $poCustomer) {
                                                    $customerId = $poCustomer->customer_id;
                                                    $transactionSummaryId = $poCustomer->transaction_summary_id;

                                                    if ($transactionSummaryId !== null) {
                                                        // Ini adalah transaksi yang sudah selesai
                                                        $key = $customerId . '_' . $transactionSummaryId;

                                                        if (!isset($completedTransactions[$key])) {
                                                            $completedTransactions[$key] = [
                                                                'customer' => $poCustomer->customer,
                                                                'transaction_summary_id' => $transactionSummaryId,
                                                                'total_items' => 0,
                                                                'total_received' => 0,
                                                                'total_bill' => 0,
                                                                'total_dp' => 0,
                                                                'additional_payment' => 0
                                                            ];
                                                        }

                                                        $completedTransactions[$key]['total_items'] += $poCustomer->item_quantity;
                                                        $completedTransactions[$key]['total_received'] += $poCustomer->received_quantity;

                                                        // Hitung total tagihan berdasarkan produk yang diterima
                                                        $product = $poCustomer->product;
                                                        $completedTransactions[$key]['total_bill'] += $product->price * $poCustomer->received_quantity;
                                                    } else {
                                                        // Ini adalah request baru
                                                        $key = $poCustomer->customer_id . '_new';

                                                        if (!isset($newRequests[$key])) {
                                                            $newRequests[$key] = [
                                                                'customer' => $poCustomer->customer,
                                                                'total_items' => 0,
                                                                'total_received' => 0,
                                                                'total_bill' => 0,
                                                                'total_dp' => 0,
                                                                'additional_payment' => 0
                                                            ];
                                                        }

                                                        $newRequests[$key]['total_items'] += $poCustomer->item_quantity;
                                                        $newRequests[$key]['total_received'] += $poCustomer->received_quantity;

                                                        // Hitung total tagihan berdasarkan produk yang diterima
                                                        $product = $poCustomer->product;
                                                        $newRequests[$key]['total_bill'] += $product->price * $poCustomer->received_quantity;
                                                    }
                                                }

                                                // Inisialisasi total DP dan additional payment untuk semua entri
                                                foreach($completedTransactions as $key => $summary) {
                                                    $completedTransactions[$key]['total_dp'] = 0;
                                                    $completedTransactions[$key]['additional_payment'] = 0;
                                                }
                                                foreach($newRequests as $key => $summary) {
                                                    $newRequests[$key]['total_dp'] = 0;
                                                    $newRequests[$key]['additional_payment'] = 0;
                                                }

                                                // Hitung total DP dan additional payment per pelanggan dan per transaction_summary_id
                                                foreach($purchaseOrder->downPayments as $dp) {
                                                    $customerId = $dp->customer_id;
                                                    if ($dp->transaction_summary_id !== null) {
                                                        // Ini adalah DP untuk transaksi yang sudah selesai
                                                        $key = $customerId . '_' . $dp->transaction_summary_id;
                                                        if (isset($completedTransactions[$key])) {
                                                            $completedTransactions[$key]['total_dp'] += $dp->amount;
                                                        }
                                                    } else {
                                                        // Ini adalah DP untuk request baru
                                                        $key = $customerId . '_new';
                                                        if (isset($newRequests[$key])) {
                                                            $newRequests[$key]['total_dp'] += $dp->amount;
                                                        }
                                                    }
                                                }
                                            @endphp

                                            <!-- Tampilkan transaksi yang sudah selesai terlebih dahulu -->
                                            @foreach($completedTransactions as $summary)
                                            @php
                                                $outstandingAmount = max(0, $summary['total_bill'] - $summary['total_dp']); // Sisa pembayaran tidak boleh negatif
                                            @endphp
                                            <tr>
                                                <td>{{ $summary['customer']->name }} <small class="text-muted">(Transaksi Selesai #{{ $summary['transaction_summary_id'] }})</small></td>
                                                <td>{{ $summary['total_items'] }}</td>
                                                <td>{{ $summary['total_received'] }}</td>
                                                <td>Rp {{ number_format($summary['total_dp'], 0, ',', '.') }}</td>
                                                <td>Rp {{ number_format($outstandingAmount, 0, ',', '.') }}</td>
                                                <td>Rp {{ number_format($summary['total_bill'], 0, ',', '.') }}</td>
                                                <td>
                                                    @php
                                                        // Cek apakah semua pesanan dalam transaksi ini sudah paid
                                                        $allPaid = $purchaseOrder->poCustomers
                                                            ->where('customer_id', $summary['customer']->id)
                                                            ->where('transaction_summary_id', $summary['transaction_summary_id'])
                                                            ->every(function ($order) {
                                                                return $order->payment_status === 'paid';
                                                            });
                                                    @endphp
                                                    @if($allPaid)
                                                        <span class="badge bg-success">Paid</span>
                                                    @else
                                                        <span class="badge bg-warning">Unpaid</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('po.customers.show-transaction-detail', [$purchaseOrder, $summary['customer']]) }}?transaction_summaries_id={{ $summary['transaction_summary_id'] }}" class="btn btn-info btn-sm">
                                                            <i class="fas fa-eye"></i> Lihat Detail
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach

                                            <!-- Tampilkan request baru di bawahnya -->
                                            @foreach($newRequests as $summary)
                                            @php
                                                $outstandingAmount = $summary['total_bill'] - $summary['total_dp'];
                                            @endphp
                                            <tr>
                                                <td>{{ $summary['customer']->name }} <small class="text-muted">(Request Baru)</small></td>
                                                <td>{{ $summary['total_items'] }}</td>
                                                <td>{{ $summary['total_received'] }}</td>
                                                <td>Rp {{ number_format($summary['total_dp'], 0, ',', '.') }}</td>
                                                <td>Rp {{ number_format($summary['total_bill'], 0, ',', '.') }}</td>
                                                <td>Rp {{ number_format($outstandingAmount, 0, ',', '.') }}</td>
                                                <td>
                                                    @php
                                                        // Cek apakah semua pesanan dalam request baru ini sudah paid
                                                        $allPaid = $purchaseOrder->poCustomers
                                                            ->where('customer_id', $summary['customer']->id)
                                                            ->whereNull('transaction_summary_id')
                                                            ->every(function ($order) {
                                                                return $order->payment_status === 'paid';
                                                            });
                                                    @endphp
                                                    @if($allPaid)
                                                        <span class="badge bg-success">Paid</span>
                                                    @else
                                                        <span class="badge bg-warning">Unpaid</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        @if($allPaid)
                                                            <a href="{{ route('po.customers.show-transaction-detail', [$purchaseOrder, $summary['customer']]) }}" class="btn btn-info btn-sm">
                                                                <i class="fas fa-eye"></i> Lihat Detail
                                                            </a>
                                                        @else
                                                            <a href="{{ route('po.customers.show-complete-transaction', [$purchaseOrder, $summary['customer']]) }}" class="btn btn-primary btn-sm">
                                                                <i class="fas fa-cash-register"></i> Proses
                                                            </a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted">Tidak ada pelanggan dalam PO ini</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <div>
                        <a href="{{ route('po.index') }}" class="btn btn-info">Kembali</a>
                        <a href="{{ route('master.purchase-orders.edit', $purchaseOrder) }}" class="btn btn-warning">Edit</a>
                    </div>
                    <div>
                        <a href="{{ route('po.customers.index', $purchaseOrder) }}" class="btn btn-primary">
                            <i class="fas fa-users"></i> Pesanan
                        </a>
                        <a href="{{ route('po.down-payments.index', $purchaseOrder) }}" class="btn btn-success">
                            <i class="fas fa-money-bill-wave"></i> DP
                        </a>
                        <a href="{{ route('po.stock-adjustments.index', $purchaseOrder) }}" class="btn btn-secondary">
                            <i class="fas fa-boxes"></i> Stok
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection