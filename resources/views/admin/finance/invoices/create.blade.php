@extends('layouts.admin')
@section('title', 'New Invoice - ' . config('app.name'))
@section('page_title', 'New Invoice')

@section('content')
<form id="invForm" action="{{ route('admin.invoices.store') }}" method="POST" class="bg-white rounded-xl border p-6 space-y-4 max-w-4xl">
    @csrf
    <div class="grid grid-cols-4 gap-4">
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Client <span class="text-red-500">*</span></label><select name="client_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none" required><option value="">Select...</option>@foreach($clients as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">File (optional)</label><select name="shipment_file_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="">—</option>@foreach($files as $f)<option value="{{ $f->id }}">{{ $f->file_no }} — {{ $f->client?->name }}</option>@endforeach</select></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Date <span class="text-red-500">*</span></label><input type="date" name="date" value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none" required></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label><input type="date" name="due_date" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
    </div>
    <div class="grid grid-cols-3 gap-4">
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Kind</label><select name="kind" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="invoice">Invoice</option><option value="proforma">Proforma</option></select></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Currency</label><select name="currency" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option>TZS</option><option>USD</option><option>EUR</option></select></div>
    </div>

    <div class="border-t pt-4">
        <h4 class="text-sm font-semibold text-gray-900 mb-2">Line Items</h4>
        <div id="invItems" class="space-y-2"></div>
        <button type="button" onclick="addInvItem()" class="mt-2 text-xs font-medium text-emerald-600 hover:text-emerald-700">+ Add Line</button>
    </div>

    <div class="flex items-center gap-3 pt-4 border-t">
        <button type="submit" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">Create Invoice</button>
        <a href="{{ route('admin.invoices.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800">Cancel</a>
    </div>
</form>
@push('scripts')
<script>
let ii=0;
function addInvItem(){
    const html=`<div class="grid grid-cols-12 gap-2 items-start">
        <select name="items[${ii}][charge_item_id]" class="col-span-3 px-2 py-1.5 text-xs rounded-lg border border-gray-200 focus:border-emerald-500 outline-none"><option value="">Charge...</option>@foreach($chargeItems as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select>
        <input type="text" name="items[${ii}][description]" placeholder="Description" class="col-span-4 px-2 py-1.5 text-xs rounded-lg border border-gray-200 focus:border-emerald-500 outline-none">
        <input type="number" name="items[${ii}][qty]" placeholder="Qty" step="0.01" class="col-span-1 px-2 py-1.5 text-xs rounded-lg border border-gray-200 focus:border-emerald-500 outline-none">
        <input type="number" name="items[${ii}][unit_price]" placeholder="Price" step="0.01" class="col-span-2 px-2 py-1.5 text-xs rounded-lg border border-gray-200 focus:border-emerald-500 outline-none">
        <input type="number" name="items[${ii}][vat_rate]" placeholder="VAT%" step="0.01" class="col-span-2 px-2 py-1.5 text-xs rounded-lg border border-gray-200 focus:border-emerald-500 outline-none">
    </div>`;
    $('#invItems').append(html); ii++;
}
addInvItem();
$('#invForm').on('submit',function(e){e.preventDefault();const form=this;const btn=form.querySelector('button[type="submit"]');btn.disabled=true;btn.textContent='Saving...';$.ajax({url:form.action,type:'POST',data:new FormData(form),processData:false,contentType:false,success:function(r){if(r.success){showToast('success','Success',r.message);setTimeout(()=>window.location.href=r.redirect,800);}},error:function(x){btn.disabled=false;btn.textContent='Create Invoice';if(x.responseJSON?.errors){Object.values(x.responseJSON.errors).forEach(e=>showToast('error','Validation',e[0]));}else{showToast('error','Error','Something went wrong');}}});});
</script>
@endpush
@endsection
