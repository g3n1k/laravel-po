@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Distribusi Stok - {{ $purchaseOrder->title }}</h1>

            <div class="d-flex justify-content-between mb-3">
                <a href="{{ route('po.customers.index', $purchaseOrder) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar Pesanan
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('po.customers.process-distribute-stock', $purchaseOrder) }}" method="POST">
                        @csrf
                        
                        @foreach($poCustomersGrouped as $customerId => $customerOrders)
                            @php
                                $customer = $customerOrders->first()->customer;
                            @endphp
                            
                            <div class="mb-4 border rounded p-3">
                                <h4 class="mb-3">Pelanggan: {{ $customer->name }}</h4>
                                
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Produk</th>
                                                <th>Harga</th>
                                                <th>Jumlah Pesanan</th>
                                                <th>Jumlah Diterima</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($customerOrders as $order)
                                                <tr>
                                                    <td>{{ $order->product->name }}</td>
                                                    <td>Rp {{ number_format($order->product->price, 0, ',', '.') }}</td>
                                                    <td>{{ $order->item_quantity }}</td>
                                                    <td>
                                                        <input type="number" 
                                                               name="received_quantities[{{ $order->id }}]" 
                                                               value="{{ $order->received_quantity ?: $order->item_quantity }}" 
                                                               min="0" 
                                                               max="{{ $order->item_quantity }}"
                                                               class="form-control" />
                                                    </td>
                                                    <td>
                                                        Rp {{ number_format($order->product->price * ($order->received_quantity ?: $order->item_quantity), 0, ',', '.') }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach

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
@endsection
