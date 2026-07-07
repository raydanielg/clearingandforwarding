<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShipmentFile;
use App\Models\Client;
use App\Models\ShippingLine;
use App\Models\Terminal;
use App\Models\User;
use App\Services\FileNumberService;
use App\Services\MilestoneService;
use Illuminate\Http\Request;

class ShipmentFileController extends Controller
{
    public function index(Request $request)
    {
        $query = ShipmentFile::with(['client', 'declarationOfficer', 'clearingOfficer']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('file_no', 'like', "%{$search}%")
                  ->orWhere('bl_awb_no', 'like', "%{$search}%")
                  ->orWhereHas('client', fn($cq) => $cq->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $files = $query->latest()->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.files.partials.table', compact('files'))->render(),
            ]);
        }

        return view('admin.files.index', compact('files'));
    }

    public function create()
    {
        $clients = Client::where('is_active', true)->orderBy('name')->get();
        $shippingLines = ShippingLine::where('is_active', true)->orderBy('name')->get();
        $terminals = Terminal::where('is_active', true)->orderBy('name')->get();
        $officers = User::where('is_active', true)->orderBy('name')->get();

        return view('admin.files.create', compact('clients', 'shippingLines', 'terminals', 'officers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'type' => 'required|in:import,export,transit',
            'mode' => 'required|in:sea,air,road',
            'bl_awb_no' => 'nullable|string|max:255',
            'vessel_flight' => 'nullable|string|max:255',
            'shipping_line_id' => 'nullable|exists:shipping_lines,id',
            'port_of_loading' => 'nullable|string|max:255',
            'port_of_discharge' => 'nullable|string|max:255',
            'terminal_id' => 'nullable|exists:terminals,id',
            'eta' => 'nullable|date',
            'discharge_date' => 'nullable|date',
            'free_days_expiry' => 'nullable|date',
            'currency' => 'nullable|string|size:3',
            'exchange_rate' => 'nullable|numeric|min:0',
            'assigned_declaration_officer_id' => 'nullable|exists:users,id',
            'assigned_clearing_officer_id' => 'nullable|exists:users,id',
            'remarks' => 'nullable|string',
        ]);

        $validated['file_no'] = FileNumberService::generate($validated['type']);
        $validated['status'] = 'open';
        $validated['opened_by'] = auth()->id();
        $validated['opened_at'] = now();

        $file = ShipmentFile::create($validated);

        MilestoneService::record($file, 'file_opened', auth()->id(), 'File created');

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'File created: ' . $file->file_no, 'redirect' => route('admin.files.show', $file)]);
        }

        return redirect()->route('admin.files.show', $file)->with('status', 'File created: ' . $file->file_no);
    }

    public function show(ShipmentFile $file)
    {
        $file->load([
            'client', 'shippingLine', 'terminal', 'declarationOfficer', 'clearingOfficer', 'openedBy',
            'consignments.containers', 'declaration.items.hsCode', 'declaration.queries',
            'documents.documentType', 'documents.uploadedBy',
            'milestones.recordedBy', 'deliveryOrders.shippingLine',
            'trips.truck', 'trips.driver', 'trips.container',
            'charges.chargeItem', 'invoices.items', 'disbursements.agency',
            'insurancePolicies.insurer', 'insurancePolicies.claims', 'insuranceClaims.policy'
        ]);

        return view('admin.files.show', compact('file'));
    }

    public function edit(ShipmentFile $file)
    {
        $clients = Client::where('is_active', true)->orderBy('name')->get();
        $shippingLines = ShippingLine::where('is_active', true)->orderBy('name')->get();
        $terminals = Terminal::where('is_active', true)->orderBy('name')->get();
        $officers = User::where('is_active', true)->orderBy('name')->get();

        return view('admin.files.edit', compact('file', 'clients', 'shippingLines', 'terminals', 'officers'));
    }

    public function update(Request $request, ShipmentFile $file)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'type' => 'required|in:import,export,transit',
            'mode' => 'required|in:sea,air,road',
            'bl_awb_no' => 'nullable|string|max:255',
            'vessel_flight' => 'nullable|string|max:255',
            'shipping_line_id' => 'nullable|exists:shipping_lines,id',
            'port_of_loading' => 'nullable|string|max:255',
            'port_of_discharge' => 'nullable|string|max:255',
            'terminal_id' => 'nullable|exists:terminals,id',
            'eta' => 'nullable|date',
            'discharge_date' => 'nullable|date',
            'free_days_expiry' => 'nullable|date',
            'currency' => 'nullable|string|size:3',
            'exchange_rate' => 'nullable|numeric|min:0',
            'assigned_declaration_officer_id' => 'nullable|exists:users,id',
            'assigned_clearing_officer_id' => 'nullable|exists:users,id',
            'remarks' => 'nullable|string',
        ]);

        $file->update($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'File updated successfully']);
        }

        return redirect()->route('admin.files.show', $file)->with('status', 'File updated');
    }

    public function destroy(Request $request, ShipmentFile $file)
    {
        $file->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'File deleted successfully']);
        }

        return redirect()->route('admin.files.index')->with('status', 'File deleted');
    }

    public function assignOfficers(Request $request, ShipmentFile $file)
    {
        $validated = $request->validate([
            'assigned_declaration_officer_id' => 'nullable|exists:users,id',
            'assigned_clearing_officer_id' => 'nullable|exists:users,id',
        ]);

        $file->update($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Officers assigned successfully']);
        }

        return back()->with('status', 'Officers assigned');
    }

    public function closeFile(Request $request, ShipmentFile $file)
    {
        if ($file->status === 'closed') {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'File already closed'], 400);
            }
            return back()->with('error', 'File already closed');
        }

        MilestoneService::record($file, 'file_closed', auth()->id(), $request->remarks);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'File closed successfully']);
        }

        return back()->with('status', 'File closed');
    }

    public function addMilestone(Request $request, ShipmentFile $file)
    {
        $validated = $request->validate([
            'stage' => 'required|in:file_opened,docs_complete,declaration_lodged,taxes_paid,customs_released,do_collected,port_charges_paid,verification,gate_out,insurance_arranged,in_transit,delivered,empty_returned,file_closed',
            'remarks' => 'nullable|string',
        ]);

        $milestone = MilestoneService::record($file, $validated['stage'], auth()->id(), $validated['remarks'] ?? null);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Milestone recorded', 'milestone' => $milestone]);
        }

        return back()->with('status', 'Milestone recorded');
    }
}
