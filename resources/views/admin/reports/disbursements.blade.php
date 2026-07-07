@extends('layouts.admin')
@section('title', 'Disbursement Report - ' . config('app.name'))
@section('page_title', 'Disbursement Report')

@section('content')
<div class="bg-white rounded-xl border p-6 mb-6">
    <form id="filterForm" class="flex items-end gap-4">
        <div><label class="block text-xs font-medium text-gray-700 mb-1">From Date</label><input type="date" name="from" value="{{ request('from', date('Y-m-01')) }}" class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
        <div><label class="block text-xs font-medium text-gray-700 mb-1">To Date</label><input type="date" name="to" value="{{ request('to', date('Y-m-d')) }}" class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg">Filter</button>
    </form>
</div>

<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl border p-4"><p class="text-[10px] font-medium text-gray-500">Total Disbursed</p><p class="text-2xl font-bold text-red-600 mt-1">{{ number_format($stats['total']) }}</p></div>
    <div class="bg-white rounded-xl border p-4"><p class="text-[10px] font-medium text-gray-500">Approved</p><p class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($stats['approved']) }}</p></div>
    <div class="bg-white rounded-xl border p-4"><p class="text-[10px] font-medium text-gray-500">Pending Approval</p><p class="text-2xl font-bold text-amber-600 mt-1">{{ number_format($stats['pending']) }}</p></div>
</div>

<div class="bg-white rounded-xl border overflow-hidden">
    <div class="px-5 py-4 border-b"><h3 class="text-sm font-semibold text-gray-900">Disbursements in Period</h3></div>
    <table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50 border-b">
            <th class="px-5 py-3 font-medium">File No</th><th class="px-5 py-3 font-medium">Payee</th><th class="px-5 py-3 font-medium">Agency</th><th class="px-5 py-3 font-medium">Date</th><th class="px-5 py-3 font-medium text-right">Amount</th><th class="px-5 py-3 font-medium">Approved</th>
        </tr></thead>
        <tbody>
            @forelse($disbursements as $d)
            <tr class="border-b border-gray-100 hover:bg-gray-50/50">
                <td class="px-5 py-3"><a href="{{ route('admin.files.show', $d->shipmentFile) }}" class="font-mono text-xs text-emerald-600 hover:underline">{{ $d->shipmentFile?->file_no }}</a></td>
                <td class="px-5 py-3 text-xs text-gray-700">{{ $d->payee ?? $d->agency?->name }}</td>
                <td class="px-5 py-3 text-xs text-gray-500">{{ $d->agency?->name ?? '—' }}</td>
                <td class="px-5 py-3 text-xs text-gray-500">{{ $d->paid_at?->format('d M Y') }}</td>
                <td class="px-5 py-3 text-xs text-right font-medium text-red-600">{{ number_format($d->amount) }}</td>
                <td class="px-5 py-3">@if($d->approved_by)<span class="text-[10px] text-emerald-600">Yes</span>@else<span class="text-[10px] text-amber-600">Pending</span>@endif</td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-5 py-12 text-center text-sm text-gray-400">No disbursements in this period.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@push('scripts')
<script>$('#filterForm').on('submit',function(e){e.preventDefault();const p=new URLSearchParams();p.set('from',$('input[name="from"]').val());p.set('to',$('input[name="to"]').val());window.location.href='{{route('admin.reports.disbursements')}}?'+p.toString();});</script>
@endpush
@endsection
