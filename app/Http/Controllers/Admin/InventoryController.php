<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\InventorySupplier;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InventoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = InventoryItem::with(['category', 'supplier']);

        // Apply filters
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'low_stock':
                    $query->lowStock();
                    break;
                case 'out_of_stock':
                    $query->outOfStock();
                    break;
                case 'expiring_soon':
                    $query->expiringSoon();
                    break;
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('name')->paginate(20);

        // Get filter options
        $categories = InventoryCategory::active()->ordered()->get();
        $suppliers = InventorySupplier::active()->orderBy('name')->get();
        $statuses = ['active', 'inactive', 'discontinued'];
        $stockStatuses = ['low_stock', 'out_of_stock', 'expiring_soon'];

        // Get statistics
        $stats = [
            'total_items' => InventoryItem::count(),
            'active_items' => InventoryItem::active()->count(),
            'low_stock_items' => InventoryItem::lowStock()->count(),
            'out_of_stock_items' => InventoryItem::outOfStock()->count(),
            'expiring_soon_items' => InventoryItem::expiringSoon()->count(),
            'total_value' => InventoryItem::sum(DB::raw('current_stock * unit_cost')),
            'categories_count' => InventoryCategory::active()->count(),
            'suppliers_count' => InventorySupplier::active()->count()
        ];

        return view('admin.inventory.index', compact(
            'items', 'categories', 'suppliers', 'statuses', 'stockStatuses', 'stats'
        ));
    }

    public function create()
    {
        $categories = InventoryCategory::active()->ordered()->get();
        $suppliers = InventorySupplier::active()->orderBy('name')->get();
        $units = ['pcs', 'kg', 'liters', 'boxes', 'pairs', 'sets', 'meters', 'pieces'];

        return view('admin.inventory.create', compact('categories', 'suppliers', 'units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:inventory_categories,id',
            'supplier_id' => 'nullable|exists:inventory_suppliers,id',
            'description' => 'nullable|string',
            'unit_of_measure' => 'required|string|max:50',
            'unit_cost' => 'required|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'current_stock' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'maximum_stock' => 'nullable|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'reorder_quantity' => 'required|integer|min:0',
            'location' => 'nullable|string|max:255',
            'shelf' => 'nullable|string|max:100',
            'expiry_date' => 'nullable|date|after:today',
            'is_trackable' => 'boolean',
            'requires_approval' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            $item = InventoryItem::create([
                'name' => $request->name,
                'sku' => $request->sku ?? InventoryItem::make()->generateSku(),
                'barcode' => $request->barcode ?? InventoryItem::make()->generateBarcode(),
                'description' => $request->description,
                'category_id' => $request->category_id,
                'supplier_id' => $request->supplier_id,
                'unit_of_measure' => $request->unit_of_measure,
                'unit_cost' => $request->unit_cost,
                'selling_price' => $request->selling_price,
                'current_stock' => $request->current_stock,
                'minimum_stock' => $request->minimum_stock,
                'maximum_stock' => $request->maximum_stock,
                'reorder_level' => $request->reorder_level,
                'reorder_quantity' => $request->reorder_quantity,
                'location' => $request->location,
                'shelf' => $request->shelf,
                'expiry_date' => $request->expiry_date,
                'is_trackable' => $request->boolean('is_trackable', true),
                'requires_approval' => $request->boolean('requires_approval', false),
                'status' => 'active'
            ]);

            // Create initial stock transaction if stock > 0
            if ($item->current_stock > 0) {
                $item->updateStock($item->current_stock, 'in');
            }

            DB::commit();

            return redirect()->route('admin.inventory.index')
                ->with('success', 'Inventory item created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->with('error', 'Failed to create inventory item: ' . $e->getMessage());
        }
    }

    public function show(InventoryItem $item)
    {
        $item->load(['category', 'supplier', 'transactions.createdBy', 'transactions.approvedBy']);
        
        $recentTransactions = $item->transactions()
            ->with(['createdBy', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.inventory.show', compact('item', 'recentTransactions'));
    }

    public function edit(InventoryItem $item)
    {
        $categories = InventoryCategory::active()->ordered()->get();
        $suppliers = InventorySupplier::active()->orderBy('name')->get();
        $units = ['pcs', 'kg', 'liters', 'boxes', 'pairs', 'sets', 'meters', 'pieces'];

        return view('admin.inventory.edit', compact('item', 'categories', 'suppliers', 'units'));
    }

    public function update(Request $request, InventoryItem $item)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:inventory_categories,id',
            'supplier_id' => 'nullable|exists:inventory_suppliers,id',
            'description' => 'nullable|string',
            'unit_of_measure' => 'required|string|max:50',
            'unit_cost' => 'required|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'maximum_stock' => 'nullable|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'reorder_quantity' => 'required|integer|min:0',
            'location' => 'nullable|string|max:255',
            'shelf' => 'nullable|string|max:100',
            'expiry_date' => 'nullable|date',
            'status' => 'required|in:active,inactive,discontinued',
            'is_trackable' => 'boolean',
            'requires_approval' => 'boolean'
        ]);

        $item->update($request->only([
            'name', 'category_id', 'supplier_id', 'description', 'unit_of_measure',
            'unit_cost', 'selling_price', 'minimum_stock', 'maximum_stock',
            'reorder_level', 'reorder_quantity', 'location', 'shelf',
            'expiry_date', 'status', 'is_trackable', 'requires_approval'
        ]));

        return redirect()->route('admin.inventory.show', $item)
            ->with('success', 'Inventory item updated successfully.');
    }

    public function destroy(InventoryItem $item)
    {
        if (!$item->canBeDeleted()) {
            return back()->with('error', 'Cannot delete item with existing transactions.');
        }

        $item->delete();

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Inventory item deleted successfully.');
    }

    public function adjustStock(Request $request, InventoryItem $item)
    {
        $request->validate([
            'quantity' => 'required|integer',
            'type' => 'required|in:in,out,adjustment,damage,loss',
            'notes' => 'nullable|string|max:1000',
            'reference_number' => 'nullable|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            $item->updateStock($request->quantity, $request->type);

            // Update transaction with additional details
            $transaction = $item->transactions()->latest()->first();
            $transaction->update([
                'notes' => $request->notes,
                'reference_number' => $request->reference_number
            ]);

            DB::commit();

            return back()->with('success', 'Stock adjusted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'Failed to adjust stock: ' . $e->getMessage());
        }
    }

    public function transferStock(Request $request, InventoryItem $item)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $item->current_stock,
            'location_from' => 'required|string|max:255',
            'location_to' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            $item->updateStock(-$request->quantity, 'transfer');

            // Update transaction with transfer details
            $transaction = $item->transactions()->latest()->first();
            $transaction->update([
                'location_from' => $request->location_from,
                'location_to' => $request->location_to,
                'notes' => $request->notes
            ]);

            DB::commit();

            return back()->with('success', 'Stock transferred successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'Failed to transfer stock: ' . $e->getMessage());
        }
    }

    public function statistics()
    {
        $stats = [
            'total_items' => InventoryItem::count(),
            'active_items' => InventoryItem::active()->count(),
            'low_stock_items' => InventoryItem::lowStock()->count(),
            'out_of_stock_items' => InventoryItem::outOfStock()->count(),
            'expiring_soon_items' => InventoryItem::expiringSoon()->count(),
            'total_value' => InventoryItem::sum(DB::raw('current_stock * unit_cost')),
            'categories_count' => InventoryCategory::active()->count(),
            'suppliers_count' => InventorySupplier::active()->count(),
            'recent_transactions' => InventoryTransaction::with(['item', 'createdBy'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
        ];

        return view('admin.inventory.statistics', compact('stats'));
    }
}
