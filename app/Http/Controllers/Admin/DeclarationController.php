<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Declaration;
use App\Models\ShipmentFile;
use App\Models\HsCode;
use App\Models\DeclarationItem;
use App\Services\MilestoneService;
use Illuminate\Http\Request;

class DeclarationController extends Controller
{
    public function index(Request $request)
    {
        $query = Declaration::with(['shipmentFile.client', 'officer']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('tansad_no', 'like', "%{$search}%")
                  ->orWhereHas('shipmentFile', fn($fq) => $fq->where('file_no', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $declarations = $query->latest()->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.declarations.partials.table', compact('declarations'))->render(),
            ]);
        }

        return view('admin.declarations.index', compact('declarations'));
    }

    public function create()
    {
        $files = ShipmentFile::whereIn('status', ['open', 'declaration'])->doesntHave('declaration')->with('client')->latest()->get();
        $hsCodes = HsCode::orderBy('code')->get();
        return view('admin.declarations.create', compact('files', 'hsCodes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'shipment_file_id' => 'required|exists:shipment_files,id',
            'tansad_no' => 'nullable|string|max:255',
            'regime_code' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.hs_code_id' => 'nullable|exists:hs_codes,id',
            'items.*.description' => 'nullable|string',
            'items.*.qty' => 'nullable|numeric|min:0',
            'items.*.customs_value' => 'nullable|numeric|min:0',
            'items.*.duty_rate' => 'nullable|numeric|min:0',
        ]);

        $declaration = Declaration::create([
            'shipment_file_id' => $validated['shipment_file_id'],
            'tansad_no' => $validated['tansad_no'] ?? null,
            'regime_code' => $validated['regime_code'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => 'draft',
            'officer_id' => auth()->id(),
        ]);

        if (!empty($validated['items'])) {
            foreach ($validated['items'] as $item) {
                $computedDuty = ($item['customs_value'] ?? 0) * ($item['duty_rate'] ?? 0) / 100;
                DeclarationItem::create([
                    'declaration_id' => $declaration->id,
                    'hs_code_id' => $item['hs_code_id'] ?? null,
                    'description' => $item['description'] ?? null,
                    'qty' => $item['qty'] ?? 0,
                    'customs_value' => $item['customs_value'] ?? 0,
                    'duty_rate' => $item['duty_rate'] ?? 0,
                    'computed_duty' => $computedDuty,
                ]);
            }
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Declaration created', 'redirect' => route('admin.declarations.show', $declaration)]);
        }

        return redirect()->route('admin.declarations.show', $declaration)->with('status', 'Declaration created');
    }

    public function show(Declaration $declaration)
    {
        $declaration->load(['shipmentFile.client', 'officer', 'items.hsCode', 'queries']);
        return view('admin.declarations.show', compact('declaration'));
    }

    public function edit(Declaration $declaration)
    {
        $hsCodes = HsCode::orderBy('code')->get();
        $declaration->load(['items.hsCode', 'shipmentFile']);
        return view('admin.declarations.edit', compact('declaration', 'hsCodes'));
    }

    public function update(Request $request, Declaration $declaration)
    {
        $validated = $request->validate([
            'tansad_no' => 'nullable|string|max:255',
            'regime_code' => 'nullable|string|max:50',
            'status' => 'nullable|in:draft,lodged,queried,assessed,paid,released',
            'duty_amount' => 'nullable|numeric|min:0',
            'vat_amount' => 'nullable|numeric|min:0',
            'other_taxes' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if (isset($validated['status'])) {
            if ($validated['status'] === 'lodged' && !$declaration->declared_at) {
                $validated['declared_at'] = now();
                MilestoneService::record($declaration->shipmentFile, 'declaration_lodged', auth()->id());
            }
            if ($validated['status'] === 'assessed' && !$declaration->assessed_at) {
                $validated['assessed_at'] = now();
            }
            if ($validated['status'] === 'paid') {
                MilestoneService::record($declaration->shipmentFile, 'taxes_paid', auth()->id());
            }
            if ($validated['status'] === 'released') {
                MilestoneService::record($declaration->shipmentFile, 'customs_released', auth()->id());
            }
        }

        $validated['total_taxes'] = ($validated['duty_amount'] ?? $declaration->duty_amount)
            + ($validated['vat_amount'] ?? $declaration->vat_amount)
            + ($validated['other_taxes'] ?? $declaration->other_taxes);

        $declaration->update($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Declaration updated']);
        }

        return redirect()->route('admin.declarations.show', $declaration)->with('status', 'Declaration updated');
    }

    public function destroy(Request $request, Declaration $declaration)
    {
        $declaration->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Declaration deleted']);
        }

        return redirect()->route('admin.declarations.index')->with('status', 'Declaration deleted');
    }

    public function addItem(Request $request, Declaration $declaration)
    {
        $validated = $request->validate([
            'hs_code_id' => 'nullable|exists:hs_codes,id',
            'description' => 'nullable|string',
            'qty' => 'required|numeric|min:0',
            'customs_value' => 'required|numeric|min:0',
            'duty_rate' => 'nullable|numeric|min:0',
        ]);

        $computedDuty = $validated['customs_value'] * ($validated['duty_rate'] ?? 0) / 100;

        $item = DeclarationItem::create([
            'declaration_id' => $declaration->id,
            'hs_code_id' => $validated['hs_code_id'] ?? null,
            'description' => $validated['description'] ?? null,
            'qty' => $validated['qty'],
            'customs_value' => $validated['customs_value'],
            'duty_rate' => $validated['duty_rate'] ?? 0,
            'computed_duty' => $computedDuty,
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Item added', 'item' => $item]);
        }

        return back()->with('status', 'Item added');
    }

    public function destroyItem(Request $request, Declaration $declaration, DeclarationItem $item)
    {
        $item->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Item removed']);
        }

        return back()->with('status', 'Item removed');
    }

    public function respondToQuery(Request $request, Declaration $declaration)
    {
        $validated = $request->validate([
            'query_text' => 'required|string',
            'response_text' => 'nullable|string',
        ]);

        $query = $declaration->queries()->create([
            'query_text' => $validated['query_text'],
            'response_text' => $validated['response_text'] ?? null,
            'raised_at' => now(),
            'status' => $validated['response_text'] ? 'responded' : 'open',
            'responded_at' => $validated['response_text'] ? now() : null,
        ]);

        if ($declaration->status === 'lodged') {
            $declaration->update(['status' => 'queried']);
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Query recorded', 'query' => $query]);
        }

        return back()->with('status', 'Query recorded');
    }
}
