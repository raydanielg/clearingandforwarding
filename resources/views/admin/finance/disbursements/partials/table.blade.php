<table class="w-full text-sm">
    <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50 border-b">
        <th class="px-5 py-3 font-medium">File No</th><th class="px-5 py-3 font-medium">Payee</th><th class="px-5 py-3 font-medium">Agency</th><th class="px-5 py-3 font-medium">Method</th><th class="px-5 py-3 font-medium">Date</th><th class="px-5 py-3 font-medium text-right">Amount</th><th class="px-5 py-3 font-medium">Approved</th><th class="px-5 py-3 font-medium text-right">Actions</th>
    </tr></thead>
    <tbody>
        @forelse($disbursements as $d)
        <tr class="border-b border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3"><a href="{{ route('admin.files.show', $d->shipmentFile) }}" class="font-mono text-xs text-emerald-600 hover:underline">{{ $d->shipmentFile?->file_no }}</a></td>
            <td class="px-5 py-3 text-xs text-gray-700">{{ $d->payee ?? $d->agency?->name ?? '—' }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $d->agency?->name ?? '—' }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ ucfirst($d->method) }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $d->paid_at?->format('d M Y') }}</td>
            <td class="px-5 py-3 text-xs text-right font-medium text-red-600">{{ number_format($d->amount) }}</td>
            <td class="px-5 py-3">@if($d->approved_by)<span class="text-[10px] text-emerald-600">Yes</span>@else<span class="text-[10px] text-amber-600">Pending</span>@endif</td>
            <td class="px-5 py-3 text-right"><a href="{{ route('admin.disbursements.show', $d) }}" class="p-1.5 rounded-lg hover:bg-emerald-50 text-emerald-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></a></td>
        </tr>
        @empty
        <tr><td colspan="8" class="px-5 py-12 text-center text-sm text-gray-400">No disbursements found.</td></tr>
        @endforelse
    </tbody>
</table>
@if($disbursements->hasPages())<div class="px-5 py-3 border-t border-gray-100">{{ $disbursements->links() }}</div>@endif
