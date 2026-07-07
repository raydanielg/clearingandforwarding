@extends('layouts.admin')
@section('title', 'Charge Items - ' . config('app.name'))
@section('page_title', 'Charge Items')

@section('content')
<div class="flex items-center justify-between mb-4">
    <div class="relative">
        <input type="text" id="chSearch" placeholder="Search charge items..." class="pl-9 pr-4 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none w-56">
        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
    </div>
    @can('charges.create')
    <button onclick="openChModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>Add Charge Item</button>
    @endcan
</div>
<div class="bg-white rounded-xl border overflow-hidden"><div id="chTable">@include('admin.finance.charges.partials.table')</div></div>

<div id="chModal" class="fixed inset-0 bg-black/40 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4" id="chModalTitle">Add Charge Item</h3>
        <form id="chForm" class="space-y-3">
            @csrf <input type="hidden" name="id" id="chId">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label><input type="text" name="name" id="chName" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none" required></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Default Price</label><input type="number" name="default_price" id="chPrice" step="0.01" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">VAT Rate %</label><input type="number" name="vat_rate" id="chVat" step="0.01" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            </div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Active</label><select name="is_active" id="chActive" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="1">Yes</option><option value="0">No</option></select></div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeChModal()" class="px-4 py-2 text-sm font-medium text-gray-600">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg">Save</button>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
let chT;$('#chSearch').on('input',function(){clearTimeout(chT);chT=setTimeout(function(){const p=new URLSearchParams();if($('#chSearch').val())p.set('search',$('#chSearch').val());$.ajax({url:'{{route('admin.charges.index')}}?'+p.toString(),type:'GET',dataType:'json',success:function(r){$('#chTable').html(r.html);}});},300);});
function openChModal(d){$('#chModal').removeClass('hidden').addClass('flex');if(d){$('#chModalTitle').text('Edit Charge Item');$('#chId').val(d.id);$('#chName').val(d.name);$('#chPrice').val(d.default_price);$('#chVat').val(d.vat_rate);$('#chActive').val(d.is_active?1:0);}else{$('#chModalTitle').text('Add Charge Item');$('#chForm')[0].reset();$('#chId').val('');}}
function closeChModal(){$('#chModal').addClass('hidden').removeClass('flex');}
function editCh(id,name,price,vat,active){openChModal({id,name,default_price:price,vat_rate:vat,is_active:active});}
function deleteCh(id){confirmDelete('{{route('admin.charges.destroy','__id__')}}'.replace('__id__',id),{title:'Delete Charge Item?',text:'This charge item will be permanently removed.'});}
$('#chForm').on('submit',function(e){e.preventDefault();const id=$('#chId').val();const url=id?'{{route('admin.charges.update','__id__')}}'.replace('__id__',id):'{{route('admin.charges.store')}}';let data=$(this).serialize();if(id)data+='&_method=PUT';$.ajax({url,type:'POST',data,success:function(r){if(r.success){showToast('success','Success',r.message);closeChModal();setTimeout(()=>location.reload(),800);}},error:function(x){if(x.responseJSON?.errors){Object.values(x.responseJSON.errors).forEach(e=>showToast('error','Validation',e[0]));}}});});
</script>
@endpush
@endsection
