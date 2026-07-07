@extends('layouts.admin')
@section('title', 'Trip Details - ' . config('app.name'))
@section('page_title', 'Trip: ' . ($trip->truck?->plate_no ?? '—'))

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl border p-5">
            <div class="flex items-start justify-between mb-4">
                <div><h2 class="text-lg font-bold text-gray-900">{{ $trip->truck?->plate_no }}</h2><p class="text-sm text-gray-500">{{ $trip->shipmentFile?->file_no }} · {{ $trip->shipmentFile?->client?->name }}</p></div>
                <span class="text-[10px] px-2 py-0.5 rounded-full {{ $trip->status === 'delivered' ? 'bg-emerald-50 text-emerald-700' : ($trip->status === 'in_transit' ? 'bg-sky-50 text-sky-700' : 'bg-amber-50 text-amber-700') }}">{{ ucfirst(str_replace('_', ' ', $trip->status)) }}</span>
            </div>
            <div class="grid grid-cols-3 gap-4 text-sm">
                <div><p class="text-[10px] text-gray-400">Driver</p><p class="font-medium">{{ $trip->driver?->name }}</p></div>
                <div><p class="text-[10px] text-gray-400">Origin</p><p class="font-medium">{{ $trip->origin ?? '—' }}</p></div>
                <div><p class="text-[10px] text-gray-400">Destination</p><p class="font-medium">{{ $trip->destination ?? '—' }}</p></div>
                <div><p class="text-[10px] text-gray-400">Dispatched</p><p class="font-medium">{{ $trip->dispatched_at?->format('d M Y, H:i') ?? '—' }}</p></div>
                <div><p class="text-[10px] text-gray-400">Delivered</p><p class="font-medium">{{ $trip->delivered_at?->format('d M Y, H:i') ?? '—' }}</p></div>
                <div><p class="text-[10px] text-gray-400">Cost</p><p class="font-medium">{{ number_format($trip->transport_cost) }}</p></div>
            </div>
            @can('transport.edit')
            <div class="mt-4 pt-4 border-t">
                <form id="statusForm" action="{{ route('admin.transport.trips.status', $trip) }}" method="POST" class="flex items-center gap-2">
                    @csrf
                    <select name="status" class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 outline-none">
                        <option value="planned" @selected($trip->status==='planned')>Planned</option>
                        <option value="loading" @selected($trip->status==='loading')>Loading</option>
                        <option value="in_transit" @selected($trip->status==='in_transit')>In Transit</option>
                        <option value="delivered" @selected($trip->status==='delivered')>Delivered</option>
                        <option value="closed" @selected($trip->status==='closed')>Closed</option>
                    </select>
                    <button type="submit" class="px-4 py-1.5 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg">Update Status</button>
                </form>
            </div>
            @endcan
        </div>
    </div>
    <div class="bg-white rounded-xl border p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-3">Notes</h3>
        <p class="text-sm text-gray-600">{{ $trip->notes ?? 'No notes.' }}</p>
    </div>
</div>
@push('scripts')
<script>
$('#statusForm').on('submit', function(e) {
    e.preventDefault(); $.ajax({ url: this.action, type: 'POST', data: $(this).serialize(),
        success: function(r) { if (r.success) { showToast('success', 'Success', r.message); setTimeout(() => location.reload(), 800); } },
        error: function() { showToast('error', 'Error', 'Could not update status'); }
    });
});
</script>
@endpush
@endsection
