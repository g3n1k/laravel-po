@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">Dashboard Purchase Order Management</h1>
            
            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title">{{ $activePOs ?? 0 }}</h4>
                                    <p class="card-text">Active POs</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-shopping-cart fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title">{{ $customersCount ?? 0 }}</h4>
                                    <p class="card-text">Customers</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title">{{ $productsCount ?? 0 }}</h4>
                                    <p class="card-text">Products</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-box fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-warning text-dark">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title">{{ $pendingOrders ?? 0 }}</h4>
                                    <p class="card-text">Pending Orders</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-2">
                                    <a href="{{ route('master.purchase-orders.create') }}" class="btn btn-primary w-100">
                                        <i class="fas fa-plus-circle"></i> Create New PO
                                    </a>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <a href="{{ route('master.customers.create') }}" class="btn btn-secondary w-100">
                                        <i class="fas fa-user-plus"></i> Add Customer
                                    </a>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <a href="{{ route('master.products.create') }}" class="btn btn-success w-100">
                                        <i class="fas fa-plus-square"></i> Add Product
                                    </a>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <a href="{{ route('po.index') }}" class="btn btn-info w-100">
                                        <i class="fas fa-list"></i> View PO List
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(isset($recentActivities) && $recentActivities->count() > 0)
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Log Aktivitas Terbaru</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Nama User</th>
                                            <th>Kegiatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentActivities as $activity)
                                        <tr>
                                            <td>{{ $activity->created_at->format('Y-m-d H:i') }}</td>
                                            <td>{{ $activity->user ? $activity->user->name : 'System' }}</td>
                                            <td>{{ $activity->description }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection