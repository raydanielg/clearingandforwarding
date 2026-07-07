<table class="w-full text-sm">
    <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50 border-b">
        <th class="px-5 py-3 font-medium">Name</th><th class="px-5 py-3 font-medium">Code</th><th class="px-5 py-3 font-medium">Contact</th><th class="px-5 py-3 font-medium">Phone</th><th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3 font-medium text-right">Actions</th>
    </tr></thead>
    <tbody>
        @forelse($agencies as $p)
        <tr class="border-b border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $p->name }}</td>
            <td class="px-5 py-3 font-mono text-xs text-gray-500">{{ $p->code ?? '—' }}</td>
            <td class="px-5 py-3 text-xs text-gray-700">{{ $p->contact_person ?? '—' }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $p->phone ?? '—' }}</td>
            <td class="px-5 py-3">@if($p->is_active)<span class="text-[10px] text-emerald-600 font-medium">Active</span>@else<span class="text-[10px] text-gray-400 font-medium">Inactive</span>@endif</td>
            <td class="px-5 py-3 text-right">
                @can('partners.edit')<button onclick="editAg({{ $p->id }},'{{ addslashes($p->name) }}','{{ $p->code ?? '' }}','{{ addslashes($p->contact_person ?? '') }}','{{ $p->phone ?? '' }}',{{ $p->is_active ? 1 : 0 }})" class="p-1.5 rounded-lg hover:bg-amber-50 text-amber-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>@endcan
                @can('partners.delete')<button onclick="deleteAg({{ $p->id }})" class="p-1.5 rounded-lg hover:bg-red-50 text-red-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>@endcan
            </td>
        </tr>
        @empty
        <tr><td colspan="6" class="px-5 py-12 text-center text-sm text-gray-400">No records found.</td></tr>
        @endforelse
    </tbody>
</table>
@if($agencies->hasPages())<div class="px-5 py-3 border-t border-gray-100">{{ $agencies->links() }}</div>@endif
