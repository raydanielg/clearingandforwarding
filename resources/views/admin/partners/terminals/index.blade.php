@extends('layouts.admin')
@section('title', 'Terminals / ICDs - ' . config('app.name'))
@section('page_title', 'ICDs / Terminals')

@section('content')
<div class="flex items-center justify-between mb-4">
    <div class="relative">
        <input type="text" id="termSearch" placeholder="Search..." class="pl-9 pr-4 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none w-56">
        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
    </div>
    @can('partners.create')
    <button onclick="openTermModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>Add Terminal</button>
    @endcan
</div>
<div class="bg-white rounded-xl border overflow-hidden"><div id="termTable">@include('admin.partners.terminals.partials.table')</div></div>

<div id="termModal" class="fixed inset-0 bg-black/40 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4" id="termModalTitle">Add Terminal</h3>
        <form id="termForm" class="space-y-3">
            @csrf <input type="hidden" name="id" id="termId">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label><input type="text" name="name" id="termName" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none" required></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Code</label><input type="text" name="code" id="termCode" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Location</label><input type="text" name="location" id="termLocation" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Contact</label><input type="text" name="contact_person" id="termContact" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Phone</label><input type="text" name="phone" id="termPhone" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            </div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Active</label><select name="is_active" id="termActive" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="1">Yes</option><option value="0">No</option></select></div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeTermModal()" class="px-4 py-2 text-sm font-medium text-gray-600">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg">Save</button>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
let tt;$('#termSearch').on('input',function(){clearTimeout(tt);tt=setTimeout(function(){const p=new URLSearchParams();if($('#termSearch').val())p.set('search',$('#termSearch').val());$.ajax({url:'{{route('admin.partners.terminals.index')}}?'+p.toString(),type:'GET',dataType:'json',success:function(r){$('#termTable').html(r.html);}});},300);});
function openTermModal(d){$('#termModal').removeClass('hidden').addClass('flex');if(d){$('#termModalTitle').text('Edit');$('#termId').val(d.id);$('#termName').val(d.name);$('#termCode').val(d.code);$('#termLocation').val(d.location);$('#termContact').val(d.contact_person);$('#termPhone').val(d.phone);$('#termActive').val(d.is_active?1:0);}else{$('#termModalTitle').text('Add Terminal');$('#termForm')[0].reset();$('#termId').val('');}}
function closeTermModal(){$('#termModal').addClass('hidden').removeClass('flex');}
function editTerm(id,name,code,loc,contact,phone,active){openTermModal({id,name,code,location:loc,contact_person:contact,phone,is_active:active});}
function deleteTerm(id){if(!confirm('Delete?'))return;$.ajax({url:'{{route('admin.partners.terminals.destroy','__id__')}}'.replace('__id__',id),type:'POST',data:{_method:'DELETE',_token:'{{csrf_token()}}'},success:function(r){showToast('success','Deleted',r.message);setTimeout(()=>location.reload(),800);},error:function(){showToast('error','Error','Could not delete');}});}
$('#termForm').on('submit',function(e){e.preventDefault();const id=$('#termId').val();const url=id?'{{route('admin.partners.terminals.update','__id__')}}'.replace('__id__',id):'{{route('admin.partners.terminals.store')}}';let data=$(this).serialize();if(id)data+='&_method=PUT';$.ajax({url,type:'POST',data,success:function(r){if(r.success){showToast('success','Success',r.message);closeTermModal();setTimeout(()=>location.reload(),800);}},error:function(x){if(x.responseJSON?.errors){Object.values(x.responseJSON.errors).forEach(e=>showToast('error','Validation',e[0]));}}});});
</script>
@endpush
@endsection
