@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Daftar Pesanan Pelanggan - {{ $purchaseOrder->title }}</h1>
            
            <div class="d-flex justify-content-between mb-3">
                <a href="{{ route('po.customers.create', $purchaseOrder) }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Pesanan Pelanggan
                </a>
                <a href="{{ route('po.index') }}" class="btn btn-secondary">Kembali ke Daftar PO</a>
            </div>
            
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Pelanggan</th>
                                    <th>Produk</th>
                                    <th>Jumlah Item</th>
                                    <th>Item Diterima</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($poCustomers as $poCustomer)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $poCustomer->customer->name }}</td>
                                    <td>{{ $poCustomer->product->name }}</td>
                                    <td>{{ $poCustomer->item_quantity }}</td>
                                    <td>{{ $poCustomer->received_quantity }}</td>
                                    <td>
                                        @if($poCustomer->status === 'waiting')
                                            <span class="badge bg-secondary">Waiting</span>
                                        @elseif($poCustomer->status === 'complete')
                                            <span class="badge bg-success">Complete</span>
                                        @elseif($poCustomer->status === 'out_of_stock')
                                            <span class="badge bg-danger">Out of Stock</span>
                                        @elseif($poCustomer->status === 'not_complete')
                                            <span class="badge bg-warning">Not Complete</span>
                                        @elseif($poCustomer->status === 'cancel')
                                            <span class="badge bg-dark">Cancel</span>
                                        @endif
                                    </td>
                                    <td>{{ $poCustomer->ordered_at->format('d M Y H:i') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('po.customers.show', [$purchaseOrder, $poCustomer]) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('po.customers.edit', [$purchaseOrder, $poCustomer]) }}" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('po.customers.destroy', [$purchaseOrder, $poCustomer]) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus pesanan ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada data pesanan pelanggan</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-center">
                        {{ $poCustomers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection