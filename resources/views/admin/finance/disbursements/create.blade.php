@extends('layouts.admin')
@section('title', 'New Disbursement - ' . config('app.name'))
@section('page_title', 'New Disbursement')

@section('content')
<form id="disbForm" action="{{ route('admin.disbursements.store') }}" method="POST" class="bg-white rounded-xl border p-6 space-y-4 max-w-2xl">
    @csrf
    <div><label class="block text-sm font-medium text-gray-700 mb-1">Shipment File <span class="text-red-500">*</span></label><select name="shipment_file_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none" required><option value="">Select file...</option>@foreach($files as $f)<option value="{{ $f->id }}">{{ $f->file_no }} — {{ $f->client?->name }}</option>@endforeach</select></div>
    <div class="grid grid-cols-2 gap-4">
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Agency</label><select name="agency_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="">—</option>@foreach($agencies as $a)<option value="{{ $a->id }}">{{ $a->name }}</option>@endforeach</select></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Payee</label><input type="text" name="payee" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
    </div>
    <div><label class="block text-sm font-medium text-gray-700 mb-1">Description</label><textarea name="description" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></textarea></div>
    <div class="grid grid-cols-3 gap-4">
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Amount <span class="text-red-500">*</span></label><input type="number" name="amount" step="0.01" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none" required></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Method <span class="text-red-500">*</span></label><select name="method" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none" required><option value="bank">Bank</option><option value="cash">Cash</option><option value="mobile_money">Mobile Money</option><option value="cheque">Cheque</option></select></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Reference No</label><input type="text" name="reference_no" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
    </div>
    <div class="flex items-center gap-3 pt-4 border-t">
        <button type="submit" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">Record Disbursement</button>
        <a href="{{ route('admin.disbursements.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800">Cancel</a>
    </div>
</form>
@push('scripts')
<script>
$('#disbForm').on('submit',function(e){e.preventDefault();const form=this;const btn=form.querySelector('button[type="submit"]');btn.disabled=true;btn.textContent='Saving...';$.ajax({url:form.action,type:'POST',data:new FormData(form),processData:false,contentType:false,success:function(r){if(r.success){showToast('success','Success',r.message);setTimeout(()=>window.location.href=r.redirect,800);}},error:function(x){btn.disabled=false;btn.textContent='Record Disbursement';if(x.responseJSON?.errors){Object.values(x.responseJSON.errors).forEach(e=>showToast('error','Validation',e[0]));}else{showToast('error','Error','Something went wrong');}}});});
</script>
@endpush
@endsection
