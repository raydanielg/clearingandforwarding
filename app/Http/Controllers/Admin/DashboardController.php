<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShipmentFile;
use App\Models\Invoice;
use App\Models\Trip;
use App\Models\InsurancePolicy;
use App\Models\InsuranceClaim;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $openFiles = ShipmentFile::whereNotIn('status', ['closed', 'cancelled'])->count();
        $filesThisWeek = ShipmentFile::where('opened_at', '>=', now()->startOfWeek())->count();
        $demurrageRisk = ShipmentFile::where('free_days_expiry', '<=', now()->addDays(3))
            ->whereNotIn('status', ['closed', 'cancelled'])->count();
        $unpaidInvoices = Invoice::whereIn('status', ['sent', 'partially_paid', 'overdue'])->count();
        $revenueThisMonth = Invoice::whereMonth('date', now()->month)->sum('total');
        $deliveriesToday = Trip::whereDate('dispatched_at', today())
            ->whereIn('status', ['in_transit', 'delivered'])->count();

        $activePolicies = InsurancePolicy::whereIn('status', ['issued', 'active'])->count();
        $openClaims = InsuranceClaim::whereIn('status', ['submitted', 'under_review', 'approved'])->count();
        $premiumThisMonth = InsurancePolicy::whereMonth('created_at', now()->month)
            ->whereIn('status', ['issued', 'active', 'expired', 'claimed'])
            ->sum('premium_amount');

        $stats = [
            'openFiles'       => $openFiles,
            'filesThisWeek'   => $filesThisWeek,
            'demurrageRisk'   => $demurrageRisk,
            'unpaidInvoices'  => $unpaidInvoices,
            'deliveriesToday' => $deliveriesToday,
            'revenueThisMonth'=> $revenueThisMonth,
            'activePolicies'  => $activePolicies,
            'openClaims'      => $openClaims,
            'premiumThisMonth'=> $premiumThisMonth,
        ];

        $recentFiles = ShipmentFile::with('client')
            ->latest()->limit(10)->get();

        $statusBreakdown = [
            'Open'             => ShipmentFile::where('status', 'open')->count(),
            'Declaration'      => ShipmentFile::where('status', 'declaration')->count(),
            'Port Clearance'   => ShipmentFile::where('status', 'port_clearance')->count(),
            'Transport'        => ShipmentFile::where('status', 'transport')->count(),
            'Delivered'        => ShipmentFile::where('status', 'delivered')->count(),
            'Invoiced'         => ShipmentFile::where('status', 'invoiced')->count(),
            'Closed'           => ShipmentFile::where('status', 'closed')->count(),
        ];

        return view('admin.dashboard', compact('stats', 'recentFiles', 'statusBreakdown'));
    }
}
