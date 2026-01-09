@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <h1>Detail Down Payment - {{ $downPayment->purchaseOrder->title }}</h1>
            
            <div class="card">
                <div class="card-header">
                    <h5>Informasi Down Payment</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Pelanggan:</strong></div>
                        <div class="col-md-8">{{ $downPayment->customer->name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Jumlah:</strong></div>
                        <div class="col-md-8">Rp {{ number_format($downPayment->amount, 0, ',', '.') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Tanggal Bayar:</strong></div>
                        <div class="col-md-8">{{ $downPayment->paid_at->format('d M Y H:i') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Catatan:</strong></div>
                        <div class="col-md-8">{{ $downPayment->notes ?? '-' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Dibuat:</strong></div>
                        <div class="col-md-8">{{ $downPayment->created_at->format('d M Y H:i') }}</div>
                    </div>
                    <div class="row">
                        <div class="col-md-4"><strong>Diupdate:</strong></div>
                        <div class="col-md-8">{{ $downPayment->updated_at->format('d M Y H:i') }}</div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('po.down-payments.index', $downPayment->purchase_order_id) }}" class="btn btn-secondary">Kembali</a>
                    <a href="{{ route('po.down-payments.edit', [$downPayment->purchase_order_id, $downPayment]) }}" class="btn btn-warning">Edit</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection