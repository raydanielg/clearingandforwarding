<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Receipt;
use App\Models\Invoice;
use App\Models\Client;
use App\Services\FileNumberService;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    public function index(Request $request)
    {
        $query = Receipt::with(['client', 'invoice', 'receivedBy']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('receipt_no', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%")
                  ->orWhereHas('client', fn($cq) => $cq->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        $receipts = $query->latest()->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.finance.receipts.partials.table', compact('receipts'))->render(),
            ]);
        }

        return view('admin.finance.receipts.index', compact('receipts'));
    }

    public function create()
    {
        $clients = Client::where('is_active', true)->orderBy('name')->get();
        $invoices = Invoice::whereIn('status', ['sent', 'partially_paid', 'overdue'])->with('client')->latest()->get();
        return view('admin.finance.receipts.create', compact('clients', 'invoices'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'nullable|exists:invoices,id',
            'client_id' => 'required|exists:clients,id',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:cash,bank,mobile_money,cheque',
            'reference' => 'nullable|string|max:255',
        ]);

        $receipt = Receipt::create([
            'receipt_no' => FileNumberService::generateReceiptNumber(),
            'invoice_id' => $validated['invoice_id'] ?? null,
            'client_id' => $validated['client_id'],
            'date' => $validated['date'],
            'amount' => $validated['amount'],
            'method' => $validated['method'],
            'reference' => $validated['reference'] ?? null,
            'received_by' => auth()->id(),
        ]);

        if ($validated['invoice_id']) {
            $invoice = Invoice::find($validated['invoice_id']);
            $newPaid = $invoice->amount_paid + $receipt->amount;
            if ($newPaid >= $invoice->total) {
                $invoice->update(['amount_paid' => $newPaid, 'status' => 'paid']);
            } else {
                $invoice->update(['amount_paid' => $newPaid, 'status' => 'partially_paid']);
            }
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Receipt created: ' . $receipt->receipt_no, 'redirect' => route('admin.receipts.show', $receipt)]);
        }

        return redirect()->route('admin.receipts.show', $receipt)->with('status', 'Receipt created');
    }

    public function show(Receipt $receipt)
    {
        $receipt->load(['client', 'invoice', 'receivedBy']);
        return view('admin.finance.receipts.show', compact('receipt'));
    }

    public function destroy(Request $request, Receipt $receipt)
    {
        if ($receipt->invoice_id) {
            $invoice = $receipt->invoice;
            $invoice->update(['amount_paid' => max(0, $invoice->amount_paid - $receipt->amount)]);
            if ($invoice->amount_paid <= 0) {
                $invoice->update(['status' => 'sent']);
            } elseif ($invoice->amount_paid < $invoice->total) {
                $invoice->update(['status' => 'partially_paid']);
            }
        }

        $receipt->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Receipt voided']);
        }

        return redirect()->route('admin.receipts.index')->with('status', 'Receipt voided');
    }
}
