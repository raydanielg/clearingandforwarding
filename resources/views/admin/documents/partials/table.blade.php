<table class="w-full text-sm">
    <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50 border-b">
        <th class="px-5 py-3 font-medium">Type</th><th class="px-5 py-3 font-medium">File No</th><th class="px-5 py-3 font-medium">Reference</th>
        <th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3 font-medium">Uploaded</th><th class="px-5 py-3 font-medium text-right">Actions</th>
    </tr></thead>
    <tbody>
        @forelse($documents as $doc)
        <tr class="border-b border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $doc->documentType?->name ?? '—' }}</td>
            <td class="px-5 py-3"><a href="{{ route('admin.files.show', $doc->shipmentFile) }}" class="font-mono text-xs text-emerald-600 hover:underline">{{ $doc->shipmentFile?->file_no }}</a></td>
            <td class="px-5 py-3 font-mono text-xs text-gray-500">{{ $doc->reference_no ?? '—' }}</td>
            <td class="px-5 py-3">
                <div class="flex gap-1">
                    @if($doc->verified_at)<span class="text-[10px] px-1.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700">Verified</span>@endif
                    @if($doc->original_received)<span class="text-[10px] px-1.5 py-0.5 rounded-full bg-sky-50 text-sky-700">Original</span>@endif
                    @if(!$doc->verified_at && !$doc->original_received)<span class="text-[10px] px-1.5 py-0.5 rounded-full bg-amber-50 text-amber-700">Pending</span>@endif
                </div>
            </td>
            <td class="px-5 py-3 text-xs text-gray-400">{{ $doc->created_at->format('d M Y') }}</td>
            <td class="px-5 py-3 text-right">
                <a href="{{ route('admin.documents.download', $doc) }}" class="p-1.5 rounded-lg hover:bg-emerald-50 text-emerald-600" title="Download"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg></a>
                @can('documents.verify')@if(!$doc->verified_at)<button onclick="verifyDoc({{ $doc->id }})" class="p-1.5 rounded-lg hover:bg-emerald-50 text-emerald-600" title="Verify"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></button>@endif@endcan
                @can('documents.delete')<button onclick="deleteDoc({{ $doc->id }})" class="p-1.5 rounded-lg hover:bg-red-50 text-red-600" title="Delete"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>@endcan
            </td>
        </tr>
        @empty
        <tr><td colspan="6" class="px-5 py-12 text-center text-sm text-gray-400">No documents found.</td></tr>
        @endforelse
    </tbody>
</table>
@if($documents->hasPages())<div class="px-5 py-3 border-t border-gray-100">{{ $documents->links() }}</div>@endif
@push('scripts')
<script>
function verifyDoc(id) { $.ajax({ url: '{{ route('admin.documents.verify', '__id__') }}'.replace('__id__', id), type: 'POST', data: { _token: '{{ csrf_token() }}' }, success: function(r) { showToast('success', 'Verified', r.message); setTimeout(() => location.reload(), 800); }, error: function() { showToast('error', 'Error', 'Could not verify'); } }); }
function deleteDoc(id) { if (!confirm('Delete this document?')) return; $.ajax({ url: '{{ route('admin.documents.destroy', '__id__') }}'.replace('__id__', id), type: 'POST', data: { _method: 'DELETE', _token: '{{ csrf_token() }}' }, success: function(r) { showToast('success', 'Deleted', r.message); setTimeout(() => location.reload(), 800); }, error: function() { showToast('error', 'Error', 'Could not delete'); } }); }
</script>
@endpush
