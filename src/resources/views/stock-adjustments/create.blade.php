@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <h1>Tambah Penyesuaian Stok - {{ $purchaseOrder->title }}</h1>
            
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('po.stock-adjustments.store', $purchaseOrder) }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="product_id" class="form-label">Produk *</label>
                            <select class="form-select @error('product_id') is-invalid @enderror" id="product_id" name="product_id" required>
                                <option value="">Pilih Produk</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }} - Stok: {{ $product->stock }}
                                    </option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="adjustment" class="form-label">Penyesuaian *</label>
                            <input type="number" class="form-control @error('adjustment') is-invalid @enderror" id="adjustment" name="adjustment" value="{{ old('adjustment', 0) }}" required>
                            <div class="form-text">Gunakan angka positif untuk menambah stok, angka negatif untuk mengurangi stok</div>
                            @error('adjustment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="reason" class="form-label">Alasan *</label>
                            <textarea class="form-control @error('reason') is-invalid @enderror" id="reason" name="reason" rows="3" required>{{ old('reason') }}</textarea>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="{{ route('po.stock-adjustments.index', $purchaseOrder) }}" class="btn btn-secondary ms-2">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection