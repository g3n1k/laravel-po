@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <h1>Detail Produk</h1>
            
            <div class="card">
                <div class="card-header">
                    <h5>Informasi Produk</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3"><strong>Nama:</strong></div>
                        <div class="col-md-9">{{ $product->name }}</div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3"><strong>Harga:</strong></div>
                        <div class="col-md-9">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3"><strong>Stok:</strong></div>
                        <div class="col-md-9">{{ $product->stock }}</div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3"><strong>Deskripsi:</strong></div>
                        <div class="col-md-9">{{ $product->description ?? '-' }}</div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3"><strong>Dibuat:</strong></div>
                        <div class="col-md-9">{{ $product->created_at->format('d M Y H:i') }}</div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3"><strong>Diupdate:</strong></div>
                        <div class="col-md-9">{{ $product->updated_at->format('d M Y H:i') }}</div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('master.products.index') }}" class="btn btn-secondary">Kembali</a>
                    <a href="{{ route('master.products.edit', $product) }}" class="btn btn-warning">Edit</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection