@extends('layouts.admin')
@section('title', 'Petty Cash - ' . config('app.name'))
@section('page_title', 'Petty Cash')

@section('content')
<div class="flex items-center justify-between mb-4">
    <div class="bg-white rounded-xl border px-5 py-3">
        <p class="text-[10px] font-medium text-gray-500">Current Balance</p>
        <p class="text-xl font-bold {{ $balance >= 0 ? 'text-emerald-600' : 'text-red-600' }}">TZS {{ number_format($balance) }}</p>
    </div>
    @can('pettycash.create')
    <button onclick="openPcModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>New Entry</button>
    @endcan
</div>
<div class="bg-white rounded-xl border overflow-hidden"><div id="pcTable">@include('admin.finance.petty-cash.partials.table')</div></div>

<div id="pcModal" class="fixed inset-0 bg-black/40 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">New Petty Cash Entry</h3>
        <form id="pcForm" class="space-y-3">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Date</label><input type="date" name="date" value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none" required></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Type</label><select name="type" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="in">Cash In</option><option value="out">Cash Out</option></select></div>
            </div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Amount</label><input type="number" name="amount" step="0.01" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none" required></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Description</label><textarea name="description" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></textarea></div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closePcModal()" class="px-4 py-2 text-sm font-medium text-gray-600">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg">Save</button>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
function openPcModal(){$('#pcModal').removeClass('hidden').addClass('flex');}
function closePcModal(){$('#pcModal').addClass('hidden').removeClass('flex');}
function deletePc(id){if(!confirm('Delete this entry?'))return;$.ajax({url:'{{route('admin.petty-cash.destroy','__id__')}}'.replace('__id__',id),type:'POST',data:{_method:'DELETE',_token:'{{csrf_token()}}'},success:function(r){showToast('success','Deleted',r.message);setTimeout(()=>location.reload(),800);},error:function(){showToast('error','Error','Could not delete');}});}
$('#pcForm').on('submit',function(e){e.preventDefault();$.ajax({url:'{{route('admin.petty-cash.store')}}',type:'POST',data:$(this).serialize(),success:function(r){if(r.success){showToast('success','Success',r.message);closePcModal();setTimeout(()=>location.reload(),800);}},error:function(x){if(x.responseJSON?.errors){Object.values(x.responseJSON.errors).forEach(e=>showToast('error','Validation',e[0]));}}});});
</script>
@endpush
@endsection
