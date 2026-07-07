@extends('layouts.admin')
@section('title', 'New Receipt - ' . config('app.name'))
@section('page_title', 'New Receipt')

@section('content')
<form id="rctForm" action="{{ route('admin.receipts.store') }}" method="POST" class="bg-white rounded-xl border p-6 space-y-4 max-w-2xl">
    @csrf
    <div><label class="block text-sm font-medium text-gray-700 mb-1">Client <span class="text-red-500">*</span></label><select name="client_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none" required><option value="">Select...</option>@foreach($clients as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
    <div><label class="block text-sm font-medium text-gray-700 mb-1">Invoice (optional)</label><select name="invoice_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="">—</option>@foreach($invoices as $inv)<option value="{{ $inv->id }}">{{ $inv->invoice_no }} — {{ $inv->client?->name }} (Bal: {{ number_format($inv->total - $inv->amount_paid) }})</option>@endforeach</select></div>
    <div class="grid grid-cols-2 gap-4">
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Date <span class="text-red-500">*</span></label><input type="date" name="date" value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none" required></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Amount <span class="text-red-500">*</span></label><input type="number" name="amount" step="0.01" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none" required></div>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Method <span class="text-red-500">*</span></label><select name="method" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none" required><option value="bank">Bank</option><option value="cash">Cash</option><option value="mobile_money">Mobile Money</option><option value="cheque">Cheque</option></select></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Reference</label><input type="text" name="reference" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
    </div>
    <div class="flex items-center gap-3 pt-4 border-t">
        <button type="submit" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">Create Receipt</button>
        <a href="{{ route('admin.receipts.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800">Cancel</a>
    </div>
</form>
@push('scripts')
<script>
$('#rctForm').on('submit',function(e){e.preventDefault();const form=this;const btn=form.querySelector('button[type="submit"]');btn.disabled=true;btn.textContent='Saving...';$.ajax({url:form.action,type:'POST',data:new FormData(form),processData:false,contentType:false,success:function(r){if(r.success){showToast('success','Success',r.message);setTimeout(()=>window.location.href=r.redirect,800);}},error:function(x){btn.disabled=false;btn.textContent='Create Receipt';if(x.responseJSON?.errors){Object.values(x.responseJSON.errors).forEach(e=>showToast('error','Validation',e[0]));}else{showToast('error','Error','Something went wrong');}}});});
</script>
@endpush
@endsection
