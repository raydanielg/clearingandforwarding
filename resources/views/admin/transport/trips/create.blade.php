@extends('layouts.admin')
@section('title', 'New Trip - ' . config('app.name'))
@section('page_title', 'New Trip')

@section('content')
<form id="tripForm" action="{{ route('admin.transport.trips.store') }}" method="POST" class="bg-white rounded-xl border p-6 space-y-4 max-w-2xl">
    @csrf
    <div><label class="block text-sm font-medium text-gray-700 mb-1">Shipment File <span class="text-red-500">*</span></label><select name="shipment_file_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none" required><option value="">Select file...</option>@foreach($files as $f)<option value="{{ $f->id }}">{{ $f->file_no }} — {{ $f->client?->name }}</option>@endforeach</select></div>
    <div class="grid grid-cols-2 gap-4">
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Truck <span class="text-red-500">*</span></label><select name="truck_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none" required><option value="">Select truck...</option>@foreach($trucks as $t)<option value="{{ $t->id }}">{{ $t->plate_no }} ({{ $t->transporter?->name }})</option>@endforeach</select></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Driver <span class="text-red-500">*</span></label><select name="driver_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none" required><option value="">Select driver...</option>@foreach($drivers as $d)<option value="{{ $d->id }}">{{ $d->name }} ({{ $d->phone }})</option>@endforeach</select></div>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Origin</label><input type="text" name="origin" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Destination</label><input type="text" name="destination" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Transport Cost</label><input type="number" name="transport_cost" step="0.01" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Dispatch Date</label><input type="datetime-local" name="dispatched_at" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
    </div>
    <div><label class="block text-sm font-medium text-gray-700 mb-1">Notes</label><textarea name="notes" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></textarea></div>
    <div class="flex items-center gap-3 pt-4 border-t">
        <button type="submit" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">Create Trip</button>
        <a href="{{ route('admin.transport.trips.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800">Cancel</a>
    </div>
</form>
@push('scripts')
<script>
$('#tripForm').on('submit', function(e) {
    e.preventDefault(); const form = this; const btn = form.querySelector('button[type="submit"]');
    btn.disabled = true; btn.textContent = 'Saving...';
    $.ajax({ url: form.action, type: 'POST', data: new FormData(form), processData: false, contentType: false,
        success: function(r) { if (r.success) { showToast('success', 'Success', r.message); setTimeout(() => window.location.href = r.redirect, 800); } },
        error: function(x) { btn.disabled = false; btn.textContent = 'Create Trip'; if (x.responseJSON?.errors) { Object.values(x.responseJSON.errors).forEach(e => showToast('error', 'Validation', e[0])); } else { showToast('error', 'Error', 'Something went wrong'); } }
    });
});
</script>
@endpush
@endsection
