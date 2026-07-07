@extends('layouts.admin')

@section('title', $file->file_no . ' - ' . config('app.name'))
@section('page_title', $file->file_no)

@section('content')
@php $statusColors = ['open'=>'emerald','declaration'=>'sky','port_clearance'=>'amber','transport'=>'violet','delivered'=>'emerald','invoiced'=>'sky','closed'=>'gray','cancelled'=>'red','draft'=>'gray']; @endphp

{{-- File Header --}}
<div class="bg-white rounded-xl border p-5 mb-6">
    <div class="flex items-start justify-between">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="text-xl font-bold text-gray-900 font-mono">{{ $file->file_no }}</h2>
                <span class="text-[10px] px-2 py-0.5 rounded-full bg-{{ $statusColors[$file->status] ?? 'gray' }}-50 text-{{ $statusColors[$file->status] ?? 'gray' }}-700">{{ ucfirst(str_replace('_', ' ', $file->status)) }}</span>
            </div>
            <p class="text-sm text-gray-500 mt-1">{{ $file->client?->name }} · {{ ucfirst($file->type) }} · {{ ucfirst($file->mode) }}</p>
        </div>
        <div class="flex gap-2">
            @can('files.edit')<a href="{{ route('admin.files.edit', $file) }}" class="px-3 py-1.5 text-xs font-medium text-amber-600 hover:bg-amber-50 rounded-lg">Edit</a>@endcan
            @can('files.close')
            @if($file->status !== 'closed' && $file->status !== 'cancelled')
            <button onclick="closeFile()" class="px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 rounded-lg">Close File</button>
            @endif
            @endcan
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Left: Details --}}
    <div class="lg:col-span-2 space-y-6">
        {{-- Shipment Details --}}
        <div class="bg-white rounded-xl border p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Shipment Details</h3>
            <div class="grid grid-cols-3 gap-4 text-sm">
                <div><p class="text-[10px] text-gray-400">BL / AWB No</p><p class="font-medium text-gray-900">{{ $file->bl_awb_no ?? '—' }}</p></div>
                <div><p class="text-[10px] text-gray-400">Vessel / Flight</p><p class="font-medium text-gray-900">{{ $file->vessel_flight ?? '—' }}</p></div>
                <div><p class="text-[10px] text-gray-400">Shipping Line</p><p class="font-medium text-gray-900">{{ $file->shippingLine?->name ?? '—' }}</p></div>
                <div><p class="text-[10px] text-gray-400">Port of Loading</p><p class="font-medium text-gray-900">{{ $file->port_of_loading ?? '—' }}</p></div>
                <div><p class="text-[10px] text-gray-400">Port of Discharge</p><p class="font-medium text-gray-900">{{ $file->port_of_discharge ?? '—' }}</p></div>
                <div><p class="text-[10px] text-gray-400">Terminal / ICD</p><p class="font-medium text-gray-900">{{ $file->terminal?->name ?? '—' }}</p></div>
                <div><p class="text-[10px] text-gray-400">ETA</p><p class="font-medium text-gray-900">{{ $file->eta?->format('d M Y') ?? '—' }}</p></div>
                <div><p class="text-[10px] text-gray-400">Discharge Date</p><p class="font-medium text-gray-900">{{ $file->discharge_date?->format('d M Y') ?? '—' }}</p></div>
                <div><p class="text-[10px] text-gray-400">Free Days Expiry</p><p class="font-medium {{ $file->free_days_expiry && $file->free_days_expiry < now() ? 'text-red-600' : 'text-gray-900' }}">{{ $file->free_days_expiry?->format('d M Y') ?? '—' }}</p></div>
            </div>
        </div>

        {{-- Milestones --}}
        <div class="bg-white rounded-xl border p-5">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-gray-900">Milestone Timeline</h3>
                @can('milestones.update')
                <form id="milestoneForm" action="{{ route('admin.files.milestone', $file) }}" method="POST" class="flex items-center gap-2">
                    @csrf
                    <select name="stage" class="px-2 py-1 text-xs rounded-lg border border-gray-200 focus:border-emerald-500 outline-none">
                        <option value="">Add milestone...</option>
                        <option value="docs_complete">Documents Complete</option><option value="declaration_lodged">Declaration Lodged</option>
                        <option value="taxes_paid">Taxes Paid</option><option value="customs_released">Customs Released</option>
                        <option value="do_collected">DO Collected</option><option value="port_charges_paid">Port Charges Paid</option>
                        <option value="verification">Verification</option><option value="gate_out">Gate Out</option>
                        <option value="in_transit">In Transit</option><option value="delivered">Delivered</option>
                        <option value="empty_returned">Empty Returned</option><option value="file_closed">File Closed</option>
                    </select>
                    <button type="submit" class="px-3 py-1 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg">Add</button>
                </form>
                @endcan
            </div>
            <div class="space-y-3">
                @forelse($file->milestones as $m)
                <div class="flex items-start gap-3">
                    <div class="w-2 h-2 rounded-full bg-emerald-500 mt-1.5 flex-shrink-0"></div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $m->stage)) }}</p>
                        <p class="text-xs text-gray-400">{{ $m->happened_at->format('d M Y, H:i') }} · {{ $m->recordedBy?->name }}</p>
                        @if($m->remarks)<p class="text-xs text-gray-500 mt-0.5">{{ $m->remarks }}</p>@endif
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center py-4">No milestones recorded yet.</p>
                @endforelse
            </div>
        </div>

        {{-- Documents --}}
        <div class="bg-white rounded-xl border p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Documents</h3>
            <div class="space-y-2">
                @forelse($file->documents as $doc)
                <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $doc->documentType?->name }}</p>
                            <p class="text-[10px] text-gray-400">Ref: {{ $doc->reference_no ?? '—' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($doc->verified_at)<span class="text-[10px] text-emerald-600 font-medium">Verified</span>@endif
                        @if($doc->original_received)<span class="text-[10px] text-sky-600 font-medium">Original</span>@endif
                        <a href="{{ route('admin.documents.download', $doc) }}" class="p-1 text-gray-400 hover:text-emerald-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg></a>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center py-4">No documents uploaded.</p>
                @endforelse
            </div>
        </div>

        {{-- Declaration --}}
        @if($file->declaration)
        <div class="bg-white rounded-xl border p-5">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-gray-900">Declaration (TANSAD)</h3>
                <a href="{{ route('admin.declarations.show', $file->declaration) }}" class="text-xs font-medium text-emerald-600 hover:text-emerald-700">View Details</a>
            </div>
            <div class="grid grid-cols-4 gap-4 text-sm">
                <div><p class="text-[10px] text-gray-400">TANSAD No</p><p class="font-medium text-gray-900">{{ $file->declaration->tansad_no ?? '—' }}</p></div>
                <div><p class="text-[10px] text-gray-400">Status</p><p class="font-medium text-gray-900">{{ ucfirst($file->declaration->status) }}</p></div>
                <div><p class="text-[10px] text-gray-400">Total Taxes</p><p class="font-medium text-gray-900">{{ number_format($file->declaration->total_taxes) }}</p></div>
                <div><p class="text-[10px] text-gray-400">Items</p><p class="font-medium text-gray-900">{{ $file->declaration->items->count() }}</p></div>
            </div>
        </div>
        @endif

        {{-- Trips --}}
        @if($file->trips->isNotEmpty())
        <div class="bg-white rounded-xl border p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Transport / Trips</h3>
            <div class="space-y-2">
                @foreach($file->trips as $trip)
                <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                    <div class="text-sm">
                        <span class="font-medium text-gray-900">{{ $trip->truck?->plate_no }}</span>
                        <span class="text-gray-400 mx-2">·</span>
                        <span class="text-gray-600">{{ $trip->driver?->name }}</span>
                    </div>
                    <span class="text-[10px] px-2 py-0.5 rounded-full {{ $trip->status === 'delivered' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">{{ ucfirst(str_replace('_', ' ', $trip->status)) }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- Right: Sidebar --}}
    <div class="space-y-6">
        {{-- Assignment --}}
        <div class="bg-white rounded-xl border p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Assignment</h3>
            <div class="space-y-2 text-sm">
                <div><p class="text-[10px] text-gray-400">Declaration Officer</p><p class="font-medium text-gray-900">{{ $file->declarationOfficer?->name ?? 'Unassigned' }}</p></div>
                <div><p class="text-[10px] text-gray-400">Clearing Officer</p><p class="font-medium text-gray-900">{{ $file->clearingOfficer?->name ?? 'Unassigned' }}</p></div>
                <div><p class="text-[10px] text-gray-400">Opened By</p><p class="font-medium text-gray-900">{{ $file->openedBy?->name }}</p></div>
                <div><p class="text-[10px] text-gray-400">Opened At</p><p class="font-medium text-gray-900">{{ $file->opened_at?->format('d M Y, H:i') }}</p></div>
            </div>
        </div>

        {{-- Financial Summary --}}
        <div class="bg-white rounded-xl border p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Financial Summary</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Invoices</span><span class="font-medium">{{ $file->invoices->count() }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Invoiced Amount</span><span class="font-medium">TZS {{ number_format($file->invoices->sum('total')) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Collected</span><span class="font-medium text-emerald-600">TZS {{ number_format($file->invoices->sum('amount_paid')) }}</span></div>
                <div class="flex justify-between border-t pt-2"><span class="text-gray-500">Disbursements</span><span class="font-medium">{{ $file->disbursements->count() }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Disbursed</span><span class="font-medium text-red-600">TZS {{ number_format($file->disbursements->sum('amount')) }}</span></div>
            </div>
        </div>

        @if($file->remarks)
        <div class="bg-white rounded-xl border p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-2">Remarks</h3>
            <p class="text-sm text-gray-600">{{ $file->remarks }}</p>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
$('#milestoneForm').on('submit', function(e) {
    e.preventDefault(); if (!$('select[name="stage"]', this).val()) return;
    $.ajax({ url: this.action, type: 'POST', data: $(this).serialize(),
        success: function(r) { if (r.success) { showToast('success', 'Success', r.message); setTimeout(() => location.reload(), 800); } },
        error: function() { showToast('error', 'Error', 'Could not add milestone'); }
    });
});
function closeFile() {
    confirmAction('{{ route('admin.files.close', $file) }}', {
        title: 'Close This File?',
        text: 'This will mark the shipment file as closed. You can reopen it later if needed.',
        successMsg: 'File closed successfully!'
    });
}
</script>
@endpush
@endsection
