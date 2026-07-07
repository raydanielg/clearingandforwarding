<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingLine;
use App\Models\Terminal;
use App\Models\Agency;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    // ── Shipping Lines ──
    public function linesIndex(Request $request)
    {
        $query = ShippingLine::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('code', 'like', "%{$request->search}%");
        }

        $lines = $query->latest()->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.partners.lines.partials.table', compact('lines'))->render(),
            ]);
        }

        return view('admin.partners.lines.index', compact('lines'));
    }

    public function linesStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $line = ShippingLine::create($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Shipping line created', 'line' => $line]);
        }

        return back()->with('status', 'Shipping line created');
    }

    public function linesUpdate(Request $request, ShippingLine $line)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $line->update($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Shipping line updated']);
        }

        return back()->with('status', 'Shipping line updated');
    }

    public function linesDestroy(Request $request, ShippingLine $line)
    {
        $line->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Shipping line deleted']);
        }

        return back()->with('status', 'Shipping line deleted');
    }

    // ── Terminals ──
    public function terminalsIndex(Request $request)
    {
        $query = Terminal::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('code', 'like', "%{$request->search}%");
        }

        $terminals = $query->latest()->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.partners.terminals.partials.table', compact('terminals'))->render(),
            ]);
        }

        return view('admin.partners.terminals.index', compact('terminals'));
    }

    public function terminalsStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'type' => 'nullable|string|max:50',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $terminal = Terminal::create($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Terminal created', 'terminal' => $terminal]);
        }

        return back()->with('status', 'Terminal created');
    }

    public function terminalsUpdate(Request $request, Terminal $terminal)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'type' => 'nullable|string|max:50',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $terminal->update($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Terminal updated']);
        }

        return back()->with('status', 'Terminal updated');
    }

    public function terminalsDestroy(Request $request, Terminal $terminal)
    {
        $terminal->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Terminal deleted']);
        }

        return back()->with('status', 'Terminal deleted');
    }

    // ── Agencies ──
    public function agenciesIndex(Request $request)
    {
        $query = Agency::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('code', 'like', "%{$request->search}%");
        }

        $agencies = $query->latest()->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.partners.agencies.partials.table', compact('agencies'))->render(),
            ]);
        }

        return view('admin.partners.agencies.index', compact('agencies'));
    }

    public function agenciesStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $agency = Agency::create($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Agency created', 'agency' => $agency]);
        }

        return back()->with('status', 'Agency created');
    }

    public function agenciesUpdate(Request $request, Agency $agency)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $agency->update($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Agency updated']);
        }

        return back()->with('status', 'Agency updated');
    }

    public function agenciesDestroy(Request $request, Agency $agency)
    {
        $agency->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Agency deleted']);
        }

        return back()->with('status', 'Agency deleted');
    }
}
