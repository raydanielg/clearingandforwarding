@extends('layouts.portal')
@section('title', $file->file_no . ' - ' . config('app.name'))
@section('page_title', $file->file_no)

@section('content')
<div class="space-y-6">
    {{-- Back link --}}
    <a href="{{ route('portal.shipments.index') }}" class="text-xs font-medium text-emerald-600 hover:text-emerald-700">← Back to Shipments</a>

    {{-- File Header --}}
    <div class="bg-white rounded-xl border p-6">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-lg font-bold text-gray-900">{{ $file->file_no }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ ucfirst($file->type) }} · {{ ucfirst($file->mode) }} · {{ str_replace('_', ' ', $file->status) }}</p>
            </div>
            <span class="text-[10px] px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 capitalize">{{ str_replace('_', ' ', $file->status) }}</span>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
            <div><p class="text-[10px] text-gray-500 font-medium">BL / AWB No</p><p class="text-sm text-gray-900 mt-0.5">{{ $file->bl_awb_no ?? '—' }}</p></div>
            <div><p class="text-[10px] text-gray-500 font-medium">Vessel / Flight</p><p class="text-sm text-gray-900 mt-0.5">{{ $file->vessel_flight ?? '—' }}</p></div>
            <div><p class="text-[10px] text-gray-500 font-medium">Port of Loading</p><p class="text-sm text-gray-900 mt-0.5">{{ $file->port_of_loading ?? '—' }}</p></div>
            <div><p class="text-[10px] text-gray-500 font-medium">Port of Discharge</p><p class="text-sm text-gray-900 mt-0.5">{{ $file->port_of_discharge ?? '—' }}</p></div>
            <div><p class="text-[10px] text-gray-500 font-medium">ETA</p><p class="text-sm text-gray-900 mt-0.5">{{ $file->eta?->format('d M Y') ?? '—' }}</p></div>
            <div><p class="text-[10px] text-gray-500 font-medium">Discharge Date</p><p class="text-sm text-gray-900 mt-0.5">{{ $file->discharge_date?->format('d M Y') ?? '—' }}</p></div>
            <div><p class="text-[10px] text-gray-500 font-medium">Shipping Line</p><p class="text-sm text-gray-900 mt-0.5">{{ $file->shippingLine?->name ?? '—' }}</p></div>
            <div><p class="text-[10px] text-gray-500 font-medium">Terminal</p><p class="text-sm text-gray-900 mt-0.5">{{ $file->terminal?->name ?? '—' }}</p></div>
        </div>
    </div>

    {{-- Milestones --}}
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b"><h3 class="text-sm font-semibold text-gray-900">Tracking Timeline</h3></div>
        <div class="p-5">
            @forelse($file->milestones as $m)
            <div class="flex items-start gap-3 pb-4 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                <div class="w-8 h-8 rounded-full bg-emerald-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900 capitalize">{{ str_replace('_', ' ', $m->stage) }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $m->happened_at?->format('d M Y, H:i') }}</p>
                    @if($m->remarks)<p class="text-xs text-gray-600 mt-1">{{ $m->remarks }}</p>@endif
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-6">No milestones recorded yet.</p>
            @endforelse
        </div>
    </div>

    {{-- Documents --}}
    @if($file->documents->isNotEmpty())
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b"><h3 class="text-sm font-semibold text-gray-900">Documents</h3></div>
        <table class="w-full text-sm">
            <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50 border-b">
                <th class="px-5 py-3 font-medium">Type</th><th class="px-5 py-3 font-medium">Reference</th><th class="px-5 py-3 font-medium">Uploaded</th><th class="px-5 py-3 font-medium">Verified</th>
            </tr></thead>
            <tbody>
                @foreach($file->documents as $doc)
                <tr class="border-b border-gray-100 hover:bg-gray-50/50">
                    <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $doc->documentType?->name ?? '—' }}</td>
                    <td class="px-5 py-3 text-xs text-gray-500">{{ $doc->reference_no ?? '—' }}</td>
                    <td class="px-5 py-3 text-xs text-gray-500">{{ $doc->created_at?->format('d M Y') }}</td>
                    <td class="px-5 py-3">@if($doc->verified_at)<span class="text-[10px] text-emerald-600 font-medium">Verified</span>@else<span class="text-[10px] text-gray-400 font-medium">Pending</span>@endif</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Consignments --}}
    @if($file->consignments->isNotEmpty())
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b"><h3 class="text-sm font-semibold text-gray-900">Cargo Details</h3></div>
        @foreach($file->consignments as $cons)
        <div class="p-5 border-b border-gray-50 last:border-0">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div><p class="text-[10px] text-gray-500 font-medium">Packages</p><p class="text-sm text-gray-900 mt-0.5">{{ $cons->packages_count }} {{ $cons->package_type }}</p></div>
                <div><p class="text-[10px] text-gray-500 font-medium">Gross Weight</p><p class="text-sm text-gray-900 mt-0.5">{{ number_format($cons->gross_weight) }} kg</p></div>
                <div><p class="text-[10px] text-gray-500 font-medium">Volume</p><p class="text-sm text-gray-900 mt-0.5">{{ number_format($cons->volume_cbm, 3) }} CBM</p></div>
                <div><p class="text-[10px] text-gray-500 font-medium">Cargo Value</p><p class="text-sm text-gray-900 mt-0.5">{{ number_format($cons->cargo_value) }} {{ $cons->cargo_currency }}</p></div>
            </div>
            @if($cons->containers->isNotEmpty())
            <div class="mt-4 space-y-2">
                @foreach($cons->containers as $c)
                <div class="flex items-center gap-3 px-3 py-2 bg-gray-50 rounded-lg">
                    <span class="text-xs font-mono font-medium text-gray-900">{{ $c->container_no }}</span>
                    <span class="text-[10px] text-gray-500">{{ $c->size }}</span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 capitalize">{{ str_replace('_', ' ', $c->status) }}</span>
                </div>
                @endforeach
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
