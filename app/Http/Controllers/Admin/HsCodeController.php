<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HsCode;
use Illuminate\Http\Request;

class HsCodeController extends Controller
{
    public function index(Request $request)
    {
        $query = HsCode::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $hsCodes = $query->latest()->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.declarations.hs-codes.partials.table', compact('hsCodes'))->render(),
            ]);
        }

        return view('admin.declarations.hs-codes.index', compact('hsCodes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:hs_codes,code',
            'description' => 'required|string',
            'duty_rate' => 'nullable|numeric|min:0|max:100',
            'vat_rate' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        $hsCode = HsCode::create($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'HS Code created', 'hsCode' => $hsCode]);
        }

        return back()->with('status', 'HS Code created');
    }

    public function update(Request $request, HsCode $hsCode)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:hs_codes,code,' . $hsCode->id,
            'description' => 'required|string',
            'duty_rate' => 'nullable|numeric|min:0|max:100',
            'vat_rate' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        $hsCode->update($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'HS Code updated']);
        }

        return back()->with('status', 'HS Code updated');
    }

    public function destroy(Request $request, HsCode $hsCode)
    {
        $hsCode->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'HS Code deleted']);
        }

        return back()->with('status', 'HS Code deleted');
    }
}
