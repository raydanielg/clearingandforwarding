@extends('layouts.admin')
@section('title', 'Drivers - ' . config('app.name'))
@section('page_title', 'Drivers')

@section('content')
<div class="flex items-center justify-between mb-4">
    <div class="relative">
        <input type="text" id="driverSearch" placeholder="Search name / phone..." class="pl-9 pr-4 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none w-56">
        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
    </div>
    @can('transport.create')
    <button onclick="openDriverModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>Add Driver</button>
    @endcan
</div>
<div class="bg-white rounded-xl border overflow-hidden"><div id="driverTable">@include('admin.transport.drivers.partials.table')</div></div>

<div id="driverModal" class="fixed inset-0 bg-black/40 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4" id="driverModalTitle">Add Driver</h3>
        <form id="driverForm" class="space-y-3">
            @csrf <input type="hidden" name="id" id="drvId">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label><input type="text" name="name" id="drvName" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none" required></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Phone</label><input type="text" name="phone" id="drvPhone" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">License No</label><input type="text" name="license_no" id="drvLicense" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">License Expiry</label><input type="date" name="license_expiry" id="drvExpiry" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Active</label><select name="is_active" id="drvActive" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="1">Yes</option><option value="0">No</option></select></div>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeDriverModal()" class="px-4 py-2 text-sm font-medium text-gray-600">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg">Save</button>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
let drvT;
$('#driverSearch').on('input',function(){clearTimeout(drvT);drvT=setTimeout(function(){const p=new URLSearchParams();if($('#driverSearch').val())p.set('search',$('#driverSearch').val());$.ajax({url:'{{route('admin.transport.drivers.index')}}?'+p.toString(),type:'GET',dataType:'json',success:function(r){$('#driverTable').html(r.html);}});},300);});
function openDriverModal(d){$('#driverModal').removeClass('hidden').addClass('flex');if(d){$('#driverModalTitle').text('Edit Driver');$('#drvId').val(d.id);$('#drvName').val(d.name);$('#drvPhone').val(d.phone);$('#drvLicense').val(d.license_no);$('#drvExpiry').val(d.license_expiry);$('#drvActive').val(d.is_active?1:0);}else{$('#driverModalTitle').text('Add Driver');$('#driverForm')[0].reset();$('#drvId').val('');}}
function closeDriverModal(){$('#driverModal').addClass('hidden').removeClass('flex');}
function editDriver(id,name,phone,lic,exp,active){openDriverModal({id,name,phone,license_no:lic,license_expiry:exp,is_active:active});}
function deleteDriver(id){confirmDelete('{{route('admin.transport.drivers.destroy','__id__')}}'.replace('__id__',id),{title:'Delete Driver?',text:'This driver will be permanently removed.'});}
$('#driverForm').on('submit',function(e){e.preventDefault();const id=$('#drvId').val();const url=id?'{{route('admin.transport.drivers.update','__id__')}}'.replace('__id__',id):'{{route('admin.transport.drivers.store')}}';let data=$(this).serialize();if(id)data+='&_method=PUT';$.ajax({url,type:'POST',data,success:function(r){if(r.success){showToast('success','Success',r.message);closeDriverModal();setTimeout(()=>location.reload(),800);}},error:function(x){if(x.responseJSON?.errors){Object.values(x.responseJSON.errors).forEach(e=>showToast('error','Validation',e[0]));}}});});
</script>
@endpush
@endsection
