<?php

namespace App\Http\Controllers;

use App\Models\ShipmentFile;
use App\Models\Document;
use App\Models\Invoice;
use App\Models\Receipt;
use App\Models\Milestone;
use App\Models\Client;
use App\Models\InsurancePolicy;
use Illuminate\Http\Request;

class PortalController extends Controller
{
    private function client(): Client
    {
        return Client::where('email', auth()->user()->email)->first()
            ?? Client::find(auth()->user()->client_id);
    }

    public function dashboard()
    {
        $client = $this->client();

        if (!$client) {
            return view('portal.no-client');
        }

        $files = ShipmentFile::where('client_id', $client->id)->latest()->limit(5)->get();
        $invoices = Invoice::where('client_id', $client->id)->latest()->limit(5)->get();

        $stats = [
            'active_files'   => ShipmentFile::where('client_id', $client->id)->whereNotIn('status', ['closed', 'cancelled'])->count(),
            'total_files'    => ShipmentFile::where('client_id', $client->id)->count(),
            'unpaid_invoices'=> Invoice::where('client_id', $client->id)->whereIn('status', ['sent', 'partially_paid', 'overdue'])->count(),
            'outstanding'    => Invoice::where('client_id', $client->id)->whereIn('status', ['sent', 'partially_paid', 'overdue'])->selectRaw('SUM(total - amount_paid) as outstanding')->value('outstanding') ?? 0,
        ];

        return view('portal.dashboard', compact('client', 'files', 'invoices', 'stats'));
    }

    public function shipmentsIndex(Request $request)
    {
        $client = $this->client();
        $query = ShipmentFile::where('client_id', $client->id)->with(['shippingLine', 'terminal']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('file_no', 'like', "%{$search}%")
                  ->orWhere('bl_awb_no', 'like', "%{$search}%");
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $files = $query->latest()->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('portal.shipments.partials.table', compact('files'))->render(),
            ]);
        }

        return view('portal.shipments.index', compact('files'));
    }

    public function shipmentsShow(ShipmentFile $file)
    {
        $file->load(['client', 'shippingLine', 'terminal', 'declarationOfficer', 'clearingOfficer', 'milestones.recordedBy', 'documents.documentType', 'consignments.containers', 'trips.truck', 'trips.driver']);

        return view('portal.shipments.show', compact('file'));
    }

    public function documentsIndex(Request $request)
    {
        $client = $this->client();
        $query = Document::whereHas('shipmentFile', fn($q) => $q->where('client_id', $client->id))
            ->with(['shipmentFile', 'documentType', 'uploadedBy']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_no', 'like', "%{$search}%")
                  ->orWhereHas('documentType', fn($dq) => $dq->where('name', 'like', "%{$search}%"));
            });
        }

        $documents = $query->latest()->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('portal.documents.partials.table', compact('documents'))->render(),
            ]);
        }

        return view('portal.documents.index', compact('documents'));
    }

    public function invoicesIndex(Request $request)
    {
        $client = $this->client();
        $query = Invoice::where('client_id', $client->id)->with(['shipmentFile']);

        if ($request->filled('search')) {
            $query->where('invoice_no', 'like', "%{$request->search}%");
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $invoices = $query->latest()->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('portal.invoices.partials.table', compact('invoices'))->render(),
            ]);
        }

        return view('portal.invoices.index', compact('invoices'));
    }

    public function invoicesShow(Invoice $invoice)
    {
        $invoice->load(['client', 'shipmentFile', 'items.chargeItem', 'receipts']);
        return view('portal.invoices.show', compact('invoice'));
    }

    public function statement()
    {
        $client = $this->client();
        $invoices = Invoice::where('client_id', $client->id)->with('receipts')->latest()->get();
        $receipts = Receipt::where('client_id', $client->id)->latest()->get();

        $openingBalance = 0;
        $transactions = collect();

        foreach ($invoices as $inv) {
            $transactions->push([
                'date'        => $inv->date,
                'description' => 'Invoice ' . $inv->invoice_no,
                'debit'       => $inv->total,
                'credit'      => 0,
                'type'        => 'invoice',
                'ref'         => $inv->invoice_no,
            ]);
        }

        foreach ($receipts as $rec) {
            $transactions->push([
                'date'        => $rec->date,
                'description' => 'Receipt ' . $rec->receipt_no,
                'debit'       => 0,
                'credit'      => $rec->amount,
                'type'        => 'receipt',
                'ref'         => $rec->receipt_no,
            ]);
        }

        $transactions = $transactions->sortByDesc('date')->values();

        $totalInvoiced = $invoices->sum('total');
        $totalPaid = $receipts->sum('amount');
        $balance = $totalInvoiced - $totalPaid;

        return view('portal.statement', compact('client', 'transactions', 'totalInvoiced', 'totalPaid', 'balance'));
    }

    public function track()
    {
        return view('portal.track');
    }

    public function trackSearch(Request $request)
    {
        $request->validate(['query' => 'required|string']);
        $client = $this->client();

        $file = ShipmentFile::where('client_id', $client->id)
            ->where(function ($q) use ($request) {
                $q->where('file_no', $request->query)
                  ->orWhere('bl_awb_no', $request->query);
            })->with(['milestones.recordedBy', 'shippingLine', 'terminal'])
            ->first();

        if (!$file) {
            return response()->json(['success' => false, 'message' => 'No shipment found with that reference.']);
        }

        return response()->json([
            'success' => true,
            'html' => view('portal.track-result', compact('file'))->render(),
        ]);
    }

    public function profile()
    {
        $client = $this->client();
        return view('portal.profile', compact('client'));
    }

    public function support()
    {
        $client = $this->client();
        return view('portal.support', compact('client'));
    }

    public function insuranceIndex(Request $request)
    {
        $client = $this->client();
        $query = InsurancePolicy::where('client_id', $client->id)->with(['insurer', 'shipmentFile']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('policy_no', 'like', "%{$search}%")
                  ->orWhereHas('shipmentFile', fn($fq) => $fq->where('file_no', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $policies = $query->latest()->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('portal.insurance.partials.table', compact('policies'))->render(),
            ]);
        }

        return view('portal.insurance.index', compact('policies'));
    }

    public function insuranceShow(InsurancePolicy $policy)
    {
        $client = $this->client();

        if ($policy->client_id !== $client->id) {
            abort(403);
        }

        $policy->load(['insurer', 'shipmentFile', 'claims.handledBy']);

        return view('portal.insurance.show', compact('policy'));
    }
}
