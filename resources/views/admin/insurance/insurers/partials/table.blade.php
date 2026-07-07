<table class="w-full text-sm">
    <thead>
        <tr class="text-left text-xs text-gray-500 bg-gray-50/50 border-b">
            <th class="px-5 py-3 font-medium">Name</th>
            <th class="px-5 py-3 font-medium">Code</th>
            <th class="px-5 py-3 font-medium">Contact</th>
            <th class="px-5 py-3 font-medium">Phone</th>
            <th class="px-5 py-3 font-medium">Status</th>
            <th class="px-5 py-3 text-right font-medium">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($insurers as $ins)
        <tr class="border-b border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 font-medium text-gray-900">{{ $ins->name }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $ins->code ?? '—' }}</td>
            <td class="px-5 py-3 text-xs text-gray-700">{{ $ins->contact_person ?? '—' }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $ins->phone ?? '—' }}</td>
            <td class="px-5 py-3">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $ins->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-gray-50 text-gray-500 border border-gray-100' }}">{{ $ins->is_active ? 'Active' : 'Inactive' }}</span>
            </td>
            <td class="px-5 py-3 text-right">
                @can('insurance.edit')
                <button onclick='editInsurer({{ $ins->id }},"{{ addslashes($ins->name) }}","{{ $ins->code ?? '' }}","{{ $ins->contact_person ?? '' }}","{{ $ins->phone ?? '' }}","{{ $ins->email ?? '' }}","{{ $ins->license_no ?? '' }}","{{ addslashes($ins->address ?? '') }}",{{ $ins->is_active ? 1 : 0 }})' class="p-1.5 rounded-lg hover:bg-emerald-50 text-emerald-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                @endcan
                @can('insurance.delete')
                <button onclick="deleteInsurer({{ $ins->id }})" class="p-1.5 rounded-lg hover:bg-red-50 text-red-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                @endcan
            </td>
        </tr>
        @empty
        <tr><td colspan="6" class="px-5 py-12 text-center text-sm text-gray-400">No insurers found.</td></tr>
        @endforelse
    </tbody>
</table>
@if($insurers->hasPages())<div class="px-5 py-3 border-t border-gray-100">{{ $insurers->links() }}</div>@endif
