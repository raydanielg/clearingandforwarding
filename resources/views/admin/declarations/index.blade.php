@extends('layouts.admin')
@section('title', 'Declarations - ' . config('app.name'))
@section('page_title', 'Customs Declarations')

@section('content')
<div class="flex items-center justify-between mb-4">
    <div class="relative">
        <input type="text" id="decSearch" placeholder="Search TANSAD / File No..." class="pl-9 pr-4 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none w-64">
        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
    </div>
    @can('declarations.create')
    <a href="{{ route('admin.declarations.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>New Declaration
    </a>
    @endcan
</div>
<div class="bg-white rounded-xl border overflow-hidden"><div id="decTable">@include('admin.declarations.partials.table')</div></div>
@push('scripts')
<script>let dt;$('#decSearch').on('input', function(){clearTimeout(dt);dt=setTimeout(function(){const p=new URLSearchParams();if($('#decSearch').val())p.set('search',$('#decSearch').val());$.ajax({url:'{{route('admin.declarations.index')}}?'+p.toString(),type:'GET',dataType:'json',success:function(r){$('#decTable').html(r.html);}});},300);});</script>
@endpush
@endsection
