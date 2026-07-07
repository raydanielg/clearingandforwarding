<table class="w-full text-sm">
    <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50 border-b">
        <th class="px-5 py-3 font-medium">Type</th><th class="px-5 py-3 font-medium">File No</th><th class="px-5 py-3 font-medium">Reference</th><th class="px-5 py-3 font-medium">Uploaded</th><th class="px-5 py-3 font-medium">Status</th>
    </tr></thead>
    <tbody>
        @forelse($documents as $d)
        <tr class="border-b border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $d->documentType?->name ?? '—' }}</td>
            <td class="px-5 py-3"><a href="{{ route('portal.shipments.show', $d->shipmentFile) }}" class="text-xs text-emerald-600 hover:underline">{{ $d->shipmentFile?->file_no }}</a></td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $d->reference_no ?? '—' }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $d->created_at?->format('d M Y') }}</td>
            <td class="px-5 py-3">@if($d->verified_at)<span class="text-[10px] text-emerald-600 font-medium">Verified</span>@else<span class="text-[10px] text-amber-600 font-medium">Pending</span>@endif</td>
        </tr>
        @empty
        <tr><td colspan="5" class="px-5 py-12 text-center text-sm text-gray-400">No documents found.</td></tr>
        @endforelse
    </tbody>
</table>
@if($documents->hasPages())<div class="px-5 py-3 border-t border-gray-100">{{ $documents->links() }}</div>@endif
