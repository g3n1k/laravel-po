@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Daftar Penyesuaian Stok - {{ $purchaseOrder->title }}</h1>
            
            <div class="d-flex justify-content-between mb-3">
                <a href="{{ route('po.stock-adjustments.create', $purchaseOrder) }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Penyesuaian Stok
                </a>
                <a href="{{ route('po.index') }}" class="btn btn-secondary">Kembali ke Daftar PO</a>
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
                                    <th>Produk</th>
                                    <th>Stok Awal</th>
                                    <th>Penyesuaian</th>
                                    <th>Stok Akhir</th>
                                    <th>Alasan</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stockAdjustments as $product)
                                    @foreach($product->stockAdjustments as $adjustment)
                                    <tr>
                                        <td>{{ $loop->parent->iteration }}</td>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $adjustment->initial_stock }}</td>
                                        <td>{{ $adjustment->adjustment > 0 ? '+' : '' }}{{ $adjustment->adjustment }}</td>
                                        <td>{{ $adjustment->final_stock }}</td>
                                        <td>{{ $adjustment->reason }}</td>
                                        <td>{{ $adjustment->adjusted_at->format('d M Y H:i') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('po.stock-adjustments.show', [$purchaseOrder, $adjustment]) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('po.stock-adjustments.edit', [$purchaseOrder, $adjustment]) }}" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('po.stock-adjustments.destroy', [$purchaseOrder, $adjustment]) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus penyesuaian stok ini?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Tidak ada data penyesuaian stok</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-center">
                        {{ $stockAdjustments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection