<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChargeItem;
use Illuminate\Http\Request;

class ChargeItemController extends Controller
{
    public function index(Request $request)
    {
        $query = ChargeItem::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $chargeItems = $query->latest()->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.finance.charges.partials.table', compact('chargeItems'))->render(),
            ]);
        }

        return view('admin.finance.charges.index', compact('chargeItems'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'default_price' => 'nullable|numeric|min:0',
            'vat_rate' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $charge = ChargeItem::create($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Charge item created', 'charge' => $charge]);
        }

        return back()->with('status', 'Charge item created');
    }

    public function update(Request $request, ChargeItem $charge)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'default_price' => 'nullable|numeric|min:0',
            'vat_rate' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $charge->update($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Charge item updated']);
        }

        return back()->with('status', 'Charge item updated');
    }

    public function destroy(Request $request, ChargeItem $charge)
    {
        $charge->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Charge item deleted']);
        }

        return back()->with('status', 'Charge item deleted');
    }
}
