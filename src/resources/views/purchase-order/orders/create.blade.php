@extends("layouts.app")

@section("content")
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-semibold">Add New Order to {{ $purchaseOrder->title }}</h1>
                    <a href="{{ route("purchase-orders.show", $purchaseOrder) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Back to PO</a>
                </div>

                <form method="POST" action="{{ route("purchase-orders.orders.store", $purchaseOrder) }}">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="customer_id" class="block text-sm font-medium text-gray-700">Customer</label>
                        <select name="customer_id" id="customer_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                            <option value="">Select a customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                        @error("customer_id")
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="product_id" class="block text-sm font-medium text-gray-700">Product</label>
                        <select name="product_id" id="product_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                            <option value="">Select a product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->price }}" data-stock="{{ $product->stock }}">
                                    {{ $product->name }} - Rp {{ number_format($product->price, 0, ",", ".") }} (Stock: {{ $product->stock }})
                                </option>
                            @endforeach
                        </select>
                        @error("product_id")
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity</label>
                        <input type="number" name="quantity" id="quantity" value="{{ old("quantity", 1) }}" min="1" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                        @error("quantity")
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6 p-4 bg-yellow-50 rounded-lg">
                        <p class="text-sm text-gray-700"><strong>Note:</strong> Customers can place multiple orders for the same product. The system will accumulate quantities for the same product ordered by the same customer.</p>
                    </div>

                    <div class="flex items-center justify-end">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Add Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const productSelect = document.getElementById("product_id");
    const quantityInput = document.getElementById("quantity");
    
    productSelect.addEventListener("change", function() {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const stock = selectedOption.getAttribute("data-stock");
        
        if(stock) {
            quantityInput.max = stock;
            quantityInput.placeholder = `Max: ${stock}`;
        }
    });
});
</script>
@endsection
