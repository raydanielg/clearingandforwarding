@extends('layouts.admin')

@section('title', 'Insurers - ' . config('app.name', 'CAF'))
@section('page_title', 'Insurance Insurers')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-lg font-bold text-gray-900">Partner Insurers</h2>
        <p class="text-sm text-gray-500">Manage insurance underwriter companies</p>
    </div>
    @can('insurance.create')
    <button onclick="openInsurerModal()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Insurer
    </button>
    @endcan
</div>

<div class="bg-white rounded-xl border overflow-hidden">
    <div class="px-5 py-4 border-b flex items-center gap-3">
        <div class="flex-1 relative">
            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" id="insurerSearch" placeholder="Search insurers..." class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none">
        </div>
    </div>
    <div id="insurerTable">
        @include('admin.insurance.insurers.partials.table')
    </div>
</div>

{{-- Modal --}}
<div id="insurerModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">
        <div class="px-6 py-4 border-b flex items-center justify-between">
            <h3 id="insurerModalTitle" class="text-sm font-bold text-gray-900">Add Insurer</h3>
            <button onclick="closeInsurerModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form id="insurerForm" class="p-6 space-y-4">
            @csrf
            <input type="hidden" id="insurerId" name="id">
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Name *</label><input type="text" id="insurerName" name="name" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Code</label><input type="text" id="insurerCode" name="code" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Contact Person</label><input type="text" id="insurerContact" name="contact_person" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Phone</label><input type="text" id="insurerPhone" name="phone" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Email</label><input type="email" id="insurerEmail" name="email" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">License No</label><input type="text" id="insurerLicense" name="license_no" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            </div>
            <div><label class="block text-xs font-medium text-gray-700 mb-1">Address</label><textarea id="insurerAddress" name="address" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></textarea></div>
            <div><label class="flex items-center gap-2 text-sm text-gray-700"><input type="checkbox" id="insurerActive" name="is_active" value="1" checked class="rounded border-gray-300 text-emerald-600"> Active</label></div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeInsurerModal()" class="px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg">Save</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let insT;
$('#insurerSearch').on('input', function(){clearTimeout(insT);insT=setTimeout(function(){const p=new URLSearchParams();if($('#insurerSearch').val())p.set('search',$('#insurerSearch').val());$.ajax({url:'{{route('admin.insurance.insurers.index')}}?'+p.toString(),type:'GET',dataType:'json',success:function(r){$('#insurerTable').html(r.html);}});},300);});
function openInsurerModal(d){$('#insurerModal').removeClass('hidden').addClass('flex');if(d){$('#insurerModalTitle').text('Edit Insurer');$('#insurerId').val(d.id);$('#insurerName').val(d.name);$('#insurerCode').val(d.code);$('#insurerContact').val(d.contact_person);$('#insurerPhone').val(d.phone);$('#insurerEmail').val(d.email);$('#insurerLicense').val(d.license_no);$('#insurerAddress').val(d.address);$('#insurerActive').prop('checked',d.is_active);}else{$('#insurerModalTitle').text('Add Insurer');$('#insurerForm')[0].reset();$('#insurerId').val('');}}
function closeInsurerModal(){$('#insurerModal').addClass('hidden').removeClass('flex');}
function editInsurer(id,name,code,contact,phone,email,license,address,active){openInsurerModal({id,name,code,contact_person:contact,phone,email,license_no:license,address,is_active:active});}
function deleteInsurer(id){confirmDelete('{{route('admin.insurance.insurers.destroy','__id__')}}'.replace('__id__',id),{title:'Delete Insurer?',text:'This insurer will be permanently removed.'});}
$('#insurerForm').on('submit',function(e){e.preventDefault();const id=$('#insurerId').val();const url=id?'{{route('admin.insurance.insurers.update','__id__')}}'.replace('__id__',id):'{{route('admin.insurance.insurers.store')}}';let data=$(this).serialize();if(id)data+='&_method=PUT';$.ajax({url,type:'POST',data,success:function(r){if(r.success){showToast('success','Success',r.message);closeInsurerModal();setTimeout(()=>location.reload(),800);}},error:function(x){if(x.responseJSON?.errors){Object.values(x.responseJSON.errors).forEach(e=>showToast('error','Validation',e[0]));}}});});
</script>
@endpush
@endsection
