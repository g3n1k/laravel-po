@extends("layouts.app")

@section("content")
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-semibold">{{ $purchaseOrder->title }}</h1>
                    <a href="{{ route("purchase-orders.index") }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Back to List</a>
                </div>

                <div class="mb-8">
                    <h2 class="text-xl font-semibold mb-4">Summary</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <p class="text-gray-600">Start Date</p>
                            <p class="text-lg font-medium">{{ $purchaseOrder->start_date->format("Y-m-d") }}</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <p class="text-gray-600">End Date</p>
                            <p class="text-lg font-medium">{{ $purchaseOrder->end_date->format("Y-m-d") }}</p>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <p class="text-gray-600">Status</p>
                            <p class="text-lg font-medium">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($purchaseOrder->end_date < now()) bg-red-100 text-red-800 
                                    @elseif($purchaseOrder->start_date > now()) bg-yellow-100 text-yellow-800 
                                    @else bg-green-100 text-green-800 @endif">
                                    @if($purchaseOrder->end_date < now()) Expired 
                                    @elseif($purchaseOrder->start_date > now()) Upcoming 
                                    @else Active @endif
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <h2 class="text-xl font-semibold mb-4">Products</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="px-4 py-2">Product</th>
                                    <th class="px-4 py-2">Price</th>
                                    <th class="px-4 py-2">Stock</th>
                                    <th class="px-4 py-2">Free Stock</th>
                                    <th class="px-4 py-2">Total Ordered</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                <tr class="border-b">
                                    <td class="px-4 py-2">{{ $product->name }}</td>
                                    <td class="px-4 py-2">Rp {{ number_format($product->price, 0, ",", ".") }}</td>
                                    <td class="px-4 py-2">{{ $product->stock }}</td>
                                    <td class="px-4 py-2">{{ $product->free_stock }}</td>
                                    <td class="px-4 py-2">{{ $product->total_quantity ?? 0 }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mb-8">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold">Customer Orders</h2>
                        <a href="{{ route("purchase-orders.orders.create", $purchaseOrder) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Add Order</a>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="px-4 py-2">Customer</th>
                                    <th class="px-4 py-2">Product</th>
                                    <th class="px-4 py-2">Quantity</th>
                                    <th class="px-4 py-2">Status</th>
                                    <th class="px-4 py-2">Date</th>
                                    <th class="px-4 py-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                <tr class="border-b">
                                    <td class="px-4 py-2">{{ $order->customer->name }}</td>
                                    <td class="px-4 py-2">{{ $order->product->name }}</td>
                                    <td class="px-4 py-2">{{ $order->quantity }}</td>
                                    <td class="px-4 py-2">
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            @switch($order->status)
                                                @case("waiting")
                                                    bg-yellow-100 text-yellow-800
                                                    @break
                                                @case("complete")
                                                    bg-green-100 text-green-800
                                                    @break
                                                @case("out_of_stock")
                                                    bg-red-100 text-red-800
                                                    @break
                                                @case("not_complete")
                                                    bg-orange-100 text-orange-800
                                                    @break
                                                @case("cancel")
                                                    bg-gray-100 text-gray-800
                                                    @break
                                                @default
                                                    bg-gray-100 text-gray-800
                                            @endswitch">
                                            {{ ucfirst(str_replace("_", " ", $order->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2">{{ $order->created_at->format("Y-m-d H:i") }}</td>
                                    <td class="px-4 py-2">
                                        <a href="{{ route("purchase-orders.orders.edit", [$purchaseOrder, $order]) }}" class="text-yellow-500 hover:text-yellow-700 mr-2">Edit</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center px-4 py-2">No orders found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
