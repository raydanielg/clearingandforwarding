@extends('layouts.admin')

@section('title', 'Shipment Files - ' . config('app.name'))
@section('page_title', 'Shipment Files')

@section('content')
<div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-3">
        <div class="relative">
            <input type="text" id="fileSearch" placeholder="Search File No / BL..." class="pl-9 pr-4 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none w-56">
            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
        <select id="fileStatus" class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none">
            <option value="">All Status</option>
            <option value="open">Open</option><option value="declaration">Declaration</option><option value="port_clearance">Port Clearance</option>
            <option value="transport">Transport</option><option value="delivered">Delivered</option><option value="invoiced">Invoiced</option>
            <option value="closed">Closed</option><option value="cancelled">Cancelled</option>
        </select>
        <select id="fileType" class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none">
            <option value="">All Types</option><option value="import">Import</option><option value="export">Export</option><option value="transit">Transit</option>
        </select>
    </div>
    @can('files.create')
    <a href="{{ route('admin.files.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>Open New File
    </a>
    @endcan
</div>

<div class="bg-white rounded-xl border overflow-hidden">
    <div id="filesTable">@include('admin.files.partials.table')</div>
</div>

@push('scripts')
<script>
let ft;
$('#fileSearch, #fileStatus, #fileType').on('input change', function() {
    clearTimeout(ft);
    ft = setTimeout(function() {
        const p = new URLSearchParams();
        if ($('#fileSearch').val()) p.set('search', $('#fileSearch').val());
        if ($('#fileStatus').val()) p.set('status', $('#fileStatus').val());
        if ($('#fileType').val()) p.set('type', $('#fileType').val());
        $.ajax({ url: '{{ route('admin.files.index') }}?' + p.toString(), type: 'GET', dataType: 'json',
            success: function(r) { $('#filesTable').html(r.html); } });
    }, 300);
});
</script>
@endpush
@endsection
