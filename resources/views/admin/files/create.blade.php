@extends('layouts.admin')

@section('title', 'Open New File - ' . config('app.name'))
@section('page_title', 'Open New File')

@section('content')
<form id="fileForm" action="{{ route('admin.files.store') }}" method="POST" class="bg-white rounded-xl border p-6 space-y-4 max-w-4xl">
    @csrf
    <div class="grid grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Client <span class="text-red-500">*</span></label>
            <select name="client_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none" required>
                <option value="">Select client...</option>
                @foreach($clients as $c)<option value="{{ $c->id }}">{{ $c->name }} ({{ $c->code }})</option>@endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Type <span class="text-red-500">*</span></label>
            <select name="type" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none" required>
                <option value="import">Import</option><option value="export">Export</option><option value="transit">Transit</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Mode <span class="text-red-500">*</span></label>
            <select name="mode" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none" required>
                <option value="sea">Sea</option><option value="air">Air</option><option value="road">Road</option>
            </select>
        </div>
    </div>
    <div class="grid grid-cols-3 gap-4">
        <div><label class="block text-sm font-medium text-gray-700 mb-1">BL / AWB No</label><input type="text" name="bl_awb_no" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Vessel / Flight</label><input type="text" name="vessel_flight" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Shipping Line</label><select name="shipping_line_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="">—</option>@foreach($shippingLines as $l)<option value="{{ $l->id }}">{{ $l->name }}</option>@endforeach</select></div>
    </div>
    <div class="grid grid-cols-3 gap-4">
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Port of Loading</label><input type="text" name="port_of_loading" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Port of Discharge</label><input type="text" name="port_of_discharge" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Terminal / ICD</label><select name="terminal_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="">—</option>@foreach($terminals as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach</select></div>
    </div>
    <div class="grid grid-cols-4 gap-4">
        <div><label class="block text-sm font-medium text-gray-700 mb-1">ETA</label><input type="date" name="eta" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Discharge Date</label><input type="date" name="discharge_date" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Free Days Expiry</label><input type="date" name="free_days_expiry" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Currency</label><select name="currency" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option>TZS</option><option>USD</option><option>EUR</option></select></div>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Declaration Officer</label><select name="assigned_declaration_officer_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="">—</option>@foreach($officers as $o)<option value="{{ $o->id }}">{{ $o->name }}</option>@endforeach</select></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Clearing Officer</label><select name="assigned_clearing_officer_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="">—</option>@foreach($officers as $o)<option value="{{ $o->id }}">{{ $o->name }}</option>@endforeach</select></div>
    </div>
    <div><label class="block text-sm font-medium text-gray-700 mb-1">Remarks</label><textarea name="remarks" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></textarea></div>
    <div class="flex items-center gap-3 pt-4 border-t">
        <button type="submit" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">Create File</button>
        <a href="{{ route('admin.files.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800">Cancel</a>
    </div>
</form>
@push('scripts')
<script>
$('#fileForm').on('submit', function(e) {
    e.preventDefault(); const form = this; const btn = form.querySelector('button[type="submit"]');
    btn.disabled = true; btn.textContent = 'Saving...';
    $.ajax({ url: form.action, type: 'POST', data: new FormData(form), processData: false, contentType: false,
        success: function(r) { if (r.success) { showToast('success', 'Success', r.message); setTimeout(() => window.location.href = r.redirect, 800); } },
        error: function(x) { btn.disabled = false; btn.textContent = 'Create File'; if (x.responseJSON?.errors) { Object.values(x.responseJSON.errors).forEach(e => showToast('error', 'Validation', e[0])); } else { showToast('error', 'Error', 'Something went wrong'); } }
    });
});
</script>
@endpush
@endsection
