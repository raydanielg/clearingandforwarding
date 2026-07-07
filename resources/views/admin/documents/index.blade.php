@extends('layouts.admin')
@section('title', 'Documents - ' . config('app.name'))
@section('page_title', 'Document Register')

@section('content')
<div class="flex items-center justify-between mb-4">
    <div class="relative">
        <input type="text" id="docSearch" placeholder="Search documents..." class="pl-9 pr-4 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none w-64">
        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
    </div>
    @can('documents.upload')
    <a href="{{ route('admin.documents.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>Upload Document
    </a>
    @endcan
</div>
<div class="bg-white rounded-xl border overflow-hidden"><div id="docTable">@include('admin.documents.partials.table')</div></div>
@push('scripts')
<script>let docT;$('#docSearch').on('input',function(){clearTimeout(docT);docT=setTimeout(function(){const p=new URLSearchParams();if($('#docSearch').val())p.set('search',$('#docSearch').val());$.ajax({url:'{{route('admin.documents.index')}}?'+p.toString(),type:'GET',dataType:'json',success:function(r){$('#docTable').html(r.html);}});},300);});</script>
@endpush
@endsection
