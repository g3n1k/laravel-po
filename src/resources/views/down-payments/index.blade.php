@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Daftar Down Payment - {{ $purchaseOrder->title }}</h1>
            
            <div class="d-flex justify-content-between mb-3">
                <a href="{{ route('po.down-payments.create', $purchaseOrder) }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Down Payment
                </a>
                <a href="{{ route('master.purchase-orders.show', $purchaseOrder) }}" class="btn btn-info">
                    <i class="fas fa-eye"></i> Kembali Ke PO
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
                                    <th>Pelanggan</th>
                                    <th>Jumlah</th>
                                    <th>Tanggal Bayar</th>
                                    <th>Catatan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($downPayments as $downPayment)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $downPayment->customer->name }}</td>
                                    <td>Rp {{ number_format($downPayment->amount, 0, ',', '.') }}</td>
                                    <td>{{ $downPayment->paid_at->format('d M Y H:i') }}</td>
                                    <td>{{ $downPayment->notes ?? '-' }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('po.down-payments.show', [$purchaseOrder, $downPayment]) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($downPayment->transaction_summary_id === null)
                                                <a href="{{ route('po.down-payments.edit', [$purchaseOrder, $downPayment]) }}" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('po.down-payments.destroy', [$purchaseOrder, $downPayment]) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus down payment ini?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <button class="btn btn-warning btn-sm" disabled title="Sudah terkait dengan transaksi yang selesai">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-danger btn-sm" disabled title="Sudah terkait dengan transaksi yang selesai">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data down payment</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-center">
                        {{ $downPayments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection