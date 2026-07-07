@extends('layouts.admin')
@section('title', 'New Declaration - ' . config('app.name'))
@section('page_title', 'New Declaration')

@section('content')
<form id="decForm" action="{{ route('admin.declarations.store') }}" method="POST" class="bg-white rounded-xl border p-6 space-y-4 max-w-4xl">
    @csrf
    <div class="grid grid-cols-3 gap-4">
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Shipment File <span class="text-red-500">*</span></label><select name="shipment_file_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none" required><option value="">Select file...</option>@foreach($files as $f)<option value="{{ $f->id }}">{{ $f->file_no }} — {{ $f->client?->name }}</option>@endforeach</select></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">TANSAD No</label><input type="text" name="tansad_no" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Regime Code</label><input type="text" name="regime_code" placeholder="e.g. IM4" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
    </div>
    <div><label class="block text-sm font-medium text-gray-700 mb-1">Notes</label><textarea name="notes" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></textarea></div>

    <div class="border-t pt-4">
        <h4 class="text-sm font-semibold text-gray-900 mb-2">Declaration Items</h4>
        <div id="itemsContainer" class="space-y-2"></div>
        <button type="button" onclick="addItemRow()" class="mt-2 text-xs font-medium text-emerald-600 hover:text-emerald-700">+ Add Item</button>
    </div>

    <div class="flex items-center gap-3 pt-4 border-t">
        <button type="submit" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">Create Declaration</button>
        <a href="{{ route('admin.declarations.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800">Cancel</a>
    </div>
</form>
@push('scripts')
<script>
let itemIdx = 0;
function addItemRow() {
    const html = `<div class="grid grid-cols-5 gap-2 items-start">
        <select name="items[${itemIdx}][hs_code_id]" class="px-2 py-1.5 text-xs rounded-lg border border-gray-200 focus:border-emerald-500 outline-none"><option value="">HS Code...</option>@foreach($hsCodes as $h)<option value="{{ $h->id }}">{{ $h->code }} — {{ $h->description }}</option>@endforeach</select>
        <input type="text" name="items[${itemIdx}][description]" placeholder="Description" class="px-2 py-1.5 text-xs rounded-lg border border-gray-200 focus:border-emerald-500 outline-none">
        <input type="number" name="items[${itemIdx}][qty]" placeholder="Qty" step="0.01" class="px-2 py-1.5 text-xs rounded-lg border border-gray-200 focus:border-emerald-500 outline-none">
        <input type="number" name="items[${itemIdx}][customs_value]" placeholder="Value" step="0.01" class="px-2 py-1.5 text-xs rounded-lg border border-gray-200 focus:border-emerald-500 outline-none">
        <input type="number" name="items[${itemIdx}][duty_rate]" placeholder="Duty %" step="0.01" class="px-2 py-1.5 text-xs rounded-lg border border-gray-200 focus:border-emerald-500 outline-none">
    </div>`;
    $('#itemsContainer').append(html); itemIdx++;
}
addItemRow();
$('#decForm').on('submit', function(e) {
    e.preventDefault(); const form = this; const btn = form.querySelector('button[type="submit"]');
    btn.disabled = true; btn.textContent = 'Saving...';
    $.ajax({ url: form.action, type: 'POST', data: new FormData(form), processData: false, contentType: false,
        success: function(r) { if (r.success) { showToast('success', 'Success', r.message); setTimeout(() => window.location.href = r.redirect, 800); } },
        error: function(x) { btn.disabled = false; btn.textContent = 'Create Declaration'; if (x.responseJSON?.errors) { Object.values(x.responseJSON.errors).forEach(e => showToast('error', 'Validation', e[0])); } else { showToast('error', 'Error', 'Something went wrong'); } }
    });
});
</script>
@endpush
@endsection
