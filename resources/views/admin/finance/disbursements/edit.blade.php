@extends('layouts.admin')
@section('title', 'Edit Disbursement - ' . config('app.name'))
@section('page_title', 'Edit Disbursement')

@section('content')
<form id="disbEditForm" action="{{ route('admin.disbursements.update', $disbursement) }}" method="POST" class="bg-white rounded-xl border p-6 space-y-4 max-w-2xl">
    @csrf @method('PUT')
    <div class="grid grid-cols-2 gap-4">
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Agency</label><select name="agency_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="">—</option>@foreach($agencies as $a)<option value="{{ $a->id }}" @selected($disbursement->agency_id === $a->id)>{{ $a->name }}</option>@endforeach</select></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Payee</label><input type="text" name="payee" value="{{ old('payee', $disbursement->payee) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
    </div>
    <div><label class="block text-sm font-medium text-gray-700 mb-1">Description</label><textarea name="description" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none">{{ old('description', $disbursement->description) }}</textarea></div>
    <div class="grid grid-cols-3 gap-4">
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Amount</label><input type="number" name="amount" value="{{ old('amount', $disbursement->amount) }}" step="0.01" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none" required></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Method</label><select name="method" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="bank" @selected($disbursement->method==='bank')>Bank</option><option value="cash" @selected($disbursement->method==='cash')>Cash</option><option value="mobile_money" @selected($disbursement->method==='mobile_money')>Mobile Money</option><option value="cheque" @selected($disbursement->method==='cheque')>Cheque</option></select></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Reference No</label><input type="text" name="reference_no" value="{{ old('reference_no', $disbursement->reference_no) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
    </div>
    <div class="flex items-center gap-3 pt-4 border-t">
        <button type="submit" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">Update</button>
        <a href="{{ route('admin.disbursements.show', $disbursement) }}" class="px-4 py-2 text-sm font-medium text-gray-600">Cancel</a>
    </div>
</form>
@push('scripts')
<script>$('#disbEditForm').on('submit',function(e){e.preventDefault();const form=this;const btn=form.querySelector('button[type="submit"]');btn.disabled=true;btn.textContent='Saving...';$.ajax({url:form.action,type:'POST',data:new FormData(form),processData:false,contentType:false,success:function(r){if(r.success){showToast('success','Success',r.message);setTimeout(()=>window.location.href='{{route('admin.disbursements.show',$disbursement)}}',800);}},error:function(x){btn.disabled=false;btn.textContent='Update';if(x.responseJSON?.errors){Object.values(x.responseJSON.errors).forEach(e=>showToast('error','Validation',e[0]));}}});});</script>
@endpush
@endsection
