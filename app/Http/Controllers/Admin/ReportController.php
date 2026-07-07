<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShipmentFile;
use App\Models\Invoice;
use App\Models\Disbursement;
use App\Models\Declaration;
use App\Models\Client;
use App\Models\User;
use App\Models\InsurancePolicy;
use App\Models\InsuranceClaim;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function operations(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->get('to', now()->format('Y-m-d'));

        $query = ShipmentFile::with(['client', 'declarationOfficer', 'clearingOfficer'])
            ->whereDate('opened_at', '>=', $from)
            ->whereDate('opened_at', '<=', $to);

        $files = $query->latest()->paginate(20);

        $stats = [
            'total_files'   => (clone $query)->count(),
            'open_files'    => (clone $query)->whereNotIn('status', ['closed', 'cancelled'])->count(),
            'closed_files'  => (clone $query)->where('status', 'closed')->count(),
            'declarations'  => Declaration::whereHas('shipmentFile', function ($q) use ($from, $to) {
                $q->whereDate('opened_at', '>=', $from)->whereDate('opened_at', '<=', $to);
            })->count(),
        ];

        return view('admin.reports.operations', compact('files', 'stats'));
    }

    public function revenue(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->get('to', now()->format('Y-m-d'));

        $query = Invoice::with(['client', 'shipmentFile'])
            ->whereDate('date', '>=', $from)
            ->whereDate('date', '<=', $to);

        $invoices = $query->latest()->paginate(20);

        $stats = [
            'total_invoiced'   => (clone $query)->sum('total'),
            'total_collected'  => (clone $query)->sum('amount_paid'),
            'outstanding'      => (clone $query)->sum('total') - (clone $query)->sum('amount_paid'),
            'total_disbursed'  => Disbursement::whereDate('paid_at', '>=', $from)->whereDate('paid_at', '<=', $to)->sum('amount'),
        ];

        return view('admin.reports.revenue', compact('invoices', 'stats'));
    }

    public function disbursements(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->get('to', now()->format('Y-m-d'));

        $query = Disbursement::with(['shipmentFile.client', 'agency'])
            ->whereDate('paid_at', '>=', $from)
            ->whereDate('paid_at', '<=', $to);

        $disbursements = $query->latest()->paginate(20);

        $stats = [
            'total'    => (clone $query)->sum('amount'),
            'approved' => (clone $query)->whereNotNull('approved_by')->sum('amount'),
            'pending'  => (clone $query)->whereNull('approved_by')->sum('amount'),
        ];

        return view('admin.reports.disbursements', compact('disbursements', 'stats'));
    }

    public function debtors(Request $request)
    {
        $clients = Client::whereHas('invoices', function ($q) {
            $q->whereIn('status', ['sent', 'partially_paid', 'overdue'])
              ->whereRaw('amount_paid < total');
        })->with(['invoices' => function ($q) {
            $q->whereIn('status', ['sent', 'partially_paid', 'overdue'])
              ->whereRaw('amount_paid < total');
        }])->get()->map(function ($client) {
            $now = now();
            $current = 0; $d30 = 0; $d60 = 0; $d90 = 0;
            foreach ($client->invoices as $inv) {
                $diff = $inv->date?->diffInDays($now) ?? 0;
                $remaining = $inv->total - $inv->amount_paid;
                if ($diff <= 30) $current += $remaining;
                elseif ($diff <= 60) $d30 += $remaining;
                elseif ($diff <= 90) $d60 += $remaining;
                else $d90 += $remaining;
            }
            return [
                'client'   => $client->name,
                'current'  => $current,
                'd30'      => $d30,
                'd60'      => $d60,
                'd90'      => $d90,
                'total'    => $current + $d30 + $d60 + $d90,
            ];
        })->sortByDesc('total')->values();

        return view('admin.reports.debtors', compact('clients'));
    }

    public function officerPerformance(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->get('to', now()->format('Y-m-d'));

        $users = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['Operations Manager', 'Declaration Officer', 'Clearing Officer', 'Documentation Officer']);
        })->get();

        $officers = $users->map(function ($user) use ($from, $to) {
            $filesAssigned = ShipmentFile::where(function ($q) use ($user) {
                $q->where('assigned_declaration_officer_id', $user->id)
                  ->orWhere('assigned_clearing_officer_id', $user->id);
            })->whereDate('opened_at', '>=', $from)->whereDate('opened_at', '<=', $to)->count();

            $declarations = Declaration::where('officer_id', $user->id)
                ->whereHas('shipmentFile', function ($q) use ($from, $to) {
                    $q->whereDate('opened_at', '>=', $from)->whereDate('opened_at', '<=', $to);
                })->count();

            $filesClosed = ShipmentFile::where(function ($q) use ($user) {
                $q->where('assigned_declaration_officer_id', $user->id)
                  ->orWhere('assigned_clearing_officer_id', $user->id);
            })->where('status', 'closed')
              ->whereDate('opened_at', '>=', $from)->whereDate('opened_at', '<=', $to)->count();

            $revenue = Invoice::whereHas('shipmentFile', function ($q) use ($user) {
                $q->where('assigned_declaration_officer_id', $user->id)
                  ->orWhere('assigned_clearing_officer_id', $user->id);
            })->whereDate('date', '>=', $from)->whereDate('date', '<=', $to)->sum('total');

            return [
                'name'            => $user->name,
                'files_assigned'  => $filesAssigned,
                'declarations'    => $declarations,
                'files_closed'    => $filesClosed,
                'revenue'         => $revenue,
            ];
        })->sortByDesc('files_assigned')->values();

        return view('admin.reports.officer-performance', compact('officers'));
    }

    public function insurance(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->get('to', now()->format('Y-m-d'));

        $policies = InsurancePolicy::with(['client', 'insurer', 'shipmentFile'])
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->latest()->paginate(20);

        $stats = [
            'total_policies'    => InsurancePolicy::whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)->count(),
            'active_policies'   => InsurancePolicy::whereIn('status', ['issued', 'active'])->count(),
            'total_premium'     => InsurancePolicy::whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)->sum('premium_amount'),
            'total_commission'  => InsurancePolicy::whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)->sum('commission_amount'),
            'total_claims'      => InsuranceClaim::whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)->count(),
            'open_claims'       => InsuranceClaim::whereIn('status', ['submitted', 'under_review', 'approved'])->count(),
            'claims_settled'    => InsuranceClaim::where('status', 'settled')->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)->sum('settled_amount'),
        ];

        return view('admin.reports.insurance', compact('policies', 'stats'));
    }
}
