@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Daftar Purchase Order</h1>
            <p>Halaman ini menampilkan daftar semua Purchase Order yang aktif.</p>

            <div class="card">
                <div class="card-header">
                    <h5>Filter Purchase Order</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('po.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">Semua Status</option>
                                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="date_from" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="date_to" class="form-label">Tanggal Akhir</label>
                                <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-3">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="{{ route('po.index') }}" class="btn btn-secondary">Reset</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-4">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama PO</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Akhir</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchaseOrders as $po)
                        <tr>
                            <td>{{ $po->id }}</td>
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
                                    <a href="{{ route('po.customers.index', $po) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-users"></i> Pelanggan PO
                                    </a>
                                    <a href="{{ route('po.down-payments.index', $po) }}" class="btn btn-success btn-sm">
                                        <i class="fas fa-money-bill-wave"></i> DP
                                    </a>
                                    <a href="{{ route('po.stock-adjustments.index', $po) }}" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-boxes"></i> Stok
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Belum ada data Purchase Order</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex justify-content-center">
                    {{ $purchaseOrders->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection