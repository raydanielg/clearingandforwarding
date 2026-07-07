@extends('layouts.admin')
@section('title', 'Trucks - ' . config('app.name'))
@section('page_title', 'Trucks')

@section('content')
<div class="flex items-center justify-between mb-4">
    <div class="relative">
        <input type="text" id="truckSearch" placeholder="Search plate / trailer..." class="pl-9 pr-4 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none w-56">
        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
    </div>
    @can('transport.create')
    <button onclick="openTruckModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>Add Truck</button>
    @endcan
</div>
<div class="bg-white rounded-xl border overflow-hidden"><div id="truckTable">@include('admin.transport.trucks.partials.table')</div></div>

<div id="truckModal" class="fixed inset-0 bg-black/40 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4" id="truckModalTitle">Add Truck</h3>
        <form id="truckForm" class="space-y-3">
            @csrf <input type="hidden" name="id" id="truckId">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Plate No <span class="text-red-500">*</span></label><input type="text" name="plate_no" id="truckPlate" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none" required></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Trailer No</label><input type="text" name="trailer_no" id="truckTrailer" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Transporter</label><select name="transporter_id" id="truckTransporter" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="">—</option>@foreach($transporters as $tp)<option value="{{ $tp->id }}">{{ $tp->name }}</option>@endforeach</select></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Capacity (tons)</label><input type="number" name="capacity_tons" id="truckCapacity" step="0.01" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Active</label><select name="is_active" id="truckActive" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="1">Yes</option><option value="0">No</option></select></div>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeTruckModal()" class="px-4 py-2 text-sm font-medium text-gray-600">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg">Save</button>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
let truckT;
$('#truckSearch').on('input', function(){clearTimeout(truckT);truckT=setTimeout(function(){const p=new URLSearchParams();if($('#truckSearch').val())p.set('search',$('#truckSearch').val());$.ajax({url:'{{route('admin.transport.trucks.index')}}?'+p.toString(),type:'GET',dataType:'json',success:function(r){$('#truckTable').html(r.html);}});},300);});
function openTruckModal(d){$('#truckModal').removeClass('hidden').addClass('flex');if(d){$('#truckModalTitle').text('Edit Truck');$('#truckId').val(d.id);$('#truckPlate').val(d.plate_no);$('#truckTrailer').val(d.trailer_no);$('#truckCapacity').val(d.capacity_tons);$('#truckActive').val(d.is_active?1:0);$('#truckTransporter').val(d.transporter_id||'');}else{$('#truckModalTitle').text('Add Truck');$('#truckForm')[0].reset();$('#truckId').val('');}}
function closeTruckModal(){$('#truckModal').addClass('hidden').removeClass('flex');}
function editTruck(id,plate,trailer,cap,active,tpId){openTruckModal({id,plate_no:plate,trailer_no:trailer,capacity_tons:cap,is_active:active,transporter_id:tpId});}
function deleteTruck(id){confirmDelete('{{route('admin.transport.trucks.destroy','__id__')}}'.replace('__id__',id),{title:'Delete Truck?',text:'This truck will be permanently removed from the fleet.'});}
$('#truckForm').on('submit',function(e){e.preventDefault();const id=$('#truckId').val();const url=id?'{{route('admin.transport.trucks.update','__id__')}}'.replace('__id__',id):'{{route('admin.transport.trucks.store')}}';let data=$(this).serialize();if(id)data+='&_method=PUT';$.ajax({url,type:'POST',data,success:function(r){if(r.success){showToast('success','Success',r.message);closeTruckModal();setTimeout(()=>location.reload(),800);}},error:function(x){if(x.responseJSON?.errors){Object.values(x.responseJSON.errors).forEach(e=>showToast('error','Validation',e[0]));}}});});
</script>
@endpush
@endsection
