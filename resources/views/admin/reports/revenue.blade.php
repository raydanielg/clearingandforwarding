@extends('layouts.admin')
@section('title', 'Revenue Report - ' . config('app.name'))
@section('page_title', 'Revenue Report')

@section('content')
<div class="bg-white rounded-xl border p-6 mb-6">
    <form id="filterForm" class="flex items-end gap-4">
        <div><label class="block text-xs font-medium text-gray-700 mb-1">From Date</label><input type="date" name="from" value="{{ request('from', date('Y-m-01')) }}" class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
        <div><label class="block text-xs font-medium text-gray-700 mb-1">To Date</label><input type="date" name="to" value="{{ request('to', date('Y-m-d')) }}" class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg">Filter</button>
    </form>
</div>

<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border p-4"><p class="text-[10px] font-medium text-gray-500">Total Invoiced</p><p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_invoiced']) }}</p></div>
    <div class="bg-white rounded-xl border p-4"><p class="text-[10px] font-medium text-gray-500">Total Collected</p><p class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($stats['total_collected']) }}</p></div>
    <div class="bg-white rounded-xl border p-4"><p class="text-[10px] font-medium text-gray-500">Outstanding</p><p class="text-2xl font-bold text-red-600 mt-1">{{ number_format($stats['outstanding']) }}</p></div>
    <div class="bg-white rounded-xl border p-4"><p class="text-[10px] font-medium text-gray-500">Disbursed</p><p class="text-2xl font-bold text-amber-600 mt-1">{{ number_format($stats['total_disbursed']) }}</p></div>
</div>

<div class="bg-white rounded-xl border overflow-hidden">
    <div class="px-5 py-4 border-b"><h3 class="text-sm font-semibold text-gray-900">Invoices in Period</h3></div>
    <table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50 border-b">
            <th class="px-5 py-3 font-medium">Invoice No</th><th class="px-5 py-3 font-medium">Client</th><th class="px-5 py-3 font-medium">Date</th><th class="px-5 py-3 font-medium text-right">Total</th><th class="px-5 py-3 font-medium text-right">Paid</th><th class="px-5 py-3 font-medium">Status</th>
        </tr></thead>
        <tbody>
            @forelse($invoices as $inv)
            <tr class="border-b border-gray-100 hover:bg-gray-50/50">
                <td class="px-5 py-3"><a href="{{ route('admin.invoices.show', $inv) }}" class="font-mono text-xs text-emerald-600 hover:underline">{{ $inv->invoice_no }}</a></td>
                <td class="px-5 py-3 text-xs text-gray-700">{{ $inv->client?->name }}</td>
                <td class="px-5 py-3 text-xs text-gray-500">{{ $inv->date?->format('d M Y') }}</td>
                <td class="px-5 py-3 text-xs text-right font-medium">{{ number_format($inv->total) }}</td>
                <td class="px-5 py-3 text-xs text-right text-emerald-600">{{ number_format($inv->amount_paid) }}</td>
                <td class="px-5 py-3"><span class="text-[10px] px-2 py-0.5 rounded-full {{ $inv->status === 'paid' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">{{ ucfirst(str_replace('_', ' ', $inv->status)) }}</span></td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-5 py-12 text-center text-sm text-gray-400">No invoices in this period.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@push('scripts')
<script>$('#filterForm').on('submit',function(e){e.preventDefault();const p=new URLSearchParams();p.set('from',$('input[name="from"]').val());p.set('to',$('input[name="to"]').val());window.location.href='{{route('admin.reports.revenue')}}?'+p.toString();});</script>
@endpush
@endsection
