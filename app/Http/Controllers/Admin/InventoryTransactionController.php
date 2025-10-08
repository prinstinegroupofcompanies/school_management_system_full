<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryTransaction;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryTransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = InventoryTransaction::with(['item', 'user']);

        // Apply filters
        if ($request->filled('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }

        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhereHas('item', function ($itemQuery) use ($search) {
                      $itemQuery->where('item_name', 'like', "%{$search}%");
                  });
            });
        }

        $transactions = $query->latest()->paginate(20);

        $items = InventoryItem::select('id', 'item_name')->orderBy('item_name')->get();
        $transactionTypes = ['in', 'out', 'transfer', 'adjustment'];

        return view('admin.inventory.transactions.index', compact(
            'transactions', 'items', 'transactionTypes'
        ));
    }

    public function create()
    {
        $items = InventoryItem::select('id', 'item_name', 'current_stock')->orderBy('item_name')->get();
        $transactionTypes = ['in', 'out', 'transfer', 'adjustment'];

        return view('admin.inventory.transactions.create', compact('items', 'transactionTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:inventory_items,id',
            'transaction_type' => 'required|in:in,out,transfer,adjustment',
            'quantity' => 'required|numeric|min:0.01',
            'transaction_date' => 'required|date',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'destination_location' => 'nullable|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            $item = InventoryItem::findOrFail($request->item_id);

            // Check stock availability for outbound transactions
            if (in_array($request->transaction_type, ['out', 'transfer']) && 
                $item->current_stock < $request->quantity) {
                return back()->withInput()
                    ->with('error', 'Insufficient stock. Available: ' . $item->current_stock);
            }

            $transaction = InventoryTransaction::create([
                'item_id' => $request->item_id,
                'transaction_type' => $request->transaction_type,
                'quantity' => $request->quantity,
                'transaction_date' => $request->transaction_date,
                'reference_number' => $request->reference_number,
                'notes' => $request->notes,
                'destination_location' => $request->destination_location,
                'user_id' => auth()->id()
            ]);

            // Update item stock
            switch ($request->transaction_type) {
                case 'in':
                    $item->increment('current_stock', $request->quantity);
                    break;
                case 'out':
                    $item->decrement('current_stock', $request->quantity);
                    break;
                case 'transfer':
                    $item->decrement('current_stock', $request->quantity);
                    break;
                case 'adjustment':
                    $item->update(['current_stock' => $request->quantity]);
                    break;
            }

            DB::commit();

            return redirect()->route('admin.inventory.transactions.index')
                ->with('success', 'Transaction created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->with('error', 'Failed to create transaction: ' . $e->getMessage());
        }
    }

    public function show(InventoryTransaction $transaction)
    {
        $transaction->load(['item', 'user']);
        
        return view('admin.inventory.transactions.show', compact('transaction'));
    }

    public function destroy(InventoryTransaction $transaction)
    {
        try {
            DB::beginTransaction();

            $item = $transaction->item;

            // Reverse the stock change
            switch ($transaction->transaction_type) {
                case 'in':
                    $item->decrement('current_stock', $transaction->quantity);
                    break;
                case 'out':
                    $item->increment('current_stock', $transaction->quantity);
                    break;
                case 'transfer':
                    $item->increment('current_stock', $transaction->quantity);
                    break;
                case 'adjustment':
                    // For adjustments, we can't easily reverse without knowing the previous value
                    // So we'll just delete the transaction without stock adjustment
                    break;
            }

            $transaction->delete();

            DB::commit();

            return redirect()->route('admin.inventory.transactions.index')
                ->with('success', 'Transaction deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->with('error', 'Failed to delete transaction: ' . $e->getMessage());
        }
    }
}
