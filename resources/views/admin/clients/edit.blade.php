@extends('layouts.admin')

@section('title', 'Edit Client - ' . config('app.name'))
@section('page_title', 'Edit: ' . $client->name)

@section('content')
<div class="max-w-2xl">
    <form id="clientEditForm" action="{{ route('admin.clients.update', $client) }}" method="POST" class="bg-white rounded-xl border p-6 space-y-4">
        @csrf @method('PUT')
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Client Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $client->name) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select name="type" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none">
                    <option value="company" @selected($client->type === 'company')>Company</option>
                    <option value="individual" @selected($client->type === 'individual')>Individual</option>
                </select>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">TIN</label><input type="text" name="tin" value="{{ old('tin', $client->tin) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">VRN</label><input type="text" name="vrn" value="{{ old('vrn', $client->vrn) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Phone</label><input type="text" name="phone" value="{{ old('phone', $client->phone) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Email</label><input type="email" name="email" value="{{ old('email', $client->email) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
        </div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Address</label><textarea name="address" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none">{{ old('address', $client->address) }}</textarea></div>
        <div class="grid grid-cols-3 gap-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Credit Limit</label><input type="number" name="credit_limit" value="{{ old('credit_limit', $client->credit_limit) }}" step="0.01" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Payment Terms (days)</label><input type="number" name="payment_terms_days" value="{{ old('payment_terms_days', $client->payment_terms_days) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Status</label><select name="is_active" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="1" @selected($client->is_active)>Active</option><option value="0" @selected(!$client->is_active)>Inactive</option></select></div>
        </div>
        <div class="flex items-center gap-3 pt-4 border-t">
            <button type="submit" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">Update Client</button>
            <a href="{{ route('admin.clients.show', $client) }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800">Cancel</a>
        </div>
    </form>
</div>
@push('scripts')
<script>
$('#clientEditForm').on('submit', function(e) {
    e.preventDefault();
    const form = this; const btn = form.querySelector('button[type="submit"]');
    btn.disabled = true; btn.textContent = 'Saving...';
    $.ajax({ url: form.action, type: 'POST', data: new FormData(form), processData: false, contentType: false,
        success: function(res) { if (res.success) { showToast('success', 'Success', res.message); setTimeout(() => window.location.href = '{{ route('admin.clients.show', $client) }}', 800); } },
        error: function(xhr) { btn.disabled = false; btn.textContent = 'Update Client'; if (xhr.responseJSON?.errors) { Object.values(xhr.responseJSON.errors).forEach(e => showToast('error', 'Validation', e[0])); } else { showToast('error', 'Error', 'Something went wrong'); } }
    });
});
</script>
@endpush
@endsection
