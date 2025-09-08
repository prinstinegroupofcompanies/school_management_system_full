<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\FeeItem;
use Illuminate\Http\Request;

class FeeItemController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'finance']);
    }

    public function index(Request $request)
    {
        $query = FeeItem::query();

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }
        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }
        if ($request->filled('active')) {
            $query->where('is_active', (bool) $request->boolean('active'));
        }

        $feeItems = $query->latest()->paginate(20);
        $classes = ClassRoom::all();

        return view('finance.fee_items.index', compact('feeItems', 'classes'));
    }

    public function create()
    {
        $classes = ClassRoom::all();
        $currentYear = (int) date('Y');
        return view('finance.fee_items.create', compact('classes', 'currentYear'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'item_name' => 'required|string|max:255',
            'quantity' => 'nullable|integer|min:1',
            'price_per_unit' => 'nullable|numeric|min:0',
            'total' => 'nullable|numeric|min:0',
            'class_id' => 'nullable|exists:class_rooms,id',
            'semester' => 'nullable|string|max:32',
            'year' => 'nullable|integer|min:2000|max:2100',
            'is_active' => 'nullable|boolean',
        ]);

        $quantity = $data['quantity'] ?? 1;
        $price = $data['price_per_unit'] ?? 0;
        $total = $data['total'] ?? ($quantity * $price);

        FeeItem::create([
            'item_name' => $data['item_name'],
            'quantity' => $quantity,
            'price_per_unit' => $price,
            'total' => $total,
            'class_id' => $data['class_id'] ?? null,
            'semester' => $data['semester'] ?? null,
            'year' => $data['year'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);

        return redirect()->route('finance.fee-items.index')->with('success', 'Fee item created');
    }

    public function edit(FeeItem $feeItem)
    {
        $classes = ClassRoom::all();
        return view('finance.fee_items.edit', compact('feeItem', 'classes'));
    }

    public function show(FeeItem $feeItem)
    {
        $feeItem->load('classRoom');
        return view('finance.fee_items.show', compact('feeItem'));
    }

    public function update(Request $request, FeeItem $feeItem)
    {
        $data = $request->validate([
            'item_name' => 'required|string|max:255',
            'quantity' => 'nullable|integer|min:1',
            'price_per_unit' => 'nullable|numeric|min:0',
            'total' => 'nullable|numeric|min:0',
            'class_id' => 'nullable|exists:class_rooms,id',
            'semester' => 'nullable|string|max:32',
            'year' => 'nullable|integer|min:2000|max:2100',
            'is_active' => 'nullable|boolean',
        ]);

        $quantity = $data['quantity'] ?? $feeItem->quantity;
        $price = $data['price_per_unit'] ?? $feeItem->price_per_unit;
        $total = $data['total'] ?? ($quantity * $price);

        $feeItem->update([
            'item_name' => $data['item_name'],
            'quantity' => $quantity,
            'price_per_unit' => $price,
            'total' => $total,
            'class_id' => $data['class_id'] ?? null,
            'semester' => $data['semester'] ?? null,
            'year' => $data['year'] ?? null,
            'is_active' => $data['is_active'] ?? $feeItem->is_active,
        ]);

        return redirect()->route('finance.fee-items.index')->with('success', 'Fee item updated');
    }

    public function destroy(FeeItem $feeItem)
    {
        $feeItem->delete();
        return redirect()->route('finance.fee-items.index')->with('success', 'Fee item deleted');
    }
}


