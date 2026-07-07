@extends('layouts.admin')
@section('title', 'Trips - ' . config('app.name'))
@section('page_title', 'Trips / Deliveries')

@section('content')
<div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-3">
        <div class="relative">
            <input type="text" id="tripSearch" placeholder="Search trips..." class="pl-9 pr-4 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none w-56">
            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
        <select id="tripStatus" class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="">All Status</option><option value="planned">Planned</option><option value="loading">Loading</option><option value="in_transit">In Transit</option><option value="delivered">Delivered</option><option value="closed">Closed</option></select>
    </div>
    @can('transport.create')
    <a href="{{ route('admin.transport.trips.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>New Trip</a>
    @endcan
</div>
<div class="bg-white rounded-xl border overflow-hidden"><div id="tripTable">@include('admin.transport.trips.partials.table')</div></div>
@push('scripts')
<script>let tt;$('#tripSearch,#tripStatus').on('input change',function(){clearTimeout(tt);tt=setTimeout(function(){const p=new URLSearchParams();if($('#tripSearch').val())p.set('search',$('#tripSearch').val());if($('#tripStatus').val())p.set('status',$('#tripStatus').val());$.ajax({url:'{{route('admin.transport.trips.index')}}?'+p.toString(),type:'GET',dataType:'json',success:function(r){$('#tripTable').html(r.html);}});},300);});</script>
@endpush
@endsection
