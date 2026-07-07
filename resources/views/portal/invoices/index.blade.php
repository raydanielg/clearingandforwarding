@extends('layouts.portal')
@section('title', 'My Invoices - ' . config('app.name'))
@section('page_title', 'Invoices & Receipts')

@section('content')
<div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-3">
        <div class="relative">
            <input type="text" id="invSearch" placeholder="Search invoice no..." class="pl-9 pr-4 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none w-56">
            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
        <select id="invStatus" class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none">
            <option value="">All Statuses</option>
            <option value="draft">Draft</option>
            <option value="sent">Sent</option>
            <option value="partially_paid">Partially Paid</option>
            <option value="paid">Paid</option>
            <option value="overdue">Overdue</option>
            <option value="cancelled">Cancelled</option>
        </select>
    </div>
</div>
<div class="bg-white rounded-xl border overflow-hidden"><div id="invTable">@include('portal.invoices.partials.table')</div></div>
@push('scripts')
<script>
let it;
function loadInvs() {
    const p = new URLSearchParams();
    if ($('#invSearch').val()) p.set('search', $('#invSearch').val());
    if ($('#invStatus').val()) p.set('status', $('#invStatus').val());
    $.ajax({ url: '{{ route('portal.invoices.index') }}?' + p.toString(), type: 'GET', dataType: 'json',
        success: function(r) { $('#invTable').html(r.html); } });
}
$('#invSearch').on('input', function() { clearTimeout(it); it = setTimeout(loadInvs, 300); });
$('#invStatus').on('change', loadInvs);
</script>
@endpush
@endsection
