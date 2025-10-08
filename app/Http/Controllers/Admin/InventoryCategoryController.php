<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = InventoryCategory::withCount('items');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $categories = $query->ordered()->paginate(20);

        return view('admin.inventory.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.inventory.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:inventory_categories,name',
            'code' => 'nullable|string|max:50|unique:inventory_categories,code',
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:20',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        try {
            DB::beginTransaction();

            $category = InventoryCategory::create([
                'name' => $request->name,
                'code' => $request->code ?? InventoryCategory::make()->generateCode(),
                'description' => $request->description,
                'icon' => $request->icon,
                'color' => $request->color,
                'sort_order' => $request->sort_order ?? 0,
                'is_active' => true
            ]);

            DB::commit();

            return redirect()->route('admin.inventory.categories.index')
                ->with('success', 'Category created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->with('error', 'Failed to create category: ' . $e->getMessage());
        }
    }

    public function show(InventoryCategory $category)
    {
        $category->load(['items' => function($query) {
            $query->with(['supplier'])->orderBy('name');
        }]);

        return view('admin.inventory.categories.show', compact('category'));
    }

    public function edit(InventoryCategory $category)
    {
        return view('admin.inventory.categories.edit', compact('category'));
    }

    public function update(Request $request, InventoryCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:inventory_categories,name,' . $category->id,
            'code' => 'nullable|string|max:50|unique:inventory_categories,code,' . $category->id,
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:20',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $category->update($request->only([
            'name', 'code', 'description', 'icon', 'color', 'sort_order', 'is_active'
        ]));

        return redirect()->route('admin.inventory.categories.show', $category)
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(InventoryCategory $category)
    {
        if (!$category->canBeDeleted()) {
            return back()->with('error', 'Cannot delete category with existing items.');
        }

        $category->delete();

        return redirect()->route('admin.inventory.categories.index')
            ->with('success', 'Category deleted successfully.');
    }

    public function toggleStatus(InventoryCategory $category)
    {
        if ($category->is_active) {
            $category->deactivate();
            $message = 'Category deactivated successfully.';
        } else {
            $category->activate();
            $message = 'Category activated successfully.';
        }

        return back()->with('success', $message);
    }
}
