<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\InventoryCategory;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function dashboard()
    {
        // Get statistics
        $stats = [
            'total_items' => InventoryItem::count(),
            'low_stock_items' => InventoryItem::where('current_stock', '<=', DB::raw('minimum_stock'))->count(),
            'out_of_stock_items' => InventoryItem::where('current_stock', 0)->count(),
            'total_categories' => InventoryCategory::count(),
            'total_transactions' => InventoryTransaction::count(),
            'pending_requests' => InventoryTransaction::where('status', 'pending')->count(),
            'approved_requests' => InventoryTransaction::where('status', 'approved')->count(),
            'rejected_requests' => InventoryTransaction::where('status', 'rejected')->count(),
        ];

        // Get recent transactions
        $recentTransactions = InventoryTransaction::with(['item', 'requestedBy', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get low stock items
        $lowStockItems = InventoryItem::with('category')
            ->where('current_stock', '<=', DB::raw('minimum_stock'))
            ->orderBy('current_stock', 'asc')
            ->limit(5)
            ->get();

        return view('admin.inventory.dashboard', compact('stats', 'recentTransactions', 'lowStockItems'));
    }
}
