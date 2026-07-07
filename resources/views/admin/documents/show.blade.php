@extends('layouts.admin')
@section('title', 'Document Details - ' . config('app.name'))
@section('page_title', $document->documentType?->name ?? 'Document')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-xl border p-6">
        <div class="flex items-start justify-between mb-4">
            <div>
                <h2 class="text-lg font-bold text-gray-900">{{ $document->documentType?->name }}</h2>
                <p class="text-sm text-gray-500">{{ $document->shipmentFile?->file_no }} · {{ $document->shipmentFile?->client?->name }}</p>
            </div>
            <div class="flex gap-1">
                @if($document->verified_at)<span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700">Verified</span>@endif
                @if($document->original_received)<span class="text-[10px] px-2 py-0.5 rounded-full bg-sky-50 text-sky-700">Original</span>@endif
            </div>
        </div>
        <dl class="space-y-2 text-sm">
            <div class="flex justify-between"><dt class="text-gray-500">Reference No</dt><dd class="font-medium">{{ $document->reference_no ?? '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Uploaded By</dt><dd class="font-medium">{{ $document->uploadedBy?->name }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Uploaded At</dt><dd class="font-medium">{{ $document->created_at->format('d M Y, H:i') }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Verified By</dt><dd class="font-medium">{{ $document->verifiedBy?->name ?? '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Verified At</dt><dd class="font-medium">{{ $document->verified_at?->format('d M Y, H:i') ?? '—' }}</dd></div>
        </dl>
        <div class="mt-4 pt-4 border-t flex gap-2">
            <a href="{{ route('admin.documents.download', $document) }}" class="px-3 py-1.5 text-xs font-medium text-emerald-600 hover:bg-emerald-50 rounded-lg">Download</a>
            @can('documents.verify')@if(!$document->verified_at)<button onclick="verifyDoc()" class="px-3 py-1.5 text-xs font-medium text-emerald-600 hover:bg-emerald-50 rounded-lg">Verify</button>@endif@endcan
            @can('documents.verify')@if(!$document->original_received)<button onclick="markOriginal()" class="px-3 py-1.5 text-xs font-medium text-sky-600 hover:bg-sky-50 rounded-lg">Mark Original Received</button>@endif@endcan
            @can('documents.delete')<button onclick="deleteDoc()" class="px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 rounded-lg">Delete</button>@endcan
        </div>
    </div>
</div>
@push('scripts')
<script>
function verifyDoc() { $.ajax({ url: '{{ route('admin.documents.verify', $document) }}', type: 'POST', data: { _token: '{{ csrf_token() }}' }, success: function(r) { showToast('success', 'Verified', r.message); setTimeout(() => location.reload(), 800); }, error: function() { showToast('error', 'Error', 'Could not verify'); } }); }
function markOriginal() { $.ajax({ url: '{{ route('admin.documents.original', $document) }}', type: 'POST', data: { _token: '{{ csrf_token() }}' }, success: function(r) { showToast('success', 'Success', r.message); setTimeout(() => location.reload(), 800); }, error: function() { showToast('error', 'Error', 'Could not update'); } }); }
function deleteDoc() { if (!confirm('Delete this document?')) return; $.ajax({ url: '{{ route('admin.documents.destroy', $document) }}', type: 'POST', data: { _method: 'DELETE', _token: '{{ csrf_token() }}' }, success: function(r) { showToast('success', 'Deleted', r.message); setTimeout(() => window.location.href = '{{ route('admin.documents.index') }}', 800); }, error: function() { showToast('error', 'Error', 'Could not delete'); } }); }
</script>
@endpush
@endsection
