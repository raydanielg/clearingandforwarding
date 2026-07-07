@extends('layouts.admin')
@section('title', 'Invoices - ' . config('app.name'))
@section('page_title', 'Invoices')

@section('content')
<div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-3">
        <div class="relative">
            <input type="text" id="invSearch" placeholder="Search invoice no / client..." class="pl-9 pr-4 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none w-56">
            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
        <select id="invStatus" class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="">All Status</option><option value="draft">Draft</option><option value="sent">Sent</option><option value="partially_paid">Partially Paid</option><option value="paid">Paid</option><option value="overdue">Overdue</option><option value="cancelled">Cancelled</option></select>
    </div>
    @can('invoices.create')
    <a href="{{ route('admin.invoices.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>New Invoice</a>
    @endcan
</div>
<div class="bg-white rounded-xl border overflow-hidden"><div id="invTable">@include('admin.finance.invoices.partials.table')</div></div>
@push('scripts')
<script>let it;$('#invSearch,#invStatus').on('input change',function(){clearTimeout(it);it=setTimeout(function(){const p=new URLSearchParams();if($('#invSearch').val())p.set('search',$('#invSearch').val());if($('#invStatus').val())p.set('status',$('#invStatus').val());$.ajax({url:'{{route('admin.invoices.index')}}?'+p.toString(),type:'GET',dataType:'json',success:function(r){$('#invTable').html(r.html);}});},300);});</script>
@endpush
@endsection
