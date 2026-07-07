<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\ShipmentFile;
use App\Models\ChargeItem;
use App\Services\InvoiceService;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['client', 'shipmentFile', 'items']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                  ->orWhereHas('client', fn($cq) => $cq->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('kind')) {
            $query->where('kind', $request->kind);
        }

        $invoices = $query->latest()->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.finance.invoices.partials.table', compact('invoices'))->render(),
            ]);
        }

        return view('admin.finance.invoices.index', compact('invoices'));
    }

    public function create()
    {
        $clients = Client::where('is_active', true)->orderBy('name')->get();
        $files = ShipmentFile::whereIn('status', ['delivered', 'invoiced', 'closed'])->with('client')->latest()->limit(50)->get();
        $chargeItems = ChargeItem::orderBy('name')->get();
        return view('admin.finance.invoices.create', compact('clients', 'files', 'chargeItems'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'shipment_file_id' => 'nullable|exists:shipment_files,id',
            'date' => 'required|date',
            'due_date' => 'nullable|date',
            'currency' => 'nullable|string|size:3',
            'kind' => 'nullable|in:proforma,invoice',
            'items' => 'required|array|min:1',
            'items.*.charge_item_id' => 'nullable|exists:charge_items,id',
            'items.*.description' => 'nullable|string',
            'items.*.qty' => 'required|numeric|min:0',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.vat_rate' => 'nullable|numeric|min:0',
        ]);

        $file = isset($validated['shipment_file_id']) ? ShipmentFile::find($validated['shipment_file_id']) : null;

        $invoice = InvoiceService::createFromCharges(
            $file ?? ShipmentFile::find($validated['shipment_file_id']) ?? new ShipmentFile(),
            $validated,
            auth()->id(),
            $validated['kind'] ?? 'invoice'
        );

        // If no shipment file, set client directly
        if (!$file) {
            $invoice->update(['shipment_file_id' => null, 'client_id' => $validated['client_id']]);
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Invoice created: ' . $invoice->invoice_no, 'redirect' => route('admin.invoices.show', $invoice)]);
        }

        return redirect()->route('admin.invoices.show', $invoice)->with('status', 'Invoice created');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['client', 'shipmentFile', 'items.chargeItem', 'receipts', 'createdBy']);
        return view('admin.finance.invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return back()->with('error', 'Paid invoices cannot be edited');
        }

        $clients = Client::where('is_active', true)->orderBy('name')->get();
        $chargeItems = ChargeItem::orderBy('name')->get();
        $invoice->load(['items.chargeItem']);
        return view('admin.finance.invoices.edit', compact('invoice', 'clients', 'chargeItems'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        if ($invoice->status === 'paid') {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Paid invoices cannot be edited'], 400);
            }
            return back()->with('error', 'Paid invoices cannot be edited');
        }

        $validated = $request->validate([
            'date' => 'required|date',
            'due_date' => 'nullable|date',
            'status' => 'nullable|in:draft,sent,partially_paid,paid,overdue,cancelled',
            'items' => 'required|array|min:1',
            'items.*.id' => 'nullable|exists:invoice_items,id',
            'items.*.charge_item_id' => 'nullable|exists:charge_items,id',
            'items.*.description' => 'nullable|string',
            'items.*.qty' => 'required|numeric|min:0',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.vat_rate' => 'nullable|numeric|min:0',
        ]);

        // Recalculate items
        $subtotal = 0;
        $vatTotal = 0;

        $existingIds = collect($validated['items'])->pluck('id')->filter()->toArray();
        $invoice->items()->whereNotIn('id', $existingIds)->delete();

        foreach ($validated['items'] as $itemData) {
            $lineTotal = $itemData['qty'] * $itemData['unit_price'];
            $vat = $lineTotal * (($itemData['vat_rate'] ?? 0) / 100);
            $subtotal += $lineTotal;
            $vatTotal += $vat;

            if (isset($itemData['id'])) {
                $invoice->items()->where('id', $itemData['id'])->update([
                    'charge_item_id' => $itemData['charge_item_id'] ?? null,
                    'description' => $itemData['description'] ?? null,
                    'qty' => $itemData['qty'],
                    'unit_price' => $itemData['unit_price'],
                    'vat_rate' => $itemData['vat_rate'] ?? 0,
                    'line_total' => $lineTotal,
                ]);
            } else {
                $invoice->items()->create([
                    'charge_item_id' => $itemData['charge_item_id'] ?? null,
                    'description' => $itemData['description'] ?? null,
                    'qty' => $itemData['qty'],
                    'unit_price' => $itemData['unit_price'],
                    'vat_rate' => $itemData['vat_rate'] ?? 0,
                    'line_total' => $lineTotal,
                ]);
            }
        }

        $invoice->update([
            'date' => $validated['date'],
            'due_date' => $validated['due_date'] ?? null,
            'subtotal' => $subtotal,
            'vat_amount' => $vatTotal,
            'total' => $subtotal + $vatTotal,
            'status' => $validated['status'] ?? $invoice->status,
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Invoice updated']);
        }

        return redirect()->route('admin.invoices.show', $invoice)->with('status', 'Invoice updated');
    }

    public function destroy(Request $request, Invoice $invoice)
    {
        if ($invoice->status === 'paid') {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Paid invoices cannot be deleted'], 400);
            }
            return back()->with('error', 'Paid invoices cannot be deleted');
        }

        $invoice->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Invoice deleted']);
        }

        return redirect()->route('admin.invoices.index')->with('status', 'Invoice deleted');
    }

    public function recordPayment(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:cash,bank,mobile_money,cheque',
            'date' => 'required|date',
            'reference' => 'nullable|string|max:255',
        ]);

        $receipt = InvoiceService::recordPayment($invoice, $validated, auth()->id());

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Payment recorded: ' . $receipt->receipt_no, 'receipt' => $receipt]);
        }

        return back()->with('status', 'Payment recorded: ' . $receipt->receipt_no);
    }

    public function convertProforma(Request $request, Invoice $invoice)
    {
        if ($invoice->kind !== 'proforma') {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Not a proforma'], 400);
            }
            return back()->with('error', 'Not a proforma');
        }

        $newInvoice = InvoiceService::convertProformaToInvoice($invoice);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Converted to invoice: ' . $newInvoice->invoice_no, 'redirect' => route('admin.invoices.show', $newInvoice)]);
        }

        return redirect()->route('admin.invoices.show', $newInvoice)->with('status', 'Converted to invoice');
    }
}
