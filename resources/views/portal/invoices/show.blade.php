@extends('layouts.portal')
@section('title', $invoice->invoice_no . ' - ' . config('app.name'))
@section('page_title', $invoice->invoice_no)

@section('content')
<div class="space-y-6 max-w-4xl">
    <a href="{{ route('portal.invoices.index') }}" class="text-xs font-medium text-emerald-600 hover:text-emerald-700">← Back to Invoices</a>

    {{-- Invoice Header --}}
    <div class="bg-white rounded-xl border p-6">
        <div class="flex items-start justify-between mb-6">
            <div>
                <h2 class="text-lg font-bold text-gray-900">{{ $invoice->invoice_no }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $invoice->kind === 'proforma' ? 'Proforma Invoice' : 'Invoice' }}</p>
            </div>
            <span class="text-[10px] px-3 py-1 rounded-full @if($invoice->status === 'paid') bg-emerald-50 text-emerald-700 @elseif($invoice->status === 'overdue') bg-red-50 text-red-700 @else bg-amber-50 text-amber-700 @endif capitalize">{{ str_replace('_', ' ', $invoice->status) }}</span>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div><p class="text-[10px] text-gray-500 font-medium">Date</p><p class="text-sm text-gray-900 mt-0.5">{{ $invoice->date?->format('d M Y') }}</p></div>
            <div><p class="text-[10px] text-gray-500 font-medium">Due Date</p><p class="text-sm text-gray-900 mt-0.5">{{ $invoice->due_date?->format('d M Y') ?? '—' }}</p></div>
            <div><p class="text-[10px] text-gray-500 font-medium">Currency</p><p class="text-sm text-gray-900 mt-0.5">{{ $invoice->currency }}</p></div>
            <div><p class="text-[10px] text-gray-500 font-medium">File</p><p class="text-sm text-gray-900 mt-0.5">{{ $invoice->shipmentFile?->file_no ?? '—' }}</p></div>
        </div>
    </div>

    {{-- Line Items --}}
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b"><h3 class="text-sm font-semibold text-gray-900">Line Items</h3></div>
        <table class="w-full text-sm">
            <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50 border-b">
                <th class="px-5 py-3 font-medium">Description</th><th class="px-5 py-3 font-medium text-right">Qty</th><th class="px-5 py-3 font-medium text-right">Unit Price</th><th class="px-5 py-3 font-medium text-right">VAT %</th><th class="px-5 py-3 font-medium text-right">Total</th>
            </tr></thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr class="border-b border-gray-100">
                    <td class="px-5 py-3 text-xs text-gray-900">{{ $item->description ?? $item->chargeItem?->name ?? '—' }}</td>
                    <td class="px-5 py-3 text-xs text-gray-700 text-right">{{ number_format($item->qty) }}</td>
                    <td class="px-5 py-3 text-xs text-gray-700 text-right">{{ number_format($item->unit_price) }}</td>
                    <td class="px-5 py-3 text-xs text-gray-500 text-right">{{ $item->vat_rate }}%</td>
                    <td class="px-5 py-3 text-xs font-medium text-gray-900 text-right">{{ number_format($item->line_total) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="border-t-2 border-gray-200 bg-gray-50/50">
                    <td colspan="3" class="px-5 py-3"></td>
                    <td class="px-5 py-3 text-xs font-medium text-gray-700 text-right">Subtotal</td>
                    <td class="px-5 py-3 text-xs font-bold text-gray-900 text-right">{{ number_format($invoice->subtotal) }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="px-5 py-2"></td>
                    <td class="px-5 py-2 text-xs font-medium text-gray-700 text-right">VAT</td>
                    <td class="px-5 py-2 text-xs font-bold text-gray-900 text-right">{{ number_format($invoice->vat_amount) }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="px-5 py-2"></td>
                    <td class="px-5 py-2 text-xs font-bold text-gray-900 text-right">Total</td>
                    <td class="px-5 py-2 text-xs font-bold text-emerald-700 text-right">{{ number_format($invoice->total) }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="px-5 py-2"></td>
                    <td class="px-5 py-2 text-xs font-medium text-emerald-600 text-right">Paid</td>
                    <td class="px-5 py-2 text-xs font-bold text-emerald-600 text-right">{{ number_format($invoice->amount_paid) }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="px-5 py-2"></td>
                    <td class="px-5 py-2 text-xs font-bold text-red-600 text-right">Outstanding</td>
                    <td class="px-5 py-2 text-xs font-bold text-red-600 text-right">{{ number_format($invoice->total - $invoice->amount_paid) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Receipts --}}
    @if($invoice->receipts->isNotEmpty())
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b"><h3 class="text-sm font-semibold text-gray-900">Payment History</h3></div>
        <table class="w-full text-sm">
            <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50 border-b">
                <th class="px-5 py-3 font-medium">Receipt No</th><th class="px-5 py-3 font-medium">Date</th><th class="px-5 py-3 font-medium">Method</th><th class="px-5 py-3 font-medium">Reference</th><th class="px-5 py-3 font-medium text-right">Amount</th>
            </tr></thead>
            <tbody>
                @foreach($invoice->receipts as $rec)
                <tr class="border-b border-gray-100">
                    <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $rec->receipt_no }}</td>
                    <td class="px-5 py-3 text-xs text-gray-500">{{ $rec->date?->format('d M Y') }}</td>
                    <td class="px-5 py-3 text-xs text-gray-700 capitalize">{{ $rec->method }}</td>
                    <td class="px-5 py-3 text-xs text-gray-500">{{ $rec->reference ?? '—' }}</td>
                    <td class="px-5 py-3 text-xs font-medium text-emerald-600 text-right">{{ number_format($rec->amount) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
