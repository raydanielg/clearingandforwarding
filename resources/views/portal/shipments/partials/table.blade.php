<table class="w-full text-sm">
    <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50 border-b">
        <th class="px-5 py-3 font-medium">File No</th><th class="px-5 py-3 font-medium">Type</th><th class="px-5 py-3 font-medium">BL/AWB</th><th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3 font-medium">Opened</th>
    </tr></thead>
    <tbody>
        @forelse($files as $f)
        <tr class="border-b border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3"><a href="{{ route('portal.shipments.show', $f) }}" class="text-xs font-medium text-emerald-600 hover:underline">{{ $f->file_no }}</a></td>
            <td class="px-5 py-3 text-xs text-gray-700 uppercase">{{ $f->type }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $f->bl_awb_no ?? '—' }}</td>
            <td class="px-5 py-3"><span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 capitalize">{{ str_replace('_', ' ', $f->status) }}</span></td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $f->opened_at?->format('d M Y') ?? '—' }}</td>
        </tr>
        @empty
        <tr><td colspan="5" class="px-5 py-12 text-center text-sm text-gray-400">No shipments found.</td></tr>
        @endforelse
    </tbody>
</table>
@if($files->hasPages())<div class="px-5 py-3 border-t border-gray-100">{{ $files->links() }}</div>@endif
