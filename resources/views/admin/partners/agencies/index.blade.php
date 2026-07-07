@extends('layouts.admin')
@section('title', 'Government Agencies - ' . config('app.name'))
@section('page_title', 'Government Agencies')

@section('content')
<div class="flex items-center justify-between mb-4">
    <div class="relative">
        <input type="text" id="agSearch" placeholder="Search..." class="pl-9 pr-4 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none w-56">
        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
    </div>
    @can('partners.create')
    <button onclick="openAgModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>Add Agency</button>
    @endcan
</div>
<div class="bg-white rounded-xl border overflow-hidden"><div id="agTable">@include('admin.partners.agencies.partials.table')</div></div>

<div id="agModal" class="fixed inset-0 bg-black/40 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4" id="agModalTitle">Add Agency</h3>
        <form id="agForm" class="space-y-3">
            @csrf <input type="hidden" name="id" id="agId">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label><input type="text" name="name" id="agName" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none" required></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Code</label><input type="text" name="code" id="agCode" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Contact</label><input type="text" name="contact_person" id="agContact" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Phone</label><input type="text" name="phone" id="agPhone" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            </div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Active</label><select name="is_active" id="agActive" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="1">Yes</option><option value="0">No</option></select></div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeAgModal()" class="px-4 py-2 text-sm font-medium text-gray-600">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg">Save</button>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
let at;$('#agSearch').on('input',function(){clearTimeout(at);at=setTimeout(function(){const p=new URLSearchParams();if($('#agSearch').val())p.set('search',$('#agSearch').val());$.ajax({url:'{{route('admin.partners.agencies.index')}}?'+p.toString(),type:'GET',dataType:'json',success:function(r){$('#agTable').html(r.html);}});},300);});
function openAgModal(d){$('#agModal').removeClass('hidden').addClass('flex');if(d){$('#agModalTitle').text('Edit');$('#agId').val(d.id);$('#agName').val(d.name);$('#agCode').val(d.code);$('#agContact').val(d.contact_person);$('#agPhone').val(d.phone);$('#agActive').val(d.is_active?1:0);}else{$('#agModalTitle').text('Add Agency');$('#agForm')[0].reset();$('#agId').val('');}}
function closeAgModal(){$('#agModal').addClass('hidden').removeClass('flex');}
function editAg(id,name,code,contact,phone,active){openAgModal({id,name,code,contact_person:contact,phone,is_active:active});}
function deleteAg(id){confirmDelete('{{route('admin.partners.agencies.destroy','__id__')}}'.replace('__id__',id),{title:'Delete Agency?',text:'This government agency will be permanently removed.'});}
$('#agForm').on('submit',function(e){e.preventDefault();const id=$('#agId').val();const url=id?'{{route('admin.partners.agencies.update','__id__')}}'.replace('__id__',id):'{{route('admin.partners.agencies.store')}}';let data=$(this).serialize();if(id)data+='&_method=PUT';$.ajax({url,type:'POST',data,success:function(r){if(r.success){showToast('success','Success',r.message);closeAgModal();setTimeout(()=>location.reload(),800);}},error:function(x){if(x.responseJSON?.errors){Object.values(x.responseJSON.errors).forEach(e=>showToast('error','Validation',e[0]));}}});});
</script>
@endpush
@endsection
