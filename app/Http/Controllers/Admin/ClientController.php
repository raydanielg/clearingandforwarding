<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Services\FileNumberService;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('tin', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $clients = $query->latest()->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.clients.partials.table', compact('clients'))->render(),
                'pagination' => $clients->links()->toHtml(),
            ]);
        }

        return view('admin.clients.index', compact('clients'));
    }

    public function create()
    {
        return view('admin.clients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'tin' => 'nullable|string|max:50',
            'vrn' => 'nullable|string|max:50',
            'type' => 'required|in:company,individual',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'credit_limit' => 'nullable|numeric|min:0',
            'payment_terms_days' => 'nullable|integer|min:0',
        ]);

        $validated['code'] = FileNumberService::generateClientCode();
        $validated['created_by'] = auth()->id();
        $validated['is_active'] = true;

        $client = Client::create($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Client created successfully', 'client' => $client]);
        }

        return redirect()->route('admin.clients.show', $client)->with('status', 'Client created successfully');
    }

    public function show(Client $client)
    {
        $client->load(['shipmentFiles' => function ($q) { $q->latest()->limit(10); }, 'invoices' => function ($q) { $q->latest()->limit(10); }]);
        return view('admin.clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        return view('admin.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'tin' => 'nullable|string|max:50',
            'vrn' => 'nullable|string|max:50',
            'type' => 'required|in:company,individual',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'credit_limit' => 'nullable|numeric|min:0',
            'payment_terms_days' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $client->update($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Client updated successfully']);
        }

        return redirect()->route('admin.clients.show', $client)->with('status', 'Client updated successfully');
    }

    public function destroy(Request $request, Client $client)
    {
        $client->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Client deleted successfully']);
        }

        return redirect()->route('admin.clients.index')->with('status', 'Client deleted');
    }
}
