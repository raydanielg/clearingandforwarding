<table class="w-full text-sm">
    <thead>
        <tr class="text-left text-xs text-gray-500 bg-gray-50/50 border-b">
            <th class="px-5 py-3 font-medium">Code</th>
            <th class="px-5 py-3 font-medium">Name</th>
            <th class="px-5 py-3 font-medium">TIN</th>
            <th class="px-5 py-3 font-medium">Type</th>
            <th class="px-5 py-3 font-medium">Phone</th>
            <th class="px-5 py-3 font-medium">Status</th>
            <th class="px-5 py-3 font-medium text-right">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($clients as $client)
        <tr class="border-b border-gray-100 hover:bg-gray-50/50 transition-colors">
            <td class="px-5 py-3 font-mono text-xs text-gray-700">{{ $client->code }}</td>
            <td class="px-5 py-3">
                <a href="{{ route('admin.clients.show', $client) }}" class="font-medium text-gray-900 hover:text-emerald-600">{{ $client->name }}</a>
            </td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $client->tin ?? '—' }}</td>
            <td class="px-5 py-3">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $client->type === 'company' ? 'bg-sky-50 text-sky-700' : 'bg-violet-50 text-violet-700' }}">{{ ucfirst($client->type) }}</span>
            </td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $client->phone ?? '—' }}</td>
            <td class="px-5 py-3">
                @if($client->is_active)
                <span class="inline-flex items-center gap-1 text-[10px] font-medium text-emerald-600"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Active</span>
                @else
                <span class="inline-flex items-center gap-1 text-[10px] font-medium text-gray-400"><span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>Inactive</span>
                @endif
            </td>
            <td class="px-5 py-3 text-right">
                <div class="flex items-center justify-end gap-2">
                    <a href="{{ route('admin.clients.show', $client) }}" class="p-1.5 rounded-lg hover:bg-emerald-50 text-emerald-600 transition-colors" title="View">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </a>
                    @can('clients.edit')
                    <a href="{{ route('admin.clients.edit', $client) }}" class="p-1.5 rounded-lg hover:bg-amber-50 text-amber-600 transition-colors" title="Edit">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </a>
                    @endcan
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="px-5 py-12 text-center text-sm text-gray-400">No clients found.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@if($clients->hasPages())
<div class="px-5 py-3 border-t border-gray-100">{{ $clients->links() }}</div>
@endif
