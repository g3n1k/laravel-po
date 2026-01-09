@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Penyelesaian Transaksi - {{ $customer->name }} ({{ $purchaseOrder->title }})</h1>
            
            <div class="d-flex justify-content-between mb-3">
                <a href="{{ route('po.customers.index', $purchaseOrder) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar Pelanggan
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
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($aggregatedOrders as $order)
                                <tr>
                                    <td>{{ $order->product->name }}</td>
                                    <td>{{ $order->total_item_quantity }}</td>
                                    <td>
                                        <input type="number" class="form-control received-quantity"
                                               data-product-id="{{ $order->product->id }}"
                                               data-max-quantity="{{ $order->total_item_quantity }}"
                                               value="{{ $order->total_received_quantity }}"
                                               min="0"
                                               max="{{ $order->total_item_quantity }}">
                                    </td>
                                    <td>Rp {{ number_format($order->product->price, 0, ',', '.') }}</td>
                                    <td class="order-total">Rp {{ number_format($order->total_received_quantity * $order->product->price, 0, ',', '.') }}</td>
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
                                <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
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
                                    <strong id="final-amount">Rp {{ number_format($remainingPayment, 0, ',', '.') }}</strong>
                                </div>
                                
                                <form action="{{ route('po.customers.complete-transaction', [$purchaseOrder, $customer]) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="received_quantities" id="receivedQuantitiesInput">
                                    <input type="hidden" name="notes" id="notesInput">
                                    <button type="submit" class="btn btn-success w-100" id="complete-btn" {{ $remainingPayment > 0 ? 'disabled' : '' }}>
                                        <i class="fas fa-check-circle"></i> Selesaikan Transaksi
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const receivedQuantityInputs = document.querySelectorAll('.received-quantity');
    const totalBillElement = document.getElementById('total-bill');
    const remainingPaymentElement = document.getElementById('remaining-payment');
    const finalAmountElement = document.getElementById('final-amount');
    const completeBtn = document.getElementById('complete-btn');
    const notesInput = document.getElementById('notes');
    const receivedQuantitiesInput = document.querySelector('input[name="received_quantities"]');
    const notesHiddenInput = document.querySelector('input[name="notes"]');

    // Fungsi untuk menghitung ulang total dan sisa pembayaran
    function updateTotals() {
        let totalBill = 0;

        receivedQuantityInputs.forEach(input => {
            const maxQuantity = parseInt(input.dataset.maxQuantity);
            const receivedQty = Math.min(maxQuantity, Math.max(0, parseInt(input.value) || 0));

            // Pastikan jumlah diterima tidak melebihi jumlah pesanan
            input.value = receivedQty;

            const row = input.closest('tr');
            const priceText = row.cells[3].textContent;
            const price = parseFloat(priceText.replace(/[^\d,]/g, '').replace(',', ''));
            const orderTotal = receivedQty * price;

            // Update total untuk baris ini
            row.cells[4].textContent = 'Rp ' + orderTotal.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });

            totalBill += orderTotal;
        });

        // Update total tagihan
        totalBillElement.textContent = 'Rp ' + totalBill.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });

        // Hitung sisa pembayaran
        const totalDP = {{ $totalDP }};
        const remainingPayment = totalBill - totalDP;

        // Update sisa pembayaran
        remainingPaymentElement.textContent = 'Rp ' + Math.abs(remainingPayment).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        finalAmountElement.textContent = 'Rp ' + Math.max(0, remainingPayment).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });

        // Aktifkan/nonaktifkan tombol selesai berdasarkan sisa pembayaran
        if (remainingPayment <= 0) {
            completeBtn.disabled = false;
        } else {
            completeBtn.disabled = true;
        }

        // Simpan jumlah diterima ke input tersembunyi
        const quantities = {};
        receivedQuantityInputs.forEach(input => {
            quantities[input.dataset.productId] = input.value;
        });
        receivedQuantitiesInput.value = JSON.stringify(quantities);
    }

    // Tambahkan event listener untuk perubahan jumlah diterima
    receivedQuantityInputs.forEach(input => {
        input.addEventListener('change', updateTotals);
    });

    // Simpan catatan ke input tersembunyi saat berubah
    notesInput.addEventListener('input', function() {
        notesHiddenInput.value = this.value;
    });

    // Inisialisasi nilai awal
    updateTotals();
});
</script>
@endsection