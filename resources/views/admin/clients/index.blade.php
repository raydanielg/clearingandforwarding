@extends('layouts.admin')

@section('title', 'Clients - ' . config('app.name'))
@section('page_title', 'Clients')

@section('content')
<div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-3">
        <div class="relative">
            <input type="text" id="clientSearch" placeholder="Search clients..." class="pl-9 pr-4 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none w-64">
            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
        <select id="clientType" class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none">
            <option value="">All Types</option>
            <option value="company">Company</option>
            <option value="individual">Individual</option>
        </select>
    </div>
    @can('clients.create')
    <a href="{{ route('admin.clients.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Client
    </a>
    @endcan
</div>

<div class="bg-white rounded-xl border overflow-hidden">
    <div id="clientsTable">
        @include('admin.clients.partials.table')
    </div>
</div>

@push('scripts')
<script>
$(function() {
    let searchTimer;
    $('#clientSearch, #clientType').on('input change', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function() {
            const params = new URLSearchParams();
            const search = $('#clientSearch').val();
            const type = $('#clientType').val();
            if (search) params.set('search', search);
            if (type) params.set('type', type);

            $.ajax({
                url: '{{ route('admin.clients.index') }}?' + params.toString(),
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    $('#clientsTable').html(res.html);
                }
            });
        }, 300);
    });
});
</script>
@endpush
@endsection
