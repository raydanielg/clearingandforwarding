<table class="w-full text-sm">
    <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50 border-b">
        <th class="px-5 py-3 font-medium">Date</th><th class="px-5 py-3 font-medium">Category</th><th class="px-5 py-3 font-medium">Description</th><th class="px-5 py-3 font-medium">Recorded By</th><th class="px-5 py-3 font-medium text-right">Amount</th><th class="px-5 py-3 font-medium text-right">Actions</th>
    </tr></thead>
    <tbody>
        @forelse($expenses as $e)
        <tr class="border-b border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs text-gray-500">{{ $e->date?->format('d M Y') }}</td>
            <td class="px-5 py-3"><span class="text-[10px] px-2 py-0.5 rounded-full bg-gray-50 text-gray-600">{{ $e->category ?? '—' }}</span></td>
            <td class="px-5 py-3 text-xs text-gray-700">{{ $e->description ?? '—' }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $e->recordedBy?->name }}</td>
            <td class="px-5 py-3 text-xs text-right font-medium text-red-600">{{ number_format($e->amount) }}</td>
            <td class="px-5 py-3 text-right">@can('expenses.edit')<button onclick="deleteExp({{ $e->id }})" class="p-1.5 rounded-lg hover:bg-red-50 text-red-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>@endcan</td>
        </tr>
        @empty
        <tr><td colspan="6" class="px-5 py-12 text-center text-sm text-gray-400">No expenses found.</td></tr>
        @endforelse
    </tbody>
</table>
@if($expenses->hasPages())<div class="px-5 py-3 border-t border-gray-100">{{ $expenses->links() }}</div>@endif
