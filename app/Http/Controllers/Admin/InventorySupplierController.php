<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventorySupplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventorySupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = InventorySupplier::withCount(['items', 'transactions']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $suppliers = $query->orderBy('name')->paginate(20);

        return view('admin.inventory.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('admin.inventory.suppliers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:inventory_suppliers,name',
            'code' => 'nullable|string|max:50|unique:inventory_suppliers,code',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'notes' => 'nullable|string|max:1000',
            'credit_limit' => 'nullable|numeric|min:0',
            'payment_terms_days' => 'nullable|integer|min:1|max:365',
            'tax_id' => 'nullable|string|max:50'
        ]);

        try {
            DB::beginTransaction();

            $supplier = InventorySupplier::create([
                'name' => $request->name,
                'code' => $request->code ?? InventorySupplier::make()->generateCode(),
                'contact_person' => $request->contact_person,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country ?? 'Liberia',
                'postal_code' => $request->postal_code,
                'website' => $request->website,
                'notes' => $request->notes,
                'credit_limit' => $request->credit_limit,
                'payment_terms_days' => $request->payment_terms_days ?? 30,
                'tax_id' => $request->tax_id,
                'status' => 'active'
            ]);

            DB::commit();

            return redirect()->route('admin.inventory.suppliers.index')
                ->with('success', 'Supplier created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->with('error', 'Failed to create supplier: ' . $e->getMessage());
        }
    }

    public function show(InventorySupplier $supplier)
    {
        $supplier->load([
            'items' => function($query) {
                $query->with(['category'])->orderBy('name');
            },
            'transactions' => function($query) {
                $query->with(['item', 'createdBy'])->orderBy('created_at', 'desc')->limit(10);
            }
        ]);

        return view('admin.inventory.suppliers.show', compact('supplier'));
    }

    public function edit(InventorySupplier $supplier)
    {
        return view('admin.inventory.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, InventorySupplier $supplier)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:inventory_suppliers,name,' . $supplier->id,
            'code' => 'nullable|string|max:50|unique:inventory_suppliers,code,' . $supplier->id,
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'notes' => 'nullable|string|max:1000',
            'credit_limit' => 'nullable|numeric|min:0',
            'payment_terms_days' => 'nullable|integer|min:1|max:365',
            'tax_id' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive,suspended'
        ]);

        $supplier->update($request->only([
            'name', 'code', 'contact_person', 'email', 'phone', 'address',
            'city', 'state', 'country', 'postal_code', 'website', 'notes',
            'credit_limit', 'payment_terms_days', 'tax_id', 'status'
        ]));

        return redirect()->route('admin.inventory.suppliers.show', $supplier)
            ->with('success', 'Supplier updated successfully.');
    }

    public function destroy(InventorySupplier $supplier)
    {
        if (!$supplier->canBeDeleted()) {
            return back()->with('error', 'Cannot delete supplier with existing items or transactions.');
        }

        $supplier->delete();

        return redirect()->route('admin.inventory.suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }

    public function toggleStatus(InventorySupplier $supplier)
    {
        if ($supplier->isActive()) {
            $supplier->deactivate();
            $message = 'Supplier deactivated successfully.';
        } else {
            $supplier->activate();
            $message = 'Supplier activated successfully.';
        }

        return back()->with('success', $message);
    }

    public function suspend(InventorySupplier $supplier)
    {
        $supplier->suspend();
        return back()->with('success', 'Supplier suspended successfully.');
    }
}
