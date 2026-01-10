<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::paginate(10);
        return view('master.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master.products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $product = Product::create($request->all());

        // Log activity when creating a product
        tulis_log_activity("menambah produk baru {$product->name}", Product::class, $product->id);

        return redirect()->route('master.products.index')->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return view('master.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view('master.products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $oldName = $product->name;
        $product->update($request->all());

        // Log activity when updating a product
        tulis_log_activity("mengedit produk {$oldName} menjadi {$product->name}", Product::class, $product->id);

        return redirect()->route('master.products.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $productName = $product->name;
        $product->delete();

        // Log activity when deleting a product
        tulis_log_activity("menghapus produk {$productName}", Product::class, $product->id);

        return redirect()->route('master.products.index')->with('success', 'Product deleted successfully.');
    }
}
