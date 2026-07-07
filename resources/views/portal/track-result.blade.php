<div class="bg-white rounded-xl border overflow-hidden">
    <div class="px-5 py-4 border-b flex items-center justify-between">
        <div>
            <h3 class="text-sm font-semibold text-gray-900">{{ $file->file_no }}</h3>
            <p class="text-xs text-gray-500 mt-0.5">{{ ucfirst($file->type) }} · {{ ucfirst($file->mode) }} · {{ str_replace('_', ' ', $file->status) }}</p>
        </div>
        <a href="{{ route('portal.shipments.show', $file) }}" class="text-xs font-medium text-emerald-600 hover:text-emerald-700">View Details →</a>
    </div>
    <div class="p-5">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            <div><p class="text-[10px] text-gray-500 font-medium">BL/AWB</p><p class="text-sm text-gray-900 mt-0.5">{{ $file->bl_awb_no ?? '—' }}</p></div>
            <div><p class="text-[10px] text-gray-500 font-medium">Vessel</p><p class="text-sm text-gray-900 mt-0.5">{{ $file->vessel_flight ?? '—' }}</p></div>
            <div><p class="text-[10px] text-gray-500 font-medium">ETA</p><p class="text-sm text-gray-900 mt-0.5">{{ $file->eta?->format('d M Y') ?? '—' }}</p></div>
            <div><p class="text-[10px] text-gray-500 font-medium">Opened</p><p class="text-sm text-gray-900 mt-0.5">{{ $file->opened_at?->format('d M Y') ?? '—' }}</p></div>
        </div>
        @if($file->milestones->isNotEmpty())
        <div class="border-t pt-4">
            <p class="text-xs font-semibold text-gray-900 mb-3">Latest Milestones</p>
            <div class="space-y-2">
                @foreach($file->milestones->take(4) as $m)
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                    <span class="text-xs text-gray-700 capitalize">{{ str_replace('_', ' ', $m->stage) }}</span>
                    <span class="text-[10px] text-gray-400 ml-auto">{{ $m->happened_at?->format('d M Y') }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
