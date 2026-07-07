@extends('layouts.portal')
@section('title', 'My Shipments - ' . config('app.name'))
@section('page_title', 'My Shipments')

@section('content')
<div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-3">
        <div class="relative">
            <input type="text" id="shipSearch" placeholder="Search file no / BL..." class="pl-9 pr-4 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none w-56">
            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
        <select id="shipStatus" class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none">
            <option value="">All Statuses</option>
            <option value="draft">Draft</option>
            <option value="open">Open</option>
            <option value="declaration">Declaration</option>
            <option value="port_clearance">Port Clearance</option>
            <option value="transport">Transport</option>
            <option value="delivered">Delivered</option>
            <option value="invoiced">Invoiced</option>
            <option value="closed">Closed</option>
            <option value="cancelled">Cancelled</option>
        </select>
    </div>
</div>
<div class="bg-white rounded-xl border overflow-hidden"><div id="shipTable">@include('portal.shipments.partials.table')</div></div>
@push('scripts')
<script>
let st;
function loadShips() {
    const p = new URLSearchParams();
    if ($('#shipSearch').val()) p.set('search', $('#shipSearch').val());
    if ($('#shipStatus').val()) p.set('status', $('#shipStatus').val());
    $.ajax({ url: '{{ route('portal.shipments.index') }}?' + p.toString(), type: 'GET', dataType: 'json',
        success: function(r) { $('#shipTable').html(r.html); } });
}
$('#shipSearch').on('input', function() { clearTimeout(st); st = setTimeout(loadShips, 300); });
$('#shipStatus').on('change', loadShips);
</script>
@endpush
@endsection
