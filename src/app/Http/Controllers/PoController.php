<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PoController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\PurchaseOrder::query();

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan tanggal mulai
        if ($request->filled('date_from')) {
            $query->whereDate('start_date', '>=', $request->date_from);
        }

        // Filter berdasarkan tanggal akhir
        if ($request->filled('date_to')) {
            $query->whereDate('end_date', '<=', $request->date_to);
        }

        $purchaseOrders = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('po.index', compact('purchaseOrders'));
    }
}
