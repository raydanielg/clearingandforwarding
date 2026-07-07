@extends('layouts.admin')
@section('title', 'Operations Report - ' . config('app.name'))
@section('page_title', 'Operations Report')

@section('content')
<div class="bg-white rounded-xl border p-6 mb-6">
    <form id="filterForm" class="flex items-end gap-4">
        <div><label class="block text-xs font-medium text-gray-700 mb-1">From Date</label><input type="date" name="from" value="{{ request('from', date('Y-m-01')) }}" class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
        <div><label class="block text-xs font-medium text-gray-700 mb-1">To Date</label><input type="date" name="to" value="{{ request('to', date('Y-m-d')) }}" class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg">Filter</button>
    </form>
</div>

<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border p-4"><p class="text-[10px] font-medium text-gray-500">Total Files</p><p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_files'] }}</p></div>
    <div class="bg-white rounded-xl border p-4"><p class="text-[10px] font-medium text-gray-500">Open Files</p><p class="text-2xl font-bold text-emerald-600 mt-1">{{ $stats['open_files'] }}</p></div>
    <div class="bg-white rounded-xl border p-4"><p class="text-[10px] font-medium text-gray-500">Closed Files</p><p class="text-2xl font-bold text-gray-600 mt-1">{{ $stats['closed_files'] }}</p></div>
    <div class="bg-white rounded-xl border p-4"><p class="text-[10px] font-medium text-gray-500">Declarations</p><p class="text-2xl font-bold text-sky-600 mt-1">{{ $stats['declarations'] }}</p></div>
</div>

<div class="bg-white rounded-xl border overflow-hidden">
    <div class="px-5 py-4 border-b"><h3 class="text-sm font-semibold text-gray-900">Files in Period</h3></div>
    <table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50 border-b">
            <th class="px-5 py-3 font-medium">File No</th><th class="px-5 py-3 font-medium">Client</th><th class="px-5 py-3 font-medium">Type</th><th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3 font-medium">Opened</th>
        </tr></thead>
        <tbody>
            @forelse($files as $f)
            <tr class="border-b border-gray-100 hover:bg-gray-50/50">
                <td class="px-5 py-3"><a href="{{ route('admin.files.show', $f) }}" class="font-mono text-xs text-emerald-600 hover:underline">{{ $f->file_no }}</a></td>
                <td class="px-5 py-3 text-xs text-gray-700">{{ $f->client?->name }}</td>
                <td class="px-5 py-3"><span class="text-[10px] px-2 py-0.5 rounded-full bg-gray-50 text-gray-600">{{ ucfirst($f->type) }}</span></td>
                <td class="px-5 py-3"><span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700">{{ ucfirst(str_replace('_', ' ', $f->status)) }}</span></td>
                <td class="px-5 py-3 text-xs text-gray-400">{{ $f->opened_at?->format('d M Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-5 py-12 text-center text-sm text-gray-400">No files in this period.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@push('scripts')
<script>$('#filterForm').on('submit',function(e){e.preventDefault();const p=new URLSearchParams();p.set('from',$('input[name="from"]').val());p.set('to',$('input[name="to"]').val());window.location.href='{{route('admin.reports.operations')}}?'+p.toString();});</script>
@endpush
@endsection
