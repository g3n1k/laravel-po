<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ActivityLog;
use App\Services\ActivityLogService;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ActivityLogService $activityLogService)
    {
        $activePOs = PurchaseOrder::where('status', 'open')->count();
        $customersCount = Customer::count();
        $productsCount = Product::count();
        $pendingOrders = 0; // Hitung jumlah pesanan pending jika diperlukan
        $recentActivities = $activityLogService->getRecentActivities(5);

        return view('dashboard', compact('activePOs', 'customersCount', 'productsCount', 'pendingOrders', 'recentActivities'));
    }
}
