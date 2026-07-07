@extends('layouts.portal')

@section('title', 'Overview - ' . config('app.name', 'CAF'))
@section('page_title', 'My Overview')

@section('content')
{{-- Welcome Banner --}}
<div class="bg-gradient-to-br from-emerald-600 to-emerald-700 rounded-2xl p-6 text-white mb-6 relative overflow-hidden">
    <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20"></div>
    <div class="absolute bottom-0 right-12 w-24 h-24 bg-gold-400/10 rounded-full -mb-12"></div>
    <div class="relative z-10">
        <h2 class="text-xl font-bold">Welcome back, {{ $client->name }}!</h2>
        <p class="text-emerald-100 text-sm mt-1">Client Code: {{ $client->code }} · Here's an overview of your shipments and account.</p>
    </div>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    <div class="bg-gradient-to-br from-emerald-600 to-emerald-700 rounded-xl border border-emerald-500 p-4 text-white relative overflow-hidden hover:shadow-lg transition-shadow">
        <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
        <div class="relative z-10">
            <span class="text-[10px] font-medium text-emerald-100">Active Shipments</span>
            <p class="text-xl font-bold tracking-tight text-white mt-2">{{ $stats['active_files'] }}</p>
            <p class="text-[10px] text-emerald-200 font-medium mt-1">In progress</p>
        </div>
    </div>
    <div class="bg-gradient-to-br from-amber-400 to-amber-500 rounded-xl border border-amber-300 p-4 text-white relative overflow-hidden hover:shadow-lg transition-shadow">
        <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
        <div class="relative z-10">
            <span class="text-[10px] font-medium text-amber-50">Total Files</span>
            <p class="text-xl font-bold tracking-tight text-white mt-2">{{ $stats['total_files'] }}</p>
            <p class="text-[10px] text-amber-100 font-medium mt-1">All time</p>
        </div>
    </div>
    <div class="bg-gradient-to-br from-sky-500 to-sky-600 rounded-xl border border-sky-400 p-4 text-white relative overflow-hidden hover:shadow-lg transition-shadow">
        <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
        <div class="relative z-10">
            <span class="text-[10px] font-medium text-sky-100">Active Policies</span>
            <p class="text-xl font-bold tracking-tight text-white mt-2">{{ $stats['active_policies'] }}</p>
            <p class="text-[10px] text-sky-200 font-medium mt-1">{{ $stats['total_policies'] }} total</p>
        </div>
    </div>
    <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl border border-red-400 p-4 text-white relative overflow-hidden hover:shadow-lg transition-shadow">
        <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
        <div class="relative z-10">
            <span class="text-[10px] font-medium text-red-100">Outstanding Balance</span>
            <p class="text-xl font-bold tracking-tight text-white mt-2">{{ number_format($stats['outstanding']) }}</p>
            <p class="text-[10px] text-red-200 font-medium mt-1">TZS</p>
        </div>
    </div>
</div>

{{-- Recent Shipments --}}
<div class="bg-white rounded-xl border overflow-hidden mb-6">
    <div class="px-5 py-4 border-b flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-900">Recent Shipments</h3>
        <a href="{{ route('portal.shipments.index') }}" class="text-xs font-medium text-emerald-600 hover:text-emerald-700">View All →</a>
    </div>
    <table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50 border-b">
            <th class="px-5 py-3 font-medium">File No</th><th class="px-5 py-3 font-medium">Type</th><th class="px-5 py-3 font-medium">BL/AWB</th><th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3 font-medium">Opened</th>
        </tr></thead>
        <tbody>
            @forelse($files as $f)
            <tr class="border-b border-gray-100 hover:bg-gray-50/50">
                <td class="px-5 py-3"><a href="{{ route('portal.shipments.show', $f) }}" class="text-xs font-medium text-emerald-600 hover:underline">{{ $f->file_no }}</a></td>
                <td class="px-5 py-3 text-xs text-gray-700 uppercase">{{ $f->type }}</td>
                <td class="px-5 py-3 text-xs text-gray-500">{{ $f->bl_awb_no ?? '—' }}</td>
                <td class="px-5 py-3"><span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 capitalize">{{ str_replace('_', ' ', $f->status) }}</span></td>
                <td class="px-5 py-3 text-xs text-gray-500">{{ $f->opened_at?->format('d M Y') ?? '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-5 py-12 text-center text-sm text-gray-400">No shipments yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Recent Invoices --}}
<div class="bg-white rounded-xl border overflow-hidden mb-6">
    <div class="px-5 py-4 border-b flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-900">Recent Invoices</h3>
        <a href="{{ route('portal.invoices.index') }}" class="text-xs font-medium text-emerald-600 hover:text-emerald-700">View All →</a>
    </div>
    <table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50 border-b">
            <th class="px-5 py-3 font-medium">Invoice No</th><th class="px-5 py-3 font-medium">Date</th><th class="px-5 py-3 font-medium">Total</th><th class="px-5 py-3 font-medium">Status</th>
        </tr></thead>
        <tbody>
            @forelse($invoices as $inv)
            <tr class="border-b border-gray-100 hover:bg-gray-50/50">
                <td class="px-5 py-3"><a href="{{ route('portal.invoices.show', $inv) }}" class="text-xs font-medium text-emerald-600 hover:underline">{{ $inv->invoice_no }}</a></td>
                <td class="px-5 py-3 text-xs text-gray-500">{{ $inv->date?->format('d M Y') }}</td>
                <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ number_format($inv->total) }}</td>
                <td class="px-5 py-3"><span class="text-[10px] px-2 py-0.5 rounded-full @if($inv->status === 'paid') bg-emerald-50 text-emerald-700 @elseif($inv->status === 'overdue') bg-red-50 text-red-700 @else bg-amber-50 text-amber-700 @endif capitalize">{{ str_replace('_', ' ', $inv->status) }}</span></td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-5 py-12 text-center text-sm text-gray-400">No invoices yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Recent Insurance Policies --}}
