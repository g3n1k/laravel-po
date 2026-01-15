@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Detail Pesanan Pelanggan</h1>
            
            <div class="card">
                <div class="card-header">
                    <h5>Informasi Pesanan</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>PO:</strong></div>
                        <div class="col-md-8">{{ $poCustomer->purchaseOrder->title }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Pelanggan:</strong></div>
                        <div class="col-md-8">{{ $poCustomer->customer->name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Produk:</strong></div>
                        <div class="col-md-8">{{ $poCustomer->product->name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Jumlah Item:</strong></div>
                        <div class="col-md-8">{{ $poCustomer->item_quantity }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Item Diterima:</strong></div>
                        <div class="col-md-8">{{ $poCustomer->received_quantity }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Status:</strong></div>
                        <div class="col-md-8">
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
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Tanggal Dipesan:</strong></div>
                        <div class="col-md-8">{{ $poCustomer->ordered_at->format('d M Y H:i') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Dibuat:</strong></div>
                        <div class="col-md-8">{{ $poCustomer->created_at->format('d M Y H:i') }}</div>
                    </div>
                    <div class="row">
                        <div class="col-md-4"><strong>Diupdate:</strong></div>
                        <div class="col-md-8">{{ $poCustomer->updated_at->format('d M Y H:i') }}</div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('po.customers.index', $poCustomer->purchase_order_id) }}" class="btn btn-secondary">Kembali</a>
                    @if($isLinkedToCompletedTransaction)
                        <button class="btn btn-warning" disabled>Edit (Terkunci)</button>
                        <div class="mt-2 text-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            Request ini sudah terkait dengan transaksi yang selesai dan tidak bisa diedit atau dihapus.
                        </div>
                    @else
                        <a href="{{ route('po.customers.edit', [$poCustomer->purchase_order_id, $poCustomer]) }}" class="btn btn-warning">Edit</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection