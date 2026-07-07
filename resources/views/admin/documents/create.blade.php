@extends('layouts.admin')
@section('title', 'Upload Document - ' . config('app.name'))
@section('page_title', 'Upload Document')

@section('content')
<form id="docForm" action="{{ route('admin.documents.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl border p-6 space-y-4 max-w-2xl">
    @csrf
    <div><label class="block text-sm font-medium text-gray-700 mb-1">Shipment File <span class="text-red-500">*</span></label><select name="shipment_file_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none" required><option value="">Select file...</option>@foreach($files as $f)<option value="{{ $f->id }}">{{ $f->file_no }} — {{ $f->client?->name }}</option>@endforeach</select></div>
    <div><label class="block text-sm font-medium text-gray-700 mb-1">Document Type <span class="text-red-500">*</span></label><select name="document_type_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none" required><option value="">Select type...</option>@foreach($documentTypes as $dt)<option value="{{ $dt->id }}">{{ $dt->name }}</option>@endforeach</select></div>
    <div><label class="block text-sm font-medium text-gray-700 mb-1">Reference No</label><input type="text" name="reference_no" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
    <div><label class="block text-sm font-medium text-gray-700 mb-1">File <span class="text-red-500">*</span></label><input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm" required></div>
    <div class="flex items-center gap-2"><input type="checkbox" name="original_received" value="1" id="origRec" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"><label for="origRec" class="text-sm text-gray-700">Original hard copy received</label></div>
    <div class="flex items-center gap-3 pt-4 border-t">
        <button type="submit" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">Upload</button>
        <a href="{{ route('admin.documents.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800">Cancel</a>
    </div>
</form>
@push('scripts')
<script>
$('#docForm').on('submit', function(e) {
    e.preventDefault(); const form = this; const btn = form.querySelector('button[type="submit"]');
    btn.disabled = true; btn.textContent = 'Uploading...';
    $.ajax({ url: form.action, type: 'POST', data: new FormData(form), processData: false, contentType: false,
        success: function(r) { if (r.success) { showToast('success', 'Success', r.message); setTimeout(() => window.location.href = '{{ route('admin.documents.index') }}', 800); } },
        error: function(x) { btn.disabled = false; btn.textContent = 'Upload'; if (x.responseJSON?.errors) { Object.values(x.responseJSON.errors).forEach(e => showToast('error', 'Validation', e[0])); } else { showToast('error', 'Error', 'Upload failed'); } }
    });
});
</script>
@endpush
@endsection
