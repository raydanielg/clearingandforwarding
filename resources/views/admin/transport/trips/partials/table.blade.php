<table class="w-full text-sm">
    <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50 border-b">
        <th class="px-5 py-3 font-medium">File No</th><th class="px-5 py-3 font-medium">Truck</th><th class="px-5 py-3 font-medium">Driver</th><th class="px-5 py-3 font-medium">Route</th><th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3 font-medium text-right">Actions</th>
    </tr></thead>
    <tbody>
        @forelse($trips as $trip)
        <tr class="border-b border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3"><a href="{{ route('admin.files.show', $trip->shipmentFile) }}" class="font-mono text-xs text-emerald-600 hover:underline">{{ $trip->shipmentFile?->file_no }}</a></td>
            <td class="px-5 py-3 text-xs font-medium">{{ $trip->truck?->plate_no ?? '—' }}</td>
            <td class="px-5 py-3 text-xs text-gray-700">{{ $trip->driver?->name ?? '—' }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $trip->origin ?? '—' }} → {{ $trip->destination ?? '—' }}</td>
            <td class="px-5 py-3"><span class="text-[10px] px-2 py-0.5 rounded-full {{ $trip->status === 'delivered' ? 'bg-emerald-50 text-emerald-700' : ($trip->status === 'in_transit' ? 'bg-sky-50 text-sky-700' : 'bg-amber-50 text-amber-700') }}">{{ ucfirst(str_replace('_', ' ', $trip->status)) }}</span></td>
            <td class="px-5 py-3 text-right"><a href="{{ route('admin.transport.trips.show', $trip) }}" class="p-1.5 rounded-lg hover:bg-emerald-50 text-emerald-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></a></td>
        </tr>
        @empty
        <tr><td colspan="6" class="px-5 py-12 text-center text-sm text-gray-400">No trips found.</td></tr>
        @endforelse
    </tbody>
</table>
@if($trips->hasPages())<div class="px-5 py-3 border-t border-gray-100">{{ $trips->links() }}</div>@endif
