<?php

namespace App\Http\Controllers\PurchaseOrder;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder as PurchaseOrderModel;
use App\Models\PoProduct;
use App\Models\Customer;
use App\Models\PoOrder;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $purchaseOrders = PurchaseOrderModel::where("end_date", ">=", now())
            ->where("start_date", "<=", now())
            ->get();
        
        return view("purchase-order.index", compact("purchaseOrders"));
    }

    public function create()
    {
        return view("purchase-order.create");
    }

    public function store(Request $request)
    {
        $request->validate([
            "title" => "required|string|max:255",
            "description" => "nullable|string",
            "start_date" => "required|date",
            "end_date" => "required|date|after_or_equal:start_date",
        ]);

        PurchaseOrderModel::create($request->all());

        return redirect()->route("purchase-orders.index")->with("success", "Purchase Order created successfully.");
    }

    public function show(PurchaseOrderModel $purchaseOrder)
    {
        
        $customers = $purchaseOrder->orders()->with("customer")->distinct("customer_id")->get()->pluck("customer");
        $totalItems = $purchaseOrder->orders()->sum("quantity");
        
        $products = $purchaseOrder->products()
            ->select("po_products.*", 
                \DB::raw("SUM(po_orders.quantity) as total_quantity"))
            ->leftJoin("po_orders", "po_products.id", "=", "po_orders.product_id")
            ->groupBy("po_products.id", "po_products.name", "po_products.price", "po_products.stock", "po_products.free_stock")
            ->get();

        return view("purchase-order.show", compact("purchaseOrder", "customers", "totalItems", "products"));
    }

    public function edit(PurchaseOrderModel $purchaseOrder)
    {
        return view("purchase-order.edit", compact("purchaseOrder"));
    }

    public function update(Request $request, PurchaseOrderModel $purchaseOrder)
    {
        $request->validate([
            "title" => "required|string|max:255",
            "description" => "nullable|string",
            "start_date" => "required|date",
            "end_date" => "required|date|after_or_equal:start_date",
            "status" => "required|in:active,inactive,completed",
        ]);

        $purchaseOrder->update($request->all());

        return redirect()->route("purchase-orders.index")->with("success", "Purchase Order updated successfully.");
    }

    public function destroy(PurchaseOrderModel $purchaseOrder)
    {
        $purchaseOrder->delete();

        return redirect()->route("purchase-orders.index")->with("success", "Purchase Order deleted successfully.");
    }
}
