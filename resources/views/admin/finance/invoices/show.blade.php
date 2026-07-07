@extends('layouts.admin')
@section('title', $invoice->invoice_no . ' - ' . config('app.name'))
@section('page_title', $invoice->invoice_no)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl border p-6">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 font-mono">{{ $invoice->invoice_no }}</h2>
                    <p class="text-sm text-gray-500">{{ ucfirst($invoice->kind) }} · {{ $invoice->client?->name }}</p>
                    @if($invoice->shipmentFile)<p class="text-xs text-gray-400">File: {{ $invoice->shipmentFile->file_no }}</p>@endif
                </div>
                <span class="text-[10px] px-2 py-0.5 rounded-full {{ $invoice->status === 'paid' ? 'bg-emerald-50 text-emerald-700' : ($invoice->status === 'overdue' ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700') }}">{{ ucfirst(str_replace('_', ' ', $invoice->status)) }}</span>
            </div>
            <table class="w-full text-sm">
                <thead><tr class="text-left text-xs text-gray-500 border-b"><th class="pb-2 font-medium">Description</th><th class="pb-2 font-medium text-right">Qty</th><th class="pb-2 font-medium text-right">Unit Price</th><th class="pb-2 font-medium text-right">VAT</th><th class="pb-2 font-medium text-right">Total</th></tr></thead>
                <tbody>
                    @foreach($invoice->items as $item)
                    <tr class="border-b border-gray-50">
                        <td class="py-2 text-xs">{{ $item->description ?? $item->chargeItem?->name }}</td>
                        <td class="py-2 text-xs text-right">{{ $item->qty }}</td>
                        <td class="py-2 text-xs text-right">{{ number_format($item->unit_price) }}</td>
                        <td class="py-2 text-xs text-right">{{ $item->vat_rate }}%</td>
                        <td class="py-2 text-xs text-right font-medium">{{ number_format($item->line_total) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr><td colspan="4" class="pt-3 text-right text-xs text-gray-500">Subtotal:</td><td class="pt-3 text-right text-sm font-medium">{{ number_format($invoice->subtotal) }}</td></tr>
                    <tr><td class="text-right text-xs text-gray-500">VAT:</td><td class="text-right text-sm font-medium">{{ number_format($invoice->vat_amount) }}</td></tr>
                    <tr><td class="pt-2 border-t text-right text-sm font-bold text-gray-900">Total:</td><td class="pt-2 border-t text-right text-lg font-bold text-emerald-600">{{ number_format($invoice->total) }}</td></tr>
                    <tr><td class="text-right text-xs text-gray-500">Paid:</td><td class="text-right text-sm font-medium text-emerald-600">{{ number_format($invoice->amount_paid) }}</td></tr>
                    <tr><td class="text-right text-xs font-bold text-red-600">Balance:</td><td class="text-right text-sm font-bold text-red-600">{{ number_format($invoice->total - $invoice->amount_paid) }}</td></tr>
                </tfoot>
            </table>
        </div>

        @if($invoice->receipts->isNotEmpty())
        <div class="bg-white rounded-xl border p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Payment History</h3>
            <table class="w-full text-sm">
                <thead><tr class="text-left text-xs text-gray-500 border-b"><th class="pb-2 font-medium">Receipt No</th><th class="pb-2 font-medium">Date</th><th class="pb-2 font-medium">Method</th><th class="pb-2 font-medium text-right">Amount</th></tr></thead>
                <tbody>
                    @foreach($invoice->receipts as $r)
                    <tr class="border-b border-gray-50"><td class="py-2 font-mono text-xs"><a href="{{ route('admin.receipts.show', $r) }}" class="text-emerald-600 hover:underline">{{ $r->receipt_no }}</a></td><td class="py-2 text-xs">{{ $r->date?->format('d M Y') }}</td><td class="py-2 text-xs">{{ ucfirst($r->method) }}</td><td class="py-2 text-xs text-right font-medium">{{ number_format($r->amount) }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    <div class="space-y-4">
        <div class="bg-white rounded-xl border p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Details</h3>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500">Date</dt><dd class="font-medium">{{ $invoice->date?->format('d M Y') }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Due Date</dt><dd class="font-medium">{{ $invoice->due_date?->format('d M Y') ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Currency</dt><dd class="font-medium">{{ $invoice->currency }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Created By</dt><dd class="font-medium">{{ $invoice->createdBy?->name }}</dd></div>
            </dl>
        </div>
        @if($invoice->kind === 'proforma' && $invoice->status !== 'cancelled')
        <button onclick="convertProforma()" class="w-full px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold rounded-lg">Convert to Invoice</button>
        @endif
        @can('invoices.edit')@if($invoice->status !== 'paid')<a href="{{ route('admin.invoices.edit', $invoice) }}" class="block text-center px-4 py-2 text-sm font-medium text-amber-600 hover:bg-amber-50 rounded-lg">Edit Invoice</a>@endif@endcan
        @can('receipts.create')@if($invoice->status !== 'paid' && $invoice->status !== 'cancelled' && $invoice->status !== 'draft')
        <div class="bg-white rounded-xl border p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Record Payment</h3>
            <form id="paymentForm" action="{{ route('admin.invoices.payment', $invoice) }}" method="POST" class="space-y-3">
                @csrf
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Amount</label><input type="number" name="amount" value="{{ $invoice->total - $invoice->amount_paid }}" step="0.01" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none" required></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Method</label><select name="method" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="bank">Bank</option><option value="cash">Cash</option><option value="mobile_money">Mobile Money</option><option value="cheque">Cheque</option></select></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Date</label><input type="date" name="date" value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none" required></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Reference</label><input type="text" name="reference" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <button type="submit" class="w-full px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg">Record Payment</button>
            </form>
        </div>
        @endif@endcan
    </div>
</div>
@push('scripts')
<script>
$('#paymentForm').on('submit',function(e){e.preventDefault();$.ajax({url:this.action,type:'POST',data:$(this).serialize(),success:function(r){if(r.success){showToast('success','Success',r.message);setTimeout(()=>location.reload(),800);}},error:function(x){if(x.responseJSON?.errors){Object.values(x.responseJSON.errors).forEach(e=>showToast('error','Validation',e[0]));}else{showToast('error','Error','Could not record payment');}}});});
function convertProforma(){confirmAction('{{route('admin.invoices.convert',$invoice)}}',{title:'Convert Proforma to Invoice?',text:'This will convert this proforma into a formal invoice.',successMsg:'Proforma converted to invoice!'});}
</script>
@endpush
@endsection
