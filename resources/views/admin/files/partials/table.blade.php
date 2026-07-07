<table class="w-full text-sm">
    <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50 border-b">
        <th class="px-5 py-3 font-medium">File No</th><th class="px-5 py-3 font-medium">Client</th><th class="px-5 py-3 font-medium">Type</th>
        <th class="px-5 py-3 font-medium">BL / AWB</th><th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3 font-medium">ETA</th><th class="px-5 py-3 font-medium text-right">Actions</th>
    </tr></thead>
    <tbody>
        @forelse($files as $file)
        <tr class="border-b border-gray-100 hover:bg-gray-50/50 transition-colors">
            <td class="px-5 py-3"><a href="{{ route('admin.files.show', $file) }}" class="font-mono text-xs text-emerald-600 hover:underline font-medium">{{ $file->file_no }}</a></td>
            <td class="px-5 py-3 text-xs text-gray-700">{{ $file->client?->name ?? '—' }}</td>
            <td class="px-5 py-3"><span class="text-[10px] px-2 py-0.5 rounded-full {{ $file->type === 'import' ? 'bg-sky-50 text-sky-700' : ($file->type === 'export' ? 'bg-violet-50 text-violet-700' : 'bg-amber-50 text-amber-700') }}">{{ ucfirst($file->type) }}</span></td>
            <td class="px-5 py-3 font-mono text-xs text-gray-500">{{ $file->bl_awb_no ?? '—' }}</td>
            <td class="px-5 py-3"><span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700">{{ ucfirst(str_replace('_', ' ', $file->status)) }}</span></td>
            <td class="px-5 py-3 text-xs text-gray-400">{{ $file->eta?->format('d M Y') ?? '—' }}</td>
            <td class="px-5 py-3 text-right">
                <a href="{{ route('admin.files.show', $file) }}" class="p-1.5 rounded-lg hover:bg-emerald-50 text-emerald-600" title="View"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></a>
                @can('files.edit')<a href="{{ route('admin.files.edit', $file) }}" class="p-1.5 rounded-lg hover:bg-amber-50 text-amber-600" title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>@endcan
            </td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-5 py-12 text-center text-sm text-gray-400">No files found.</td></tr>
        @endforelse
    </tbody>
</table>
@if($files->hasPages())<div class="px-5 py-3 border-t border-gray-100">{{ $files->links() }}</div>@endif
