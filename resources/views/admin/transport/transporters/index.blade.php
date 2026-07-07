@extends('layouts.admin')
@section('title', 'Transporters - ' . config('app.name'))
@section('page_title', 'Transporters')

@section('content')
<div class="flex items-center justify-between mb-4">
    <div class="relative">
        <input type="text" id="tpSearch" placeholder="Search name / TIN..." class="pl-9 pr-4 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none w-56">
        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
    </div>
    @can('transport.create')
    <button onclick="openTpModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>Add Transporter</button>
    @endcan
</div>
<div class="bg-white rounded-xl border overflow-hidden"><div id="tpTable">@include('admin.transport.transporters.partials.table')</div></div>

<div id="tpModal" class="fixed inset-0 bg-black/40 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4" id="tpModalTitle">Add Transporter</h3>
        <form id="tpForm" class="space-y-3">
            @csrf <input type="hidden" name="id" id="tpId">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label><input type="text" name="name" id="tpName" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none" required></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">TIN</label><input type="text" name="tin" id="tpTin" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Phone</label><input type="text" name="phone" id="tpPhone" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            </div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Contact Person</label><input type="text" name="contact_person" id="tpContact" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Rate Notes</label><textarea name="rate_notes" id="tpRate" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></textarea></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Active</label><select name="is_active" id="tpActive" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="1">Yes</option><option value="0">No</option></select></div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeTpModal()" class="px-4 py-2 text-sm font-medium text-gray-600">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg">Save</button>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
let tpT;
$('#tpSearch').on('input',function(){clearTimeout(tpT);tpT=setTimeout(function(){const p=new URLSearchParams();if($('#tpSearch').val())p.set('search',$('#tpSearch').val());$.ajax({url:'{{route('admin.transport.transporters.index')}}?'+p.toString(),type:'GET',dataType:'json',success:function(r){$('#tpTable').html(r.html);}});},300);});
function openTpModal(d){$('#tpModal').removeClass('hidden').addClass('flex');if(d){$('#tpModalTitle').text('Edit Transporter');$('#tpId').val(d.id);$('#tpName').val(d.name);$('#tpTin').val(d.tin);$('#tpPhone').val(d.phone);$('#tpContact').val(d.contact_person);$('#tpRate').val(d.rate_notes);$('#tpActive').val(d.is_active?1:0);}else{$('#tpModalTitle').text('Add Transporter');$('#tpForm')[0].reset();$('#tpId').val('');}}
function closeTpModal(){$('#tpModal').addClass('hidden').removeClass('flex');}
function editTp(id,name,tin,phone,contact,rate,active){openTpModal({id,name,tin,phone,contact_person:contact,rate_notes:rate,is_active:active});}
function deleteTp(id){confirmDelete('{{route('admin.transport.transporters.destroy','__id__')}}'.replace('__id__',id),{title:'Delete Transporter?',text:'This transporter will be permanently removed.'});}
$('#tpForm').on('submit',function(e){e.preventDefault();const id=$('#tpId').val();const url=id?'{{route('admin.transport.transporters.update','__id__')}}'.replace('__id__',id):'{{route('admin.transport.transporters.store')}}';let data=$(this).serialize();if(id)data+='&_method=PUT';$.ajax({url,type:'POST',data,success:function(r){if(r.success){showToast('success','Success',r.message);closeTpModal();setTimeout(()=>location.reload(),800);}},error:function(x){if(x.responseJSON?.errors){Object.values(x.responseJSON.errors).forEach(e=>showToast('error','Validation',e[0]));}}});});
</script>
@endpush
@endsection
