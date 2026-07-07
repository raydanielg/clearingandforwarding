@extends('layouts.admin')

@section('title', 'Edit File - ' . config('app.name'))
@section('page_title', 'Edit: ' . $file->file_no)

@section('content')
<form id="fileEditForm" action="{{ route('admin.files.update', $file) }}" method="POST" class="bg-white rounded-xl border p-6 space-y-4 max-w-4xl">
    @csrf @method('PUT')
    <div class="grid grid-cols-3 gap-4">
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Client <span class="text-red-500">*</span></label><select name="client_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none" required>@foreach($clients as $c)<option value="{{ $c->id }}" @selected($file->client_id === $c->id)>{{ $c->name }} ({{ $c->code }})</option>@endforeach</select></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Type</label><select name="type" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="import" @selected($file->type === 'import')>Import</option><option value="export" @selected($file->type === 'export')>Export</option><option value="transit" @selected($file->type === 'transit')>Transit</option></select></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Mode</label><select name="mode" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="sea" @selected($file->mode === 'sea')>Sea</option><option value="air" @selected($file->mode === 'air')>Air</option><option value="road" @selected($file->mode === 'road')>Road</option></select></div>
    </div>
    <div class="grid grid-cols-3 gap-4">
        <div><label class="block text-sm font-medium text-gray-700 mb-1">BL / AWB No</label><input type="text" name="bl_awb_no" value="{{ old('bl_awb_no', $file->bl_awb_no) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Vessel / Flight</label><input type="text" name="vessel_flight" value="{{ old('vessel_flight', $file->vessel_flight) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Shipping Line</label><select name="shipping_line_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="">—</option>@foreach($shippingLines as $l)<option value="{{ $l->id }}" @selected($file->shipping_line_id === $l->id)>{{ $l->name }}</option>@endforeach</select></div>
    </div>
    <div class="grid grid-cols-3 gap-4">
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Port of Loading</label><input type="text" name="port_of_loading" value="{{ old('port_of_loading', $file->port_of_loading) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Port of Discharge</label><input type="text" name="port_of_discharge" value="{{ old('port_of_discharge', $file->port_of_discharge) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Terminal / ICD</label><select name="terminal_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="">—</option>@foreach($terminals as $t)<option value="{{ $t->id }}" @selected($file->terminal_id === $t->id)>{{ $t->name }}</option>@endforeach</select></div>
    </div>
    <div class="grid grid-cols-4 gap-4">
        <div><label class="block text-sm font-medium text-gray-700 mb-1">ETA</label><input type="date" name="eta" value="{{ old('eta', $file->eta?->format('Y-m-d')) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Discharge Date</label><input type="date" name="discharge_date" value="{{ old('discharge_date', $file->discharge_date?->format('Y-m-d')) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Free Days Expiry</label><input type="date" name="free_days_expiry" value="{{ old('free_days_expiry', $file->free_days_expiry?->format('Y-m-d')) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Currency</label><select name="currency" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option @selected($file->currency === 'TZS')>TZS</option><option @selected($file->currency === 'USD')>USD</option><option @selected($file->currency === 'EUR')>EUR</option></select></div>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Declaration Officer</label><select name="assigned_declaration_officer_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="">—</option>@foreach($officers as $o)<option value="{{ $o->id }}" @selected($file->assigned_declaration_officer_id === $o->id)>{{ $o->name }}</option>@endforeach</select></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Clearing Officer</label><select name="assigned_clearing_officer_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="">—</option>@foreach($officers as $o)<option value="{{ $o->id }}" @selected($file->assigned_clearing_officer_id === $o->id)>{{ $o->name }}</option>@endforeach</select></div>
    </div>
    <div><label class="block text-sm font-medium text-gray-700 mb-1">Remarks</label><textarea name="remarks" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none">{{ old('remarks', $file->remarks) }}</textarea></div>
    <div class="flex items-center gap-3 pt-4 border-t">
        <button type="submit" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">Update File</button>
        <a href="{{ route('admin.files.show', $file) }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800">Cancel</a>
    </div>
</form>
@push('scripts')
<script>
$('#fileEditForm').on('submit', function(e) {
    e.preventDefault(); const form = this; const btn = form.querySelector('button[type="submit"]');
    btn.disabled = true; btn.textContent = 'Saving...';
    $.ajax({ url: form.action, type: 'POST', data: new FormData(form), processData: false, contentType: false,
        success: function(r) { if (r.success) { showToast('success', 'Success', r.message); setTimeout(() => window.location.href = '{{ route('admin.files.show', $file) }}', 800); } },
        error: function(x) { btn.disabled = false; btn.textContent = 'Update File'; if (x.responseJSON?.errors) { Object.values(x.responseJSON.errors).forEach(e => showToast('error', 'Validation', e[0])); } else { showToast('error', 'Error', 'Something went wrong'); } }
    });
});
</script>
@endpush
@endsection
