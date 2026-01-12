<!-- resources/views/po-customers/transaction-detail.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Detail Transaksi - {{ $customer->name }} ({{ $purchaseOrder->title }})</h1>

            <div class="d-flex justify-content-between mb-3">
                <a href="{{ route('master.purchase-orders.show', $purchaseOrder) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali ke Detail PO
                </a>
                <a href="{{ route('po.index') }}" class="btn btn-outline-secondary">Kembali ke Daftar PO</a>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Informasi Pelanggan -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Informasi Pelanggan</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3"><strong>Nama:</strong></div>
                        <div class="col-md-9">{{ $customer->name }}</div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3"><strong>Telepon:</strong></div>
                        <div class="col-md-9">{{ $customer->phone ?? '-' }}</div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3"><strong>Email:</strong></div>
                        <div class="col-md-9">{{ $customer->email ?? '-' }}</div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3"><strong>Alamat:</strong></div>
                        <div class="col-md-9">{{ $customer->address ?? '-' }}</div>
                    </div>
                </div>
            </div>

            <!-- Tabel Pesanan -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Daftar Pesanan</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Jumlah Pesanan</th>
                                    <th>Jumlah Diterima</th>
                                    <th>Harga Barang</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($aggregatedOrders as $order)
                                <tr>
                                    <td>{{ $order->product->name }}</td>
                                    <td>{{ $order->total_item_quantity }}</td>
                                    <td>{{ $order->total_received_quantity }}</td>
                                    <td>Rp {{ number_format($order->product->price, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($order->total_received_quantity * $order->product->price, 0, ',', '.') }}</td>
                                    <td>
                                        @if($order->total_received_quantity == 0)
                                            <span class="badge bg-danger">Out of Stock</span>
                                        @elseif($order->total_received_quantity < $order->total_item_quantity)
                                            <span class="badge bg-warning">Not Complete</span>
                                        @elseif($order->total_received_quantity >= $order->total_item_quantity)
                                            <span class="badge bg-success">Complete</span>
                                        @else
                                            <span class="badge bg-secondary">Waiting</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Ringkasan Pembayaran -->
            <div class="card">
                <div class="card-header">
                    <h5>Ringkasan Pembayaran</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="notes" class="form-label">Catatan</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" readonly>{{ $purchaseOrder->poCustomers->where('customer_id', $customer->id)->first()->notes ?? '' }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Total Tagihan:</span>
                                    <span id="total-bill">Rp {{ number_format($totalBill, 0, ',', '.') }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Total DP:</span>
                                    <span>- Rp {{ number_format($totalDP, 0, ',', '.') }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Sisa Pembayaran:</span>
                                    <span id="remaining-payment">Rp {{ number_format($remainingPayment, 0, ',', '.') }}</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between mb-3">
                                    <strong>Total yang Harus Dibayar:</strong>
                                    <strong id="final-amount">Rp {{ number_format(max(0, $remainingPayment), 0, ',', '.') }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection