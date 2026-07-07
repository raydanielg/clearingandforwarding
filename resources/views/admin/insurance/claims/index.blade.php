@extends('layouts.admin')

@section('title', 'Insurance Claims - ' . config('app.name', 'CAF'))
@section('page_title', 'Insurance Claims')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-lg font-bold text-gray-900">Insurance Claims</h2>
        <p class="text-sm text-gray-500">Manage all insurance claims</p>
    </div>
    @can('insurance.create')
    <a href="{{ route('admin.insurance.claims.create') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Claim
    </a>
    @endcan
</div>

<div class="bg-white rounded-xl border overflow-hidden">
    <div class="px-5 py-4 border-b flex items-center gap-3 flex-wrap">
        <div class="flex-1 relative min-w-[200px]">
            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" id="claimSearch" placeholder="Search claim no, client, policy..." class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none">
        </div>
        <select id="claimStatus" class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none">
            <option value="">All Statuses</option>
            <option value="submitted">Submitted</option>
            <option value="under_review">Under Review</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
            <option value="settled">Settled</option>
        </select>
    </div>
    <div id="claimTable">
        @include('admin.insurance.claims.partials.table')
    </div>
</div>

@push('scripts')
<script>
let clmT;
function loadClaims(){const p=new URLSearchParams();if($('#claimSearch').val())p.set('search',$('#claimSearch').val());if($('#claimStatus').val())p.set('status',$('#claimStatus').val());$.ajax({url:'{{route('admin.insurance.claims.index')}}?'+p.toString(),type:'GET',dataType:'json',success:function(r){$('#claimTable').html(r.html);}});}
$('#claimSearch').on('input',function(){clearTimeout(clmT);clmT=setTimeout(loadClaims,300);});
$('#claimStatus').on('change',loadClaims);
function deleteClaim(id){confirmDelete('{{route('admin.insurance.claims.destroy','__id__')}}'.replace('__id__',id),{title:'Delete Claim?',text:'This insurance claim will be permanently removed.'});}
</script>
@endpush
@endsection
