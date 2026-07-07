<table class="w-full text-sm">
    <thead>
        <tr class="text-left text-xs text-gray-500 bg-gray-50/50 border-b">
            <th class="px-5 py-3 font-medium">Policy No</th>
            <th class="px-5 py-3 font-medium">Type</th>
            <th class="px-5 py-3 font-medium">Insurer</th>
            <th class="px-5 py-3 font-medium">Cover Period</th>
            <th class="px-5 py-3 font-medium">Premium</th>
            <th class="px-5 py-3 font-medium">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($policies as $pol)
        <tr class="border-b border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3"><a href="{{ route('portal.insurance.show', $pol) }}" class="text-xs font-medium text-emerald-600 hover:underline">{{ $pol->policy_no }}</a></td>
            <td class="px-5 py-3 text-xs text-gray-700">{{ ucfirst(str_replace('_', ' ', $pol->type)) }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $pol->insurer?->name ?? '—' }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $pol->cover_start_date?->format('d M Y') ?? '—' }} → {{ $pol->cover_end_date?->format('d M Y') ?? '—' }}</td>
            <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $pol->premium_amount ? number_format($pol->premium_amount) . ' ' . ($pol->premium_currency ?? 'TZS') : '—' }}</td>
            <td class="px-5 py-3"><span class="text-[10px] px-2 py-0.5 rounded-full @if($pol->status === 'active') bg-emerald-50 text-emerald-700 @elseif($pol->status === 'expired') bg-red-50 text-red-700 @else bg-amber-50 text-amber-700 @endif capitalize">{{ $pol->status }}</span></td>
        </tr>
        @empty
        <tr><td colspan="6" class="px-5 py-12 text-center text-sm text-gray-400">No insurance policies found.</td></tr>
        @endforelse
    </tbody>
</table>
@if($policies->hasPages())<div class="px-5 py-3 border-t border-gray-100">{{ $policies->links() }}</div>@endif
