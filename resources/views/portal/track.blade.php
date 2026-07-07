@extends('layouts.portal')
@section('title', 'Track Shipment - ' . config('app.name'))
@section('page_title', 'Track Shipment')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="bg-white rounded-xl border p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Track Your Shipment</h3>
        <form id="trackForm" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">File No or Bill of Lading</label>
                <input type="text" name="query" id="trackInput" placeholder="e.g. CAF-2026-001 or MAEU1234567" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none" required>
            </div>
            <button type="submit" class="w-full px-6 py-2.5 text-sm font-bold text-gray-900 bg-gradient-to-r from-gold-300 to-gold-400 hover:from-gold-400 hover:to-gold-500 rounded-lg shadow-md transition-all">Track Shipment</button>
        </form>
    </div>
    <div id="trackResult"></div>
</div>
@push('scripts')
<script>
$('#trackForm').on('submit', function(e) {
    e.preventDefault();
    const q = $('#trackInput').val().trim();
    if (!q) return;
    $.ajax({ url: '{{ route('portal.track.search') }}', type: 'POST', data: { _token: '{{ csrf_token() }}', query: q },
        success: function(r) {
            if (r.success) { $('#trackResult').html(r.html); }
            else { $('#trackResult').html('<div class="bg-white rounded-xl border p-6 text-center"><p class="text-sm text-gray-400">' + r.message + '</p></div>'); }
        },
        error: function() { $('#trackResult').html('<div class="bg-white rounded-xl border p-6 text-center"><p class="text-sm text-red-500">Search failed. Please try again.</p></div>'); }
    });
});
</script>
@endpush
@endsection
