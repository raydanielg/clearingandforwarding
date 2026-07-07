@extends('layouts.admin')
@section('title', 'Declaration ' . ($declaration->tansad_no ?? '—') . ' - ' . config('app.name'))
@section('page_title', 'Declaration: ' . ($declaration->tansad_no ?? '—'))

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        {{-- Declaration Info --}}
        <div class="bg-white rounded-xl border p-5">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">{{ $declaration->tansad_no ?? 'Draft Declaration' }}</h2>
                    <p class="text-sm text-gray-500">{{ $declaration->shipmentFile?->file_no }} · {{ $declaration->shipmentFile?->client?->name }}</p>
                </div>
                <span class="text-[10px] px-2 py-0.5 rounded-full {{ $declaration->status === 'released' ? 'bg-emerald-50 text-emerald-700' : ($declaration->status === 'queried' ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700') }}">{{ ucfirst($declaration->status) }}</span>
            </div>
            <div class="grid grid-cols-4 gap-4 text-sm">
                <div><p class="text-[10px] text-gray-400">Regime</p><p class="font-medium">{{ $declaration->regime_code ?? '—' }}</p></div>
                <div><p class="text-[10px] text-gray-400">Duty</p><p class="font-medium">{{ number_format($declaration->duty_amount) }}</p></div>
                <div><p class="text-[10px] text-gray-400">VAT</p><p class="font-medium">{{ number_format($declaration->vat_amount) }}</p></div>
                <div><p class="text-[10px] text-gray-400">Total Taxes</p><p class="font-medium text-emerald-600">{{ number_format($declaration->total_taxes) }}</p></div>
            </div>
            @can('declarations.edit')
            <div class="mt-4 pt-4 border-t flex gap-2">
                <a href="{{ route('admin.declarations.edit', $declaration) }}" class="px-3 py-1.5 text-xs font-medium text-amber-600 hover:bg-amber-50 rounded-lg">Edit</a>
                <form id="statusForm" action="{{ route('admin.declarations.update', $declaration) }}" method="POST" class="flex items-center gap-2">
                    @csrf @method('PUT')
                    <select name="status" class="px-2 py-1 text-xs rounded-lg border border-gray-200 focus:border-emerald-500 outline-none">
                        <option value="draft" @selected($declaration->status === 'draft')>Draft</option>
                        <option value="lodged" @selected($declaration->status === 'lodged')>Lodged</option>
                        <option value="queried" @selected($declaration->status === 'queried')>Queried</option>
                        <option value="assessed" @selected($declaration->status === 'assessed')>Assessed</option>
                        <option value="paid" @selected($declaration->status === 'paid')>Paid</option>
                        <option value="released" @selected($declaration->status === 'released')>Released</option>
                    </select>
                    <button type="submit" class="px-3 py-1 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg">Update Status</button>
                </form>
            </div>
            @endcan
        </div>

        {{-- Items --}}
        <div class="bg-white rounded-xl border p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Declaration Items</h3>
            <table class="w-full text-sm">
                <thead><tr class="text-left text-xs text-gray-500 border-b"><th class="pb-2 font-medium">HS Code</th><th class="pb-2 font-medium">Description</th><th class="pb-2 font-medium text-right">Qty</th><th class="pb-2 font-medium text-right">Value</th><th class="pb-2 font-medium text-right">Duty</th></tr></thead>
                <tbody>
                    @forelse($declaration->items as $item)
                    <tr class="border-b border-gray-50">
                        <td class="py-2 font-mono text-xs">{{ $item->hsCode?->code ?? '—' }}</td>
                        <td class="py-2 text-xs">{{ $item->description ?? $item->hsCode?->description }}</td>
                        <td class="py-2 text-xs text-right">{{ $item->qty }}</td>
                        <td class="py-2 text-xs text-right">{{ number_format($item->customs_value) }}</td>
                        <td class="py-2 text-xs text-right">{{ number_format($item->computed_duty) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="py-6 text-center text-sm text-gray-400">No items.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Customs Queries --}}
        <div class="bg-white rounded-xl border p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Customs Queries</h3>
            <div class="space-y-3">
                @forelse($declaration->queries as $q)
                <div class="border border-gray-100 rounded-lg p-3">
                    <p class="text-sm font-medium text-gray-900">Q: {{ $q->query_text }}</p>
                    <p class="text-[10px] text-gray-400 mt-0.5">{{ $q->raised_at?->format('d M Y, H:i') }}</p>
                    @if($q->response_text)
                    <p class="text-sm text-gray-600 mt-2">A: {{ $q->response_text }}</p>
                    <p class="text-[10px] text-gray-400 mt-0.5">{{ $q->responded_at?->format('d M Y, H:i') }}</p>
                    @endif
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center py-4">No queries.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-xl border p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Timeline</h3>
            <div class="space-y-2 text-sm">
                <div><p class="text-[10px] text-gray-400">Declared</p><p class="font-medium">{{ $declaration->declared_at?->format('d M Y, H:i') ?? '—' }}</p></div>
                <div><p class="text-[10px] text-gray-400">Assessed</p><p class="font-medium">{{ $declaration->assessed_at?->format('d M Y, H:i') ?? '—' }}</p></div>
                <div><p class="text-[10px] text-gray-400">Officer</p><p class="font-medium">{{ $declaration->officer?->name ?? '—' }}</p></div>
            </div>
        </div>
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
