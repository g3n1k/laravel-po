@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <h1>Tambah Down Payment - {{ $purchaseOrder->title }}</h1>
            
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('po.down-payments.store', $purchaseOrder) }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="customer_id" class="form-label">Pelanggan *</label>
                            <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id" required>
                                <option value="">Pilih Pelanggan</option>
                                @forelse($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }}
                                    </option>
                                @empty
                                    <option value="">Tidak ada pelanggan dalam PO ini</option>
                                @endforelse
                            </select>
                            @error('customer_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="amount" class="form-label">Jumlah *</label>
                            <input type="number" step="1" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount', 0) }}" min="0" required>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Catatan</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="{{ route('po.down-payments.index', $purchaseOrder) }}" class="btn btn-secondary ms-2">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection