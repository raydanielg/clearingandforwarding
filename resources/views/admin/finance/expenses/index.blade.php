@extends('layouts.admin')
@section('title', 'Expenses - ' . config('app.name'))
@section('page_title', 'Expenses')

@section('content')
<div class="flex items-center justify-between mb-4">
    <div class="bg-white rounded-xl border px-5 py-3">
        <p class="text-[10px] font-medium text-gray-500">This Month</p>
        <p class="text-xl font-bold text-red-600">TZS {{ number_format($monthTotal) }}</p>
    </div>
    @can('expenses.create')
    <button onclick="openExpModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>New Expense</button>
    @endcan
</div>
<div class="bg-white rounded-xl border overflow-hidden"><div id="expTable">@include('admin.finance.expenses.partials.table')</div></div>

<div id="expModal" class="fixed inset-0 bg-black/40 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">New Expense</h3>
        <form id="expForm" class="space-y-3">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Date</label><input type="date" name="date" value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none" required></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Category</label><input type="text" name="category" placeholder="e.g. Office, Fuel" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            </div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Amount</label><input type="number" name="amount" step="0.01" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none" required></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Description</label><textarea name="description" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></textarea></div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeExpModal()" class="px-4 py-2 text-sm font-medium text-gray-600">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg">Save</button>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
function openExpModal(){$('#expModal').removeClass('hidden').addClass('flex');}
function closeExpModal(){$('#expModal').addClass('hidden').removeClass('flex');}
function deleteExp(id){if(!confirm('Delete this expense?'))return;$.ajax({url:'{{route('admin.expenses.destroy','__id__')}}'.replace('__id__',id),type:'POST',data:{_method:'DELETE',_token:'{{csrf_token()}}'},success:function(r){showToast('success','Deleted',r.message);setTimeout(()=>location.reload(),800);},error:function(){showToast('error','Error','Could not delete');}});}
$('#expForm').on('submit',function(e){e.preventDefault();$.ajax({url:'{{route('admin.expenses.store')}}',type:'POST',data:$(this).serialize(),success:function(r){if(r.success){showToast('success','Success',r.message);closeExpModal();setTimeout(()=>location.reload(),800);}},error:function(x){if(x.responseJSON?.errors){Object.values(x.responseJSON.errors).forEach(e=>showToast('error','Validation',e[0]));}}});});
</script>
@endpush
@endsection
