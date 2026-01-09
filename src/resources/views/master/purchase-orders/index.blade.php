@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Daftar Purchase Order</h1>
            
            <div class="d-flex justify-content-between mb-3">
                <a href="{{ route('master.purchase-orders.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah PO
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
                                    <th>Judul</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Akhir</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchaseOrders as $po)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $po->title }}</td>
                                    <td>{{ $po->start_date->format('d M Y') }}</td>
                                    <td>{{ $po->end_date->format('d M Y') }}</td>
                                    <td>
                                        @if($po->status === 'open')
                                            <span class="badge bg-success">Open</span>
                                        @elseif($po->status === 'closed')
                                            <span class="badge bg-warning">Closed</span>
                                        @else
                                            <span class="badge bg-secondary">Completed</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('master.purchase-orders.show', $po) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('master.purchase-orders.edit', $po) }}" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('master.purchase-orders.destroy', $po) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus PO ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data purchase order</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-center">
                        {{ $purchaseOrders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection