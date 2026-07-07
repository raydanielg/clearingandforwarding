<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InsurancePolicy;
use App\Models\InsuranceClaim;
use App\Models\Insurer;
use App\Models\Client;
use App\Models\ShipmentFile;
use App\Services\FileNumberService;
use Illuminate\Http\Request;

class InsuranceController extends Controller
{
    // ── Insurers ──

    public function insurersIndex(Request $request)
    {
        $query = Insurer::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('license_no', 'like', "%{$search}%");
            });
        }

        $insurers = $query->latest()->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.insurance.insurers.partials.table', compact('insurers'))->render(),
            ]);
        }

        return view('admin.insurance.insurers.index', compact('insurers'));
    }

    public function insurersStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:insurers,code',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'license_no' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $insurer = Insurer::create($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Insurer created successfully']);
        }

        return back()->with('status', 'Insurer created');
    }

    public function insurersUpdate(Request $request, Insurer $insurer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:insurers,code,' . $insurer->id,
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'license_no' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $insurer->update($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Insurer updated successfully']);
        }

        return back()->with('status', 'Insurer updated');
    }

    public function insurersDestroy(Request $request, Insurer $insurer)
    {
        $insurer->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Insurer deleted']);
        }

        return back()->with('status', 'Insurer deleted');
    }

    // ── Policies ──

    public function policiesIndex(Request $request)
    {
        $query = InsurancePolicy::with(['client', 'insurer', 'shipmentFile', 'issuedBy']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('policy_no', 'like', "%{$search}%")
                  ->orWhereHas('client', fn($cq) => $cq->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('shipmentFile', fn($fq) => $fq->where('file_no', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $policies = $query->latest()->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.insurance.policies.partials.table', compact('policies'))->render(),
            ]);
        }

        return view('admin.insurance.policies.index', compact('policies'));
    }

    public function policiesCreate()
    {
        $clients = Client::where('is_active', true)->orderBy('name')->get();
        $insurers = Insurer::where('is_active', true)->orderBy('name')->get();
        $files = ShipmentFile::whereNotIn('status', ['closed', 'cancelled'])->with('client')->latest()->limit(50)->get();

        return view('admin.insurance.policies.create', compact('clients', 'insurers', 'files'));
    }

    public function policiesStore(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'shipment_file_id' => 'nullable|exists:shipment_files,id',
            'insurer_id' => 'nullable|exists:insurers,id',
            'type' => 'required|in:marine_cargo,goods_in_transit,warehouse,other',
            'cover_start_date' => 'nullable|date',
            'cover_end_date' => 'nullable|date',
            'origin' => 'nullable|string|max:255',
            'destination' => 'nullable|string|max:255',
            'cargo_description' => 'nullable|string',
            'cargo_value' => 'nullable|numeric|min:0',
            'cargo_currency' => 'nullable|string|size:3',
            'sum_insured' => 'nullable|numeric|min:0',
            'premium_amount' => 'nullable|numeric|min:0',
            'premium_currency' => 'nullable|string|size:3',
            'commission_rate' => 'nullable|numeric|min:0',
            'terms_conditions' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['policy_no'] = FileNumberService::generatePolicyNumber();
        $validated['status'] = 'quoted';
        $validated['issued_by'] = auth()->id();

        if (!empty($validated['commission_rate']) && !empty($validated['premium_amount'])) {
            $validated['commission_amount'] = $validated['premium_amount'] * $validated['commission_rate'] / 100;
        }

        $policy = InsurancePolicy::create($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Policy created: ' . $policy->policy_no, 'redirect' => route('admin.insurance.policies.show', $policy)]);
        }

        return redirect()->route('admin.insurance.policies.show', $policy)->with('status', 'Policy created: ' . $policy->policy_no);
    }

    public function policiesShow(InsurancePolicy $policy)
    {
        $policy->load(['client', 'insurer', 'shipmentFile', 'issuedBy', 'claims.handledBy']);

        return view('admin.insurance.policies.show', compact('policy'));
    }

    public function policiesEdit(InsurancePolicy $policy)
    {
        $clients = Client::where('is_active', true)->orderBy('name')->get();
        $insurers = Insurer::where('is_active', true)->orderBy('name')->get();
        $files = ShipmentFile::whereNotIn('status', ['closed', 'cancelled'])->with('client')->latest()->limit(50)->get();

        return view('admin.insurance.policies.edit', compact('policy', 'clients', 'insurers', 'files'));
    }

    public function policiesUpdate(Request $request, InsurancePolicy $policy)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'shipment_file_id' => 'nullable|exists:shipment_files,id',
            'insurer_id' => 'nullable|exists:insurers,id',
            'type' => 'required|in:marine_cargo,goods_in_transit,warehouse,other',
            'status' => 'nullable|in:draft,quoted,issued,active,expired,cancelled,claimed',
            'cover_start_date' => 'nullable|date',
            'cover_end_date' => 'nullable|date',
            'origin' => 'nullable|string|max:255',
            'destination' => 'nullable|string|max:255',
            'cargo_description' => 'nullable|string',
            'cargo_value' => 'nullable|numeric|min:0',
            'cargo_currency' => 'nullable|string|size:3',
            'sum_insured' => 'nullable|numeric|min:0',
            'premium_amount' => 'nullable|numeric|min:0',
            'premium_currency' => 'nullable|string|size:3',
            'commission_rate' => 'nullable|numeric|min:0',
            'terms_conditions' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if (isset($validated['status'])) {
            if ($validated['status'] === 'issued' && !$policy->issued_at) {
                $validated['issued_at'] = now();
            }
            if ($validated['status'] === 'active' && !$policy->issued_at) {
                $validated['issued_at'] = now();
                $validated['status'] = 'active';
            }
        }

        if (!empty($validated['commission_rate']) && !empty($validated['premium_amount'])) {
            $validated['commission_amount'] = $validated['premium_amount'] * $validated['commission_rate'] / 100;
        }

        $policy->update($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Policy updated successfully']);
        }

        return redirect()->route('admin.insurance.policies.show', $policy)->with('status', 'Policy updated');
    }

    public function policiesDestroy(Request $request, InsurancePolicy $policy)
    {
        $policy->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Policy deleted']);
        }

        return redirect()->route('admin.insurance.policies.index')->with('status', 'Policy deleted');
    }

    // ── Claims ──

    public function claimsIndex(Request $request)
    {
        $query = InsuranceClaim::with(['policy.insurer', 'client', 'shipmentFile', 'handledBy']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('claim_no', 'like', "%{$search}%")
                  ->orWhereHas('client', fn($cq) => $cq->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('policy', fn($pq) => $pq->where('policy_no', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $claims = $query->latest()->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.insurance.claims.partials.table', compact('claims'))->render(),
            ]);
        }

        return view('admin.insurance.claims.index', compact('claims'));
    }

    public function claimsCreate()
    {
        $policies = InsurancePolicy::whereIn('status', ['issued', 'active', 'claimed'])->with(['client', 'insurer'])->latest()->get();
        return view('admin.insurance.claims.create', compact('policies'));
    }

    public function claimsStore(Request $request)
    {
        $validated = $request->validate([
            'insurance_policy_id' => 'required|exists:insurance_policies,id',
            'incident_date' => 'nullable|date',
            'incident_description' => 'nullable|string',
            'loss_location' => 'nullable|string|max:255',
            'claimed_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $policy = InsurancePolicy::findOrFail($validated['insurance_policy_id']);

        $validated['claim_no'] = FileNumberService::generateClaimNumber();
        $validated['client_id'] = $policy->client_id;
        $validated['shipment_file_id'] = $policy->shipment_file_id;
        $validated['status'] = 'submitted';
        $validated['submitted_at'] = now();
        $validated['handled_by'] = auth()->id();

        $claim = InsuranceClaim::create($validated);

        $policy->update(['status' => 'claimed']);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Claim created: ' . $claim->claim_no, 'redirect' => route('admin.insurance.claims.show', $claim)]);
        }

        return redirect()->route('admin.insurance.claims.show', $claim)->with('status', 'Claim created: ' . $claim->claim_no);
    }

    public function claimsShow(InsuranceClaim $claim)
    {
        $claim->load(['policy.insurer', 'client', 'shipmentFile', 'handledBy']);
        return view('admin.insurance.claims.show', compact('claim'));
    }

    public function claimsUpdate(Request $request, InsuranceClaim $claim)
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,submitted,under_review,approved,rejected,settled',
            'incident_date' => 'nullable|date',
            'incident_description' => 'nullable|string',
            'loss_location' => 'nullable|string|max:255',
            'claimed_amount' => 'nullable|numeric|min:0',
            'approved_amount' => 'nullable|numeric|min:0',
            'settled_amount' => 'nullable|numeric|min:0',
            'insurer_response' => 'nullable|string',
            'settled_at' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        if ($validated['status'] === 'settled' && !isset($validated['settled_at'])) {
            $validated['settled_at'] = now()->format('Y-m-d');
        }

        $claim->update($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Claim updated successfully']);
        }

        return back()->with('status', 'Claim updated');
    }

    public function claimsDestroy(Request $request, InsuranceClaim $claim)
    {
        $claim->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Claim deleted']);
        }

        return redirect()->route('admin.insurance.claims.index')->with('status', 'Claim deleted');
    }
}
