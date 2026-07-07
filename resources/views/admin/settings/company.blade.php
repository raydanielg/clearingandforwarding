@extends('layouts.admin')
@section('title', 'Company Settings - ' . config('app.name'))
@section('page_title', 'Company Settings')

@section('content')
<form id="settingsForm" action="{{ route('admin.settings.company.update') }}" method="POST" class="bg-white rounded-xl border p-6 space-y-4 max-w-3xl">
    @csrf @method('PUT')
    <div class="grid grid-cols-2 gap-4">
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Company Name</label><input type="text" name="company_name" value="{{ old('company_name', $settings['company_name'] ?? '') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">TIN</label><input type="text" name="company_tin" value="{{ old('company_tin', $settings['company_tin'] ?? '') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Phone</label><input type="text" name="company_phone" value="{{ old('company_phone', $settings['company_phone'] ?? '') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Email</label><input type="email" name="company_email" value="{{ old('company_email', $settings['company_email'] ?? '') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
    </div>
    <div><label class="block text-sm font-medium text-gray-700 mb-1">Address</label><textarea name="company_address" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none">{{ old('company_address', $settings['company_address'] ?? '') }}</textarea></div>
    <div class="grid grid-cols-3 gap-4">
        <div><label class="block text-sm font-medium text-gray-700 mb-1">File No Prefix</label><input type="text" name="file_no_prefix" value="{{ old('file_no_prefix', $settings['file_no_prefix'] ?? 'CAF') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Invoice No Prefix</label><input type="text" name="invoice_no_prefix" value="{{ old('invoice_no_prefix', $settings['invoice_no_prefix'] ?? 'INV') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Receipt No Prefix</label><input type="text" name="receipt_no_prefix" value="{{ old('receipt_no_prefix', $settings['receipt_no_prefix'] ?? 'RCT') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Default Currency</label><select name="default_currency" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="TZS" @selected(($settings['default_currency'] ?? 'TZS') === 'TZS')>TZS</option><option value="USD" @selected(($settings['default_currency'] ?? '') === 'USD')>USD</option><option value="EUR" @selected(($settings['default_currency'] ?? '') === 'EUR')>EUR</option></select></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">VAT Rate (%)</label><input type="number" name="vat_rate" value="{{ old('vat_rate', $settings['vat_rate'] ?? 18) }}" step="0.01" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
    </div>
    <div class="flex items-center gap-3 pt-4 border-t">
        <button type="submit" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">Save Settings</button>
    </div>
</form>
@push('scripts')
<script>
$('#settingsForm').on('submit', function(e) {
    e.preventDefault(); const form = this; const btn = form.querySelector('button[type="submit"]');
    btn.disabled = true; btn.textContent = 'Saving...';
    $.ajax({ url: form.action, type: 'POST', data: new FormData(form), processData: false, contentType: false,
        success: function(r) { if (r.success) { showToast('success', 'Saved', r.message); btn.disabled = false; btn.textContent = 'Save Settings'; } },
        error: function(x) { btn.disabled = false; btn.textContent = 'Save Settings'; if (x.responseJSON?.errors) { Object.values(x.responseJSON.errors).forEach(e => showToast('error', 'Validation', e[0])); } else { showToast('error', 'Error', 'Could not save'); } }
    });
});
</script>
@endpush
@endsection
