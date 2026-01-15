@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <h1>Tambah Pesanan Pelanggan - {{ $purchaseOrder->title }}</h1>
            
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('po.customers.store', $purchaseOrder) }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="customer_id" class="form-label">Pelanggan *</label>
                            <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id" required>
                                <option value="">Pilih Pelanggan</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="product_id" class="form-label">Produk *</label>
                            <select class="form-select @error('product_id') is-invalid @enderror" id="product_id" name="product_id" required>
                                <option value="">Pilih Produk</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }} - Rp {{ number_format($product->price, 0, ',', '.') }} (Stok: {{ $product->stock }})
                                    </option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="item_quantity" class="form-label">Jumlah Item *</label>
                            <input type="number" class="form-control @error('item_quantity') is-invalid @enderror" id="item_quantity" name="item_quantity" value="{{ old('item_quantity', 1) }}" min="1" required>
                            @error('item_quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="{{ route('po.customers.index', $purchaseOrder) }}" class="btn btn-secondary ms-2">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection