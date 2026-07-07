<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PettyCash;
use App\Models\ShipmentFile;
use Illuminate\Http\Request;

class PettyCashController extends Controller
{
    public function index(Request $request)
    {
        $query = PettyCash::with(['shipmentFile', 'recordedBy']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('description', 'like', "%{$search}%");
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $entries = $query->latest()->paginate(15);

        $balance = PettyCash::where('type', 'in')->sum('amount') - PettyCash::where('type', 'out')->sum('amount');

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.finance.petty-cash.partials.table', compact('entries'))->render(),
            ]);
        }

        return view('admin.finance.petty-cash.index', compact('entries', 'balance'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:in,out',
            'shipment_file_id' => 'nullable|exists:shipment_files,id',
        ]);

        $validated['recorded_by'] = auth()->id();
        PettyCash::create($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Petty cash entry created']);
        }

        return back()->with('status', 'Petty cash entry created');
    }

    public function destroy(Request $request, PettyCash $pettyCash)
    {
        $pettyCash->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Entry deleted']);
        }

        return back()->with('status', 'Entry deleted');
    }
}