<div class="bg-white rounded-xl border overflow-hidden mb-6">
    <div class="px-5 py-4 border-b flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-900">Insurance Policies</h3>
        <a href="{{ route('portal.insurance.index') }}" class="text-xs font-medium text-emerald-600 hover:text-emerald-700">View All →</a>
    </div>
    <table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50 border-b">
            <th class="px-5 py-3 font-medium">Policy No</th><th class="px-5 py-3 font-medium">Type</th><th class="px-5 py-3 font-medium">Cover Period</th><th class="px-5 py-3 font-medium">Status</th>
        </tr></thead>
        <tbody>
            @forelse($policies as $pol)
            <tr class="border-b border-gray-100 hover:bg-gray-50/50">
                <td class="px-5 py-3"><a href="{{ route('portal.insurance.show', $pol) }}" class="text-xs font-medium text-emerald-600 hover:underline">{{ $pol->policy_no }}</a></td>
                <td class="px-5 py-3 text-xs text-gray-700">{{ ucfirst(str_replace('_', ' ', $pol->type)) }}</td>
                <td class="px-5 py-3 text-xs text-gray-500">{{ $pol->cover_start_date?->format('d M Y') ?? '—' }} → {{ $pol->cover_end_date?->format('d M Y') ?? '—' }}</td>
                <td class="px-5 py-3"><span class="text-[10px] px-2 py-0.5 rounded-full @if($pol->status === 'active') bg-emerald-50 text-emerald-700 @elseif($pol->status === 'expired') bg-red-50 text-red-700 @else bg-amber-50 text-amber-700 @endif capitalize">{{ $pol->status }}</span></td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-5 py-12 text-center text-sm text-gray-400">No insurance policies yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Quick Track --}}
<div class="bg-white rounded-xl border p-6">
    <h3 class="text-sm font-semibold text-gray-900 mb-4">Quick Track</h3>
    <form id="quickTrackForm" class="flex gap-3">
        <div class="flex-1 relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <input type="text" name="query" id="trackQuery" placeholder="Enter File No or Bill of Lading..." class="w-full pl-11 pr-4 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all text-sm">
        </div>
        <button type="submit" class="px-6 py-2.5 text-sm font-bold text-gray-900 bg-gradient-to-r from-gold-300 to-gold-400 hover:from-gold-400 hover:to-gold-500 rounded-lg shadow-md hover:shadow-lg transition-all">Track</button>
    </form>
    <div id="trackResult" class="mt-4"></div>
</div>
@push('scripts')
<script>
$('#quickTrackForm').on('submit', function(e) {
    e.preventDefault();
    const q = $('#trackQuery').val().trim();
    if (!q) return;
    $.ajax({ url: '{{ route('portal.track.search') }}', type: 'POST', data: { _token: '{{ csrf_token() }}', query: q },
        success: function(r) {
            if (r.success) { $('#trackResult').html(r.html); }
            else { $('#trackResult').html('<p class="text-sm text-gray-400">' + r.message + '</p>'); }
        },
        error: function() { $('#trackResult').html('<p class="text-sm text-red-500">Search failed. Please try again.</p>'); }
    });
});
</script>
@endpush
@endsection
