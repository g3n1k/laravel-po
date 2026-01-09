@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Edit Purchase Order - {{ $purchaseOrder->title }}</h1>

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('master.purchase-orders.update', $purchaseOrder) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Judul *</label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $purchaseOrder->title) }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Deskripsi</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $purchaseOrder->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="start_date" class="form-label">Tanggal Mulai *</label>
                                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date', $purchaseOrder->start_date->format('Y-m-d')) }}" required>
                                            @error('start_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="end_date" class="form-label">Tanggal Akhir *</label>
                                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date', $purchaseOrder->end_date->format('Y-m-d')) }}" required>
                                            @error('end_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="status" class="form-label">Status *</label>
                                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                        <option value="open" {{ old('status', $purchaseOrder->status) === 'open' ? 'selected' : '' }}>Open</option>
                                        <option value="closed" {{ old('status', $purchaseOrder->status) === 'closed' ? 'selected' : '' }}>Closed</option>
                                        <option value="completed" {{ old('status', $purchaseOrder->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <h5>Produk dalam PO ini</h5>
                                    <p class="text-muted">Tambahkan atau hapus produk dalam PO ini</p>

                                    <div class="row mb-3">
                                        <div class="col-md-8">
                                            <select class="form-select" id="product_select">
                                                <option value="">Pilih Produk</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->price }}" data-stock="{{ $product->stock }}">
                                                        {{ $product->name }} - Rp {{ number_format($product->price, 2, ',', '.') }} (Stok: {{ $product->stock }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="button" class="btn btn-primary" id="add_product_btn">Tambahkan</button>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="products_table">
                                            <thead>
                                                <tr>
                                                    <th>Nama Produk</th>
                                                    <th>Harga</th>
                                                    <th>Stok</th>
                                                    <th>Kuantitas</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($currentProducts as $product)
                                                <tr data-product-id="{{ $product->id }}">
                                                    <td>{{ $product->name }}</td>
                                                    <td>Rp {{ number_format($product->price, 2, ',', '.') }}</td>
                                                    <td>{{ $product->stock }}</td>
                                                    <td>
                                                        <input type="number" class="form-control quantity-input" value="{{ $purchaseOrder->poItems->where('product_id', $product->id)->first()?->quantity ?? 0 }}" min="0" max="{{ $product->stock }}" data-product-id="{{ $product->id }}">
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger btn-sm remove_product" data-product-id="{{ $product->id }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Hidden inputs untuk menyimpan produk yang dipilih -->
                                    <div id="selected_products_inputs">
                                        @foreach($currentProducts as $product)
                                            <input type="hidden" name="products[]" value="{{ $product->id }}" class="selected_product_input">
                                            <input type="hidden" name="quantities[{{ $product->id }}]" value="{{ $purchaseOrder->poItems->where('product_id', $product->id)->first()?->quantity ?? 0 }}" class="selected_quantity_input" data-product-id="{{ $product->id }}">
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex">
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="{{ route('master.purchase-orders.index') }}" class="btn btn-secondary ms-2">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const productSelect = document.getElementById('product_select');
    const addProductBtn = document.getElementById('add_product_btn');
    const productsTableBody = document.querySelector('#products_table tbody');
    const selectedProductsInputs = document.getElementById('selected_products_inputs');

    // Ambil produk-produk yang sudah dipilih sebelumnya
    let selectedProducts = [];
    document.querySelectorAll('.selected_product_input').forEach(input => {
        const productId = input.value;
        const productRow = document.querySelector(`tr[data-product-id="${productId}"]`);
        if (productRow) {
            const productName = productRow.cells[0].textContent;
            const productPrice = productRow.cells[1].textContent.replace(/[^\d,.]/g, '');
            const productStock = productRow.cells[2].textContent;
            const productQuantity = productRow.cells[3].querySelector('input').value;

            selectedProducts.push({
                id: productId,
                name: productName,
                price: productPrice,
                stock: productStock,
                quantity: productQuantity
            });
        }
    });

    addProductBtn.addEventListener('click', function() {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        if (!selectedOption.value) {
            alert('Silakan pilih produk terlebih dahulu');
            return;
        }

        const productId = selectedOption.value;
        const productName = selectedOption.getAttribute('data-name');
        const productPrice = selectedOption.getAttribute('data-price');
        const productStock = selectedOption.getAttribute('data-stock');

        // Cek apakah produk sudah ditambahkan sebelumnya
        if (selectedProducts.some(p => p.id == productId)) {
            alert('Produk ini sudah ditambahkan');
            return;
        }

        // Tambahkan produk ke array
        selectedProducts.push({
            id: productId,
            name: productName,
            price: productPrice,
            stock: productStock,
            quantity: 0
        });

        // Tambahkan baris ke tabel
        const newRow = document.createElement('tr');
        newRow.setAttribute('data-product-id', productId);
        newRow.innerHTML = `
            <td>${productName}</td>
            <td>Rp ${parseFloat(productPrice).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
            <td>${productStock}</td>
            <td>
                <input type="number" class="form-control quantity-input" value="0" min="0" max="${productStock}" data-product-id="${productId}">
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove_product" data-product-id="${productId}">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        productsTableBody.appendChild(newRow);

        // Tambahkan input tersembunyi untuk produk
        const productInput = document.createElement('input');
        productInput.type = 'hidden';
        productInput.name = 'products[]';
        productInput.value = productId;
        productInput.className = 'selected_product_input';
        selectedProductsInputs.appendChild(productInput);

        // Tambahkan input tersembunyi untuk quantity
        const quantityInput = document.createElement('input');
        quantityInput.type = 'hidden';
        quantityInput.name = 'quantities[' + productId + ']';
        quantityInput.value = '0';
        quantityInput.className = 'selected_quantity_input';
        quantityInput.setAttribute('data-product-id', productId);
        selectedProductsInputs.appendChild(quantityInput);

        // Reset pilihan
        productSelect.value = '';
    });

    // Event delegation untuk input kuantitas
    productsTableBody.addEventListener('change', function(e) {
        if (e.target.classList.contains('quantity-input')) {
            const productId = e.target.getAttribute('data-product-id');
            const quantityValue = e.target.value;

            // Update input tersembunyi untuk quantity
            const quantityInput = document.querySelector(`.selected_quantity_input[data-product-id="${productId}"]`);
            if (quantityInput) {
                quantityInput.value = quantityValue;
            }
        }
    });

    // Event delegation untuk tombol hapus
    productsTableBody.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove_product') || e.target.closest('.remove_product')) {
            const button = e.target.closest('.remove_product');
            const productId = button.dataset.productId;

            // Hapus dari array
            selectedProducts = selectedProducts.filter(p => p.id != productId);

            // Hapus baris dari tabel
            const row = button.closest('tr');
            row.remove();

            // Hapus input tersembunyi produk
            const productInputs = document.querySelectorAll(`.selected_product_input[value="${productId}"]`);
            productInputs.forEach(input => input.remove());

            // Hapus input tersembunyi quantity
            const quantityInputs = document.querySelectorAll(`.selected_quantity_input[data-product-id="${productId}"]`);
            quantityInputs.forEach(input => input.remove());
        }
    });
});
</script>
@endsection