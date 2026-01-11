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
                                    <h5>Total Item</h5>
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
                                                <th>Total Item Dipesan</th>
                                                <th>Selisih dengan Stok</th>
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
                                                        $totalOrdered = $purchaseOrder->poCustomers->where('product_id', $product->id)->sum('item_quantity');
                                                        $diff = $product->stock - $totalOrdered;
                                                    @endphp
                                                    {{ $diff }}
                                                    @if($diff < 0)
                                                        <span class="badge bg-danger">Kurang</span>
                                                    @elseif($diff == 0)
                                                        <span class="badge bg-warning">Pas</span>
                                                    @else
                                                        <span class="badge bg-success">Cukup</span>
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
                                                <th>Nama Pelanggan</th>
                                                <th>Jumlah Item Dipesan</th>
                                                <th>Jumlah Item Diterima</th>
                                                <th>Total DP</th>
                                                <th>Tagihan yang Harus Dibayar</th>
                                                <th>Kurang Bayar</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $customerSummary = [];
                                                // Kumpulkan data pesanan pelanggan
                                                foreach($purchaseOrder->poCustomers as $poCustomer) {
                                                    $customerId = $poCustomer->customer_id;

                                                    if (!isset($customerSummary[$customerId])) {
                                                        $customerSummary[$customerId] = [
                                                            'customer' => $poCustomer->customer,
                                                            'total_items' => 0,
                                                            'total_received' => 0,
                                                            'total_bill' => 0
                                                        ];
                                                    }

                                                    $customerSummary[$customerId]['total_items'] += $poCustomer->item_quantity;
                                                    $customerSummary[$customerId]['total_received'] += $poCustomer->received_quantity;

                                                    // Hitung total tagihan berdasarkan produk yang diterima
                                                    $product = $poCustomer->product;
                                                    $customerSummary[$customerId]['total_bill'] += $product->price * $poCustomer->received_quantity;
                                                }

                                                // Inisialisasi total DP untuk semua pelanggan
                                                foreach($customerSummary as $custId => $data) {
                                                    $customerSummary[$custId]['total_dp'] = 0;
                                                }

                                                // Hitung total DP per pelanggan
                                                foreach($purchaseOrder->downPayments as $dp) {
                                                    $customerId = $dp->customer_id;
                                                    if (isset($customerSummary[$customerId])) {
                                                        $customerSummary[$customerId]['total_dp'] += $dp->amount;
                                                    }
                                                }
                                            @endphp

                                            @foreach($customerSummary as $summary)
                                            @php
                                                $outstandingAmount = $summary['total_bill'] - ($summary['total_dp'] ?? 0);
                                            @endphp
                                            <tr>
                                                <td>{{ $summary['customer']->name }}</td>
                                                <td>{{ $summary['total_items'] }}</td>
                                                <td>{{ $summary['total_received'] }}</td>
                                                <td>Rp {{ number_format($summary['total_dp'] ?? 0, 0, ',', '.') }}</td>
                                                <td>Rp {{ number_format($summary['total_bill'], 0, ',', '.') }}</td>
                                                <td>Rp {{ number_format($outstandingAmount, 0, ',', '.') }}</td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        @if($outstandingAmount > 0)
                                                            <a href="{{ route('po.customers.show-complete-transaction', [$purchaseOrder, $summary['customer']]) }}" class="btn btn-primary btn-sm">
                                                                <i class="fas fa-cash-register"></i> Proses
                                                            </a>
                                                        @elseif($summary['total_bill'] == 0)
                                                            <span class="badge bg-warning">Out Stock</span>
                                                        @else
                                                            <span class="badge bg-success">Lunas</span>
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