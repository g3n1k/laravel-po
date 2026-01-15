<?php

namespace App\Http\Controllers\PurchaseOrder;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder as PurchaseOrderModel;
use App\Models\PoProduct;
use App\Models\Customer;
use App\Models\PoOrder;
use Illuminate\Http\Request;

class PoOrderController extends Controller
{
    public function index(PurchaseOrderModel $purchaseOrder)
    {
        $orders = $purchaseOrder->orders()->with(["customer", "product"])->get();
        
        return view("purchase-order.orders.index", compact("purchaseOrder", "orders"));
    }

    public function create(PurchaseOrderModel $purchaseOrder)
    {
        $customers = Customer::all();
        $products = $purchaseOrder->products()->get();
        
        return view("purchase-order.orders.create", compact("purchaseOrder", "customers", "products"));
    }

    public function store(Request $request, PurchaseOrderModel $purchaseOrder)
    {
        $request->validate([
            "customer_id" => "required|exists:customers,id",
            "product_id" => "required|exists:po_products,id",
            "quantity" => "required|integer|min:1",
        ]);

        $order = new PoOrder();
        $order->purchase_order_id = $purchaseOrder->id;
        $order->customer_id = $request->customer_id;
        $order->product_id = $request->product_id;
        $order->quantity = $request->quantity;
        $order->status = "waiting";
        $order->created_by = auth()->id(); // Assuming authenticated user
        $order->save();

        return redirect()->route("purchase-orders.orders.index", $purchaseOrder)->with("success", "Order added successfully.");
    }

    public function edit(PurchaseOrderModel $purchaseOrder, PoOrder $order)
    {
        $customers = Customer::all();
        $products = $purchaseOrder->products()->get();
        
        return view("purchase-order.orders.edit", compact("purchaseOrder", "order", "customers", "products"));
    }

    public function update(Request $request, PurchaseOrderModel $purchaseOrder, PoOrder $order)
    {
        $request->validate([
            "customer_id" => "required|exists:customers,id",
            "product_id" => "required|exists:po_products,id",
            "quantity" => "required|integer|min:1",
            "status" => "required|in:waiting,complete,out_of_stock,not_complete,cancel",
        ]);

        $order->update([
            "customer_id" => $request->customer_id,
            "product_id" => $request->product_id,
            "quantity" => $request->quantity,
            "status" => $request->status,
        ]);

        return redirect()->route("purchase-orders.orders.index", $purchaseOrder)->with("success", "Order updated successfully.");
    }

    public function destroy(PurchaseOrderModel $purchaseOrder, PoOrder $order)
    {
        $order->delete();

        return redirect()->route("purchase-orders.orders.index", $purchaseOrder)->with("success", "Order deleted successfully.");
    }
}
