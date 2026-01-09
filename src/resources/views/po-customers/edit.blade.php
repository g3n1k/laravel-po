@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <h1>Edit Pesanan Pelanggan - {{ $poCustomer->purchaseOrder->title }}</h1>
            
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('po.customers.update', [$poCustomer->purchase_order_id, $poCustomer]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="customer_id" class="form-label">Pelanggan *</label>
                            <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id" required>
                                <option value="">Pilih Pelanggan</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id', $poCustomer->customer_id) == $customer->id ? 'selected' : '' }}>
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
                                    <option value="{{ $product->id }}" {{ old('product_id', $poCustomer->product_id) == $product->id ? 'selected' : '' }}>
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
                            <input type="number" class="form-control @error('item_quantity') is-invalid @enderror" id="item_quantity" name="item_quantity" value="{{ old('item_quantity', $poCustomer->item_quantity) }}" min="1" required>
                            @error('item_quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="received_quantity" class="form-label">Jumlah Diterima</label>
                            <input type="number" class="form-control @error('received_quantity') is-invalid @enderror" id="received_quantity" name="received_quantity" value="{{ old('received_quantity', $poCustomer->received_quantity) }}" min="0">
                            @error('received_quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="status" class="form-label">Status *</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="waiting" {{ old('status', $poCustomer->status) === 'waiting' ? 'selected' : '' }}>Waiting</option>
                                <option value="complete" {{ old('status', $poCustomer->status) === 'complete' ? 'selected' : '' }}>Complete</option>
                                <option value="out_of_stock" {{ old('status', $poCustomer->status) === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                                <option value="not_complete" {{ old('status', $poCustomer->status) === 'not_complete' ? 'selected' : '' }}>Not Complete</option>
                                <option value="cancel" {{ old('status', $poCustomer->status) === 'cancel' ? 'selected' : '' }}>Cancel</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex">
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="{{ route('po.customers.index', $poCustomer->purchase_order_id) }}" class="btn btn-secondary ms-2">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection