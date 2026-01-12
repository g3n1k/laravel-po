<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseOrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Data Master Routes
    Route::prefix('master')->name('master.')->group(function () {
        // Customers routes
        Route::prefix('customers')->name('customers.')->group(function () {
            Route::get('/', [CustomerController::class, 'index'])->name('index');
            Route::get('/create', [CustomerController::class, 'create'])->name('create');
            Route::post('/', [CustomerController::class, 'store'])->name('store');
            Route::get('/{customer}', [CustomerController::class, 'show'])->name('show');
            Route::get('/{customer}/edit', [CustomerController::class, 'edit'])->name('edit');
            Route::put('/{customer}', [CustomerController::class, 'update'])->name('update');
            Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('destroy');
        });

        // Products routes
        Route::prefix('products')->name('products.')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('index');
            Route::get('/create', [ProductController::class, 'create'])->name('create');
            Route::post('/', [ProductController::class, 'store'])->name('store');
            Route::get('/{product}', [ProductController::class, 'show'])->name('show');
            Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
            Route::put('/{product}', [ProductController::class, 'update'])->name('update');
            Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
        });

        // Purchase Orders routes
        Route::prefix('purchase-orders')->name('purchase-orders.')->group(function () {
            Route::get('/', [PurchaseOrderController::class, 'index'])->name('index');
            Route::get('/create', [PurchaseOrderController::class, 'create'])->name('create');
            Route::post('/', [PurchaseOrderController::class, 'store'])->name('store');
            Route::get('/{purchaseOrder}', [PurchaseOrderController::class, 'show'])->name('show');
            Route::get('/{purchaseOrder}/edit', [PurchaseOrderController::class, 'edit'])->name('edit');
            Route::put('/{purchaseOrder}', [PurchaseOrderController::class, 'update'])->name('update');
            Route::delete('/{purchaseOrder}', [PurchaseOrderController::class, 'destroy'])->name('destroy');
        });
    });

    // Purchase Order Management Routes
    Route::prefix('po')->name('po.')->group(function () {
        Route::get('/', [App\Http\Controllers\PoController::class, 'index'])->name('index');

        // PO Customers (orders by customers)
        Route::prefix('{purchaseOrder}/customers')->name('customers.')->group(function () {
            Route::get('/', [App\Http\Controllers\PoCustomerController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\PoCustomerController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\PoCustomerController::class, 'store'])->name('store');
            Route::get('/{poCustomer}', [App\Http\Controllers\PoCustomerController::class, 'show'])->name('show');
            Route::get('/{poCustomer}/edit', [App\Http\Controllers\PoCustomerController::class, 'edit'])->name('edit');
            Route::put('/{poCustomer}', [App\Http\Controllers\PoCustomerController::class, 'update'])->name('update');
            Route::delete('/{poCustomer}', [App\Http\Controllers\PoCustomerController::class, 'destroy'])->name('destroy');
        });

        // Stock distribution routes (outside the customers prefix group)
        Route::prefix('{purchaseOrder}/customers')->group(function () {
            Route::get('/distribute-stock', [App\Http\Controllers\PoCustomerController::class, 'distributeStock'])->name('po.customers.distribute-stock');
            Route::post('/distribute-stock', [App\Http\Controllers\PoCustomerController::class, 'processDistributeStock'])->name('po.customers.process-distribute-stock');
        });

        // Product-specific stock distribution route
        Route::get('/{purchaseOrder}/product/{product}/distribute-stock', [App\Http\Controllers\PoCustomerController::class, 'distributeProductStock'])->name('po.product.distribute-stock');
        Route::post('/{purchaseOrder}/product/{product}/distribute-stock', [App\Http\Controllers\PoCustomerController::class, 'processDistributeProductStock'])->name('po.product.process-distribute-stock');

        // Down payments
        Route::prefix('{purchaseOrder}/down-payments')->name('down-payments.')->group(function () {
            Route::get('/', [App\Http\Controllers\DownPaymentController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\DownPaymentController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\DownPaymentController::class, 'store'])->name('store');
            Route::get('/{downPayment}', [App\Http\Controllers\DownPaymentController::class, 'show'])->name('show');
            Route::get('/{downPayment}/edit', [App\Http\Controllers\DownPaymentController::class, 'edit'])->name('edit');
            Route::put('/{downPayment}', [App\Http\Controllers\DownPaymentController::class, 'update'])->name('update');
            Route::delete('/{downPayment}', [App\Http\Controllers\DownPaymentController::class, 'destroy'])->name('destroy');
        });

        // Stock adjustments
        Route::prefix('{purchaseOrder}/stock-adjustments')->name('stock-adjustments.')->group(function () {
            Route::get('/', [App\Http\Controllers\StockAdjustmentController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\StockAdjustmentController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\StockAdjustmentController::class, 'store'])->name('store');
            Route::get('/{stockAdjustment}', [App\Http\Controllers\StockAdjustmentController::class, 'show'])->name('show');
            Route::get('/{stockAdjustment}/edit', [App\Http\Controllers\StockAdjustmentController::class, 'edit'])->name('edit');
            Route::put('/{stockAdjustment}', [App\Http\Controllers\StockAdjustmentController::class, 'update'])->name('update');
            Route::delete('/{stockAdjustment}', [App\Http\Controllers\StockAdjustmentController::class, 'destroy'])->name('destroy');
        });

        // Complete transaction
        Route::get('{purchaseOrder}/complete-transaction/{customer}', [App\Http\Controllers\PoCustomerController::class, 'showCompleteTransaction'])->name('customers.show-complete-transaction');
        Route::post('{purchaseOrder}/complete-transaction/{customer}', [App\Http\Controllers\PoCustomerController::class, 'completeTransaction'])->name('customers.complete-transaction');

        // Transaction detail
        Route::get('{purchaseOrder}/transaction-detail/{customer}', [App\Http\Controllers\PoCustomerController::class, 'showTransactionDetail'])->name('customers.show-transaction-detail');
    });
});

require __DIR__.'/auth.php';
