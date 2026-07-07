@extends('layouts.portal')

@section('title', 'My Insurance - ' . config('app.name', 'CAF'))
@section('page_title', 'My Insurance Policies')

@section('content')
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="px-5 py-4 border-b flex items-center gap-3 flex-wrap">
        <div class="flex-1 relative min-w-[200px]">
            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" id="policySearch" placeholder="Search policy no or file no..." class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none">
        </div>
        <select id="policyStatus" class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none">
            <option value="">All Statuses</option>
            <option value="quoted">Quoted</option>
            <option value="issued">Issued</option>
            <option value="active">Active</option>
            <option value="expired">Expired</option>
            <option value="cancelled">Cancelled</option>
            <option value="claimed">Claimed</option>
        </select>
    </div>
    <div id="policyTable">
        @include('portal.insurance.partials.table')
    </div>
</div>

@push('scripts')
<script>
let polT;
function loadPolicies(){const p=new URLSearchParams();if($('#policySearch').val())p.set('search',$('#policySearch').val());if($('#policyStatus').val())p.set('status',$('#policyStatus').val());$.ajax({url:'{{route('portal.insurance.index')}}?'+p.toString(),type:'GET',dataType:'json',success:function(r){$('#policyTable').html(r.html);}});}
$('#policySearch').on('input',function(){clearTimeout(polT);polT=setTimeout(loadPolicies,300);});
$('#policyStatus').on('change',loadPolicies);
</script>
@endpush
@endsection
