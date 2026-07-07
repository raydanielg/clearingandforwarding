<table class="w-full text-sm">
    <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50 border-b">
        <th class="px-5 py-3 font-medium">Invoice No</th><th class="px-5 py-3 font-medium">Date</th><th class="px-5 py-3 font-medium">Due Date</th><th class="px-5 py-3 font-medium">Total</th><th class="px-5 py-3 font-medium">Paid</th><th class="px-5 py-3 font-medium">Status</th>
    </tr></thead>
    <tbody>
        @forelse($invoices as $inv)
        <tr class="border-b border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3"><a href="{{ route('portal.invoices.show', $inv) }}" class="text-xs font-medium text-emerald-600 hover:underline">{{ $inv->invoice_no }}</a></td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $inv->date?->format('d M Y') }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $inv->due_date?->format('d M Y') ?? '—' }}</td>
            <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ number_format($inv->total) }}</td>
            <td class="px-5 py-3 text-xs text-emerald-600">{{ number_format($inv->amount_paid) }}</td>
            <td class="px-5 py-3"><span class="text-[10px] px-2 py-0.5 rounded-full @if($inv->status === 'paid') bg-emerald-50 text-emerald-700 @elseif($inv->status === 'overdue') bg-red-50 text-red-700 @else bg-amber-50 text-amber-700 @endif capitalize">{{ str_replace('_', ' ', $inv->status) }}</span></td>
        </tr>
        @empty
        <tr><td colspan="6" class="px-5 py-12 text-center text-sm text-gray-400">No invoices found.</td></tr>
        @endforelse
    </tbody>
</table>
@if($invoices->hasPages())<div class="px-5 py-3 border-t border-gray-100">{{ $invoices->links() }}</div>@endif
