<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with('recordedBy');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($request->filled('from')) {
            $query->whereDate('date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('date', '<=', $request->to);
        }

        $expenses = $query->latest()->paginate(15);

        $monthTotal = Expense::whereMonth('date', now()->month)->sum('amount');

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.finance.expenses.partials.table', compact('expenses'))->render(),
            ]);
        }

        return view('admin.finance.expenses.index', compact('expenses', 'monthTotal'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $validated['recorded_by'] = auth()->id();

        if ($request->hasFile('receipt')) {
            $validated['receipt_path'] = $request->file('receipt')->store('receipts', 'private');
        }

        Expense::create($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Expense recorded']);
        }

        return back()->with('status', 'Expense recorded');
    }

    public function destroy(Request $request, Expense $expense)
    {
        if ($expense->receipt_path) {
            Storage::disk('private')->delete($expense->receipt_path);
        }
        $expense->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Expense deleted']);
        }

        return back()->with('status', 'Expense deleted');
    }
}
