@extends('layouts.admin')
@section('title', 'Edit Declaration - ' . config('app.name'))
@section('page_title', 'Edit Declaration')

@section('content')
<form id="decEditForm" action="{{ route('admin.declarations.update', $declaration) }}" method="POST" class="bg-white rounded-xl border p-6 space-y-4 max-w-4xl">
    @csrf @method('PUT')
    <div class="grid grid-cols-3 gap-4">
        <div><label class="block text-sm font-medium text-gray-700 mb-1">TANSAD No</label><input type="text" name="tansad_no" value="{{ old('tansad_no', $declaration->tansad_no) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Regime Code</label><input type="text" name="regime_code" value="{{ old('regime_code', $declaration->regime_code) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Status</label><select name="status" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="draft" @selected($declaration->status==='draft')>Draft</option><option value="lodged" @selected($declaration->status==='lodged')>Lodged</option><option value="queried" @selected($declaration->status==='queried')>Queried</option><option value="assessed" @selected($declaration->status==='assessed')>Assessed</option><option value="paid" @selected($declaration->status==='paid')>Paid</option><option value="released" @selected($declaration->status==='released')>Released</option></select></div>
    </div>
    <div class="grid grid-cols-3 gap-4">
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Duty Amount</label><input type="number" name="duty_amount" value="{{ old('duty_amount', $declaration->duty_amount) }}" step="0.01" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">VAT Amount</label><input type="number" name="vat_amount" value="{{ old('vat_amount', $declaration->vat_amount) }}" step="0.01" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Other Taxes</label><input type="number" name="other_taxes" value="{{ old('other_taxes', $declaration->other_taxes) }}" step="0.01" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
    </div>
    <div><label class="block text-sm font-medium text-gray-700 mb-1">Notes</label><textarea name="notes" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none">{{ old('notes', $declaration->notes) }}</textarea></div>
    <div class="flex items-center gap-3 pt-4 border-t">
        <button type="submit" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">Update Declaration</button>
        <a href="{{ route('admin.declarations.show', $declaration) }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800">Cancel</a>
    </div>
</form>
@push('scripts')
<script>
$('#decEditForm').on('submit', function(e) {
    e.preventDefault(); const form = this; const btn = form.querySelector('button[type="submit"]');
    btn.disabled = true; btn.textContent = 'Saving...';
    $.ajax({ url: form.action, type: 'POST', data: new FormData(form), processData: false, contentType: false,
        success: function(r) { if (r.success) { showToast('success', 'Success', r.message); setTimeout(() => window.location.href = '{{ route('admin.declarations.show', $declaration) }}', 800); } },
        error: function(x) { btn.disabled = false; btn.textContent = 'Update Declaration'; if (x.responseJSON?.errors) { Object.values(x.responseJSON.errors).forEach(e => showToast('error', 'Validation', e[0])); } else { showToast('error', 'Error', 'Something went wrong'); } }
    });
});
</script>
@endpush
@endsection
