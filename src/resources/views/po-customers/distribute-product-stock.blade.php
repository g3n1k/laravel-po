@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Distribusi Stok Produk - {{ $product->name }} ({{ $purchaseOrder->title }})</h1>

            <div class="d-flex justify-content-between mb-3">
                <a href="{{ route('master.purchase-orders.show', $purchaseOrder) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali ke Detail PO
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('po.po.product.process-distribute-stock', ['purchaseOrder' => $purchaseOrder, 'product' => $product]) }}" method="POST">
                        @csrf

                        <div class="mb-4 border rounded p-3">
                            <h4 class="mb-3">Produk: {{ $product->name }}</h4>
                            <p><strong>Harga:</strong> Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                            <p><strong>Stok Tersedia:</strong> {{ $product->stock }}</p>
                        </div>

                        <!-- Tabel Pesanan Belum Dibayar -->
                        @if($unpaidPoCustomers->count() > 0)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5>Pesanan Belum Dibayar</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Pelanggan</th>
                                                <th>Tanggal Pesan</th>
                                                <th>Jumlah Pesanan</th>
                                                <th>Jumlah Diterima</th>
                                                <th>Total</th>
                                                <th>Status Pembayaran</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                            $stock = $product->stock;
                                            @endphp
                                            @foreach($unpaidPoCustomers as $order)
                                                <tr>
                                                    <td>{{ $order->customer->name }}</td>
                                                    <td>{{ $order->ordered_at->format('d M Y H:i') }}</td>
                                                    <td>{{ $order->item_quantity }}</td>
                                                    @php
                                                    // stock nya 0
                                                    if($stock == 0) {
                                                        $received_quantity = 0;
                                                    }

                                                    // stock masih banyak atau = permintaan
                                                    elseif( $stock >=  $order->item_quantity) {
                                                        $stock -=  $order->item_quantity;
                                                        $received_quantity = $order->item_quantity;
                                                    }

                                                    // stocknya lebih sedikit dari permintaan
                                                    else {
                                                        $received_quantity = $stock;
                                                        $stock = 0;
                                                    }
                                                    @endphp
                                                    <td>
                                                        <input type="number"
                                                               name="received_quantities[{{ $order->id }}]"
                                                               value="{{ $order->received_quantity ?: $received_quantity }}"
                                                               min="0"
                                                               max="{{ $order->item_quantity }}"
                                                               class="form-control received-quantity"
                                                               data-price="{{ $order->product->price }}"
                                                               data-order-id="{{ $order->id }}" />
                                                    </td>
                                                    <td class="total-amount-{{ $order->id }}">
                                                        Rp <span class="total-value">{{ number_format($order->product->price * ($order->received_quantity ?: $received_quantity), 0, ',', '.') }}</span>
                                                    </td>
                                                    <td>
                                                        @if($order->payment_status === 'paid' || $order->transaction_summary_id)
                                                            <span class="badge bg-success">Paid</span>
                                                        @else
                                                            <span class="badge bg-warning">Unpaid</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Tabel Pesanan Sudah Dibayar -->
                        @if($paidPoCustomers->count() > 0)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5>Pesanan Sudah Dibayar</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Pelanggan</th>
                                                <th>Tanggal Pesan</th>
                                                <th>Jumlah Pesanan</th>
                                                <th>Jumlah Diterima</th>
                                                <th>Total</th>
                                                <th>Status Pembayaran</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($paidPoCustomers as $order)
                                                <tr>
                                                    <td>{{ $order->customer->name }}</td>
                                                    <td>{{ $order->ordered_at->format('d M Y H:i') }}</td>
                                                    <td>{{ $order->item_quantity }}</td>
                                                    <td>{{ $order->received_quantity }}</td>
                                                    <td class="total-amount-{{ $order->id }}">
                                                        Rp <span class="total-value">{{ number_format($order->product->price * $order->received_quantity, 0, ',', '.') }}</span>
                                                    </td>
                                                    <td>
                                                        @if($order->payment_status === 'paid' || $order->transaction_summary_id)
                                                            <span class="badge bg-success">Paid</span>
                                                        @else
                                                            <span class="badge bg-warning">Unpaid</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($unpaidPoCustomers->count() == 0 && $paidPoCustomers->count() == 0)
                        <div class="alert alert-info">
                            Tidak ada pesanan untuk produk ini dalam PO ini.
                        </div>
                        @endif

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Distribusi Stok
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.received-quantity');

    inputs.forEach(input => {
        input.addEventListener('input', function() {
            const price = parseFloat(this.getAttribute('data-price'));
            const orderId = this.getAttribute('data-order-id');
            const quantity = parseInt(this.value) || 0;
            const total = price * quantity;

            // Format the total with thousands separator
            const formattedTotal = total.toLocaleString('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).replace(/,/g, '.');

            // Update the total in the corresponding cell
            const totalCell = document.querySelector(`.total-amount-${orderId} .total-value`);
            if (totalCell) {
                totalCell.textContent = formattedTotal;
            }
        });
    });
});
</script>

@endsection
