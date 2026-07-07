<table class="w-full text-sm">
    <thead>
        <tr class="text-left text-xs text-gray-500 bg-gray-50/50 border-b">
            <th class="px-5 py-3 font-medium">Policy No</th>
            <th class="px-5 py-3 font-medium">Client</th>
            <th class="px-5 py-3 font-medium">Type</th>
            <th class="px-5 py-3 font-medium">Insurer</th>
            <th class="px-5 py-3 font-medium">Premium</th>
            <th class="px-5 py-3 font-medium">Cover Period</th>
            <th class="px-5 py-3 font-medium">Status</th>
            <th class="px-5 py-3 text-right font-medium">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($policies as $pol)
        <tr class="border-b border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3"><a href="{{ route('admin.insurance.policies.show', $pol) }}" class="font-mono text-xs text-emerald-600 hover:underline">{{ $pol->policy_no }}</a></td>
            <td class="px-5 py-3 text-xs text-gray-700">{{ $pol->client?->name ?? '—' }}</td>
            <td class="px-5 py-3"><span class="text-[10px] px-2 py-0.5 rounded-full bg-sky-50 text-sky-700 border border-sky-100">{{ ucfirst(str_replace('_', ' ', $pol->type)) }}</span></td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $pol->insurer?->name ?? '—' }}</td>
            <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $pol->premium_amount ? number_format($pol->premium_amount) . ' ' . ($pol->premium_currency ?? 'TZS') : '—' }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $pol->cover_start_date?->format('d M Y') ?? '—' }} → {{ $pol->cover_end_date?->format('d M Y') ?? '—' }}</td>
            <td class="px-5 py-3">
                @php $statusColors = ['draft'=>'bg-gray-50 text-gray-600 border-gray-100','quoted'=>'bg-amber-50 text-amber-700 border-amber-100','issued'=>'bg-sky-50 text-sky-700 border-sky-100','active'=>'bg-emerald-50 text-emerald-700 border-emerald-100','expired'=>'bg-red-50 text-red-700 border-red-100','cancelled'=>'bg-gray-50 text-gray-500 border-gray-100','claimed'=>'bg-violet-50 text-violet-700 border-violet-100']; @endphp
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium border {{ $statusColors[$pol->status] ?? $statusColors['draft'] }}">{{ ucfirst($pol->status) }}</span>
            </td>
            <td class="px-5 py-3 text-right">
                <a href="{{ route('admin.insurance.policies.show', $pol) }}" class="p-1.5 rounded-lg hover:bg-emerald-50 text-emerald-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></a>
                @can('insurance.edit')
                <a href="{{ route('admin.insurance.policies.edit', $pol) }}" class="p-1.5 rounded-lg hover:bg-emerald-50 text-emerald-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
                @endcan
                @can('insurance.delete')
                <button onclick="deletePolicy({{ $pol->id }})" class="p-1.5 rounded-lg hover:bg-red-50 text-red-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                @endcan
            </td>
        </tr>
        @empty
        <tr><td colspan="8" class="px-5 py-12 text-center text-sm text-gray-400">No policies found.</td></tr>
        @endforelse
    </tbody>
</table>
@if($policies->hasPages())<div class="px-5 py-3 border-t border-gray-100">{{ $policies->links() }}</div>@endif
