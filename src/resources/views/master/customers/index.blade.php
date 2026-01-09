@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Daftar Pelanggan</h1>
            
            <div class="d-flex justify-content-between mb-3">
                <a href="{{ route('master.customers.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Pelanggan
                </a>
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
                                    <th>Nama</th>
                                    <th>Telepon</th>
                                    <th>Email</th>
                                    <th>Alamat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customers as $customer)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $customer->name }}</td>
                                    <td>{{ $customer->phone ?? '-' }}</td>
                                    <td>{{ $customer->email ?? '-' }}</td>
                                    <td>{{ Str::limit($customer->address ?? '', 50, '...') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('master.customers.show', $customer) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('master.customers.edit', $customer) }}" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('master.customers.destroy', $customer) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus pelanggan ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data pelanggan</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-center">
                        {{ $customers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection