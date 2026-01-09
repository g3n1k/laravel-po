@extends("layouts.app")

@section("content")
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-semibold">Purchase Orders</h1>
                    <a href="{{ route("purchase-orders.create") }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Create New PO</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2">Title</th>
                                <th class="px-4 py-2">Start Date</th>
                                <th class="px-4 py-2">End Date</th>
                                <th class="px-4 py-2">Status</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($purchaseOrders as $po)
                            <tr class="border-b">
                                <td class="px-4 py-2">{{ $po->title }}</td>
                                <td class="px-4 py-2">{{ $po->start_date->format("Y-m-d") }}</td>
                                <td class="px-4 py-2">{{ $po->end_date->format("Y-m-d") }}</td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        @if($po->end_date < now()) bg-red-100 text-red-800 
                                        @elseif($po->start_date > now()) bg-yellow-100 text-yellow-800 
                                        @else bg-green-100 text-green-800 @endif">
                                        @if($po->end_date < now()) Expired 
                                        @elseif($po->start_date > now()) Upcoming 
                                        @else Active @endif
                                    </span>
                                </td>
                                <td class="px-4 py-2">
                                    <a href="{{ route("purchase-orders.show", $po) }}" class="text-blue-500 hover:text-blue-700 mr-2">View</a>
                                    <a href="{{ route("purchase-orders.edit", $po) }}" class="text-yellow-500 hover:text-yellow-700 mr-2">Edit</a>
                                    <form action="{{ route("purchase-orders.destroy", $po) }}" method="POST" class="inline">
                                        @csrf
                                        @method("DELETE")
                                        <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm(&quot;Are you sure?&quot;)">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center px-4 py-2">No purchase orders found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
