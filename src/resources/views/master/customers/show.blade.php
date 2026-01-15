@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <h1>Detail Pelanggan</h1>
            
            <div class="card">
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
                    <div class="row mt-2">
                        <div class="col-md-3"><strong>Dibuat:</strong></div>
                        <div class="col-md-9">{{ $customer->created_at->format('d M Y H:i') }}</div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3"><strong>Diupdate:</strong></div>
                        <div class="col-md-9">{{ $customer->updated_at->format('d M Y H:i') }}</div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('master.customers.index') }}" class="btn btn-secondary">Kembali</a>
                    <a href="{{ route('master.customers.edit', $customer) }}" class="btn btn-warning">Edit</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection