<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Disbursement;
use App\Models\ShipmentFile;
use App\Models\Agency;
use Illuminate\Http\Request;

class DisbursementController extends Controller
{
    public function index(Request $request)
    {
        $query = Disbursement::with(['shipmentFile.client', 'agency', 'recordedBy']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('payee', 'like', "%{$search}%")
                  ->orWhere('reference_no', 'like', "%{$search}%")
                  ->orWhereHas('shipmentFile', fn($fq) => $fq->where('file_no', 'like', "%{$search}%"));
            });
        }

        $disbursements = $query->latest()->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.finance.disbursements.partials.table', compact('disbursements'))->render(),
            ]);
        }

        return view('admin.finance.disbursements.index', compact('disbursements'));
    }

    public function create()
    {
        $files = ShipmentFile::with('client')->latest()->limit(50)->get();
        $agencies = Agency::where('is_active', true)->orderBy('name')->get();
        return view('admin.finance.disbursements.create', compact('files', 'agencies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'shipment_file_id' => 'required|exists:shipment_files,id',
            'agency_id' => 'nullable|exists:agencies,id',
            'payee' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'paid_at' => 'nullable|date',
            'method' => 'required|in:cash,bank,mobile_money,cheque',
            'reference_no' => 'nullable|string|max:255',
        ]);

        $validated['recorded_by'] = auth()->id();
        $validated['paid_at'] = $validated['paid_at'] ?? now();

        $disbursement = Disbursement::create($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Disbursement recorded', 'redirect' => route('admin.disbursements.show', $disbursement)]);
        }

        return redirect()->route('admin.disbursements.show', $disbursement)->with('status', 'Disbursement recorded');
    }

    public function show(Disbursement $disbursement)
    {
        $disbursement->load(['shipmentFile.client', 'agency', 'recordedBy', 'approvedBy']);
        return view('admin.finance.disbursements.show', compact('disbursement'));
    }

    public function edit(Disbursement $disbursement)
    {
        $files = ShipmentFile::with('client')->latest()->limit(50)->get();
        $agencies = Agency::where('is_active', true)->orderBy('name')->get();
        return view('admin.finance.disbursements.edit', compact('disbursement', 'files', 'agencies'));
    }

    public function update(Request $request, Disbursement $disbursement)
    {
        $validated = $request->validate([
            'agency_id' => 'nullable|exists:agencies,id',
            'payee' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'paid_at' => 'nullable|date',
            'method' => 'required|in:cash,bank,mobile_money,cheque',
            'reference_no' => 'nullable|string|max:255',
        ]);

        $disbursement->update($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Disbursement updated']);
        }

        return redirect()->route('admin.disbursements.show', $disbursement)->with('status', 'Disbursement updated');
    }

    public function approve(Request $request, Disbursement $disbursement)
    {
        $disbursement->update(['approved_by' => auth()->id()]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Disbursement approved']);
        }

        return back()->with('status', 'Disbursement approved');
    }

    public function destroy(Request $request, Disbursement $disbursement)
    {
        $disbursement->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Disbursement deleted']);
        }

        return redirect()->route('admin.disbursements.index')->with('status', 'Disbursement deleted');
    }
}
