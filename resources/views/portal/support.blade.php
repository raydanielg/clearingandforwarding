@extends('layouts.portal')
@section('title', 'Support - ' . config('app.name'))
@section('page_title', 'Support / Queries')

@section('content')
<div class="max-w-2xl space-y-6">
    <div class="bg-white rounded-xl border p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Contact Support</h3>
        <div class="space-y-3">
            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <div><p class="text-xs font-medium text-gray-900">Email</p><p class="text-xs text-gray-500">{{ config('app.name') }} Support</p></div>
            </div>
            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                <div><p class="text-xs font-medium text-gray-900">Phone</p><p class="text-xs text-gray-500">Call us during business hours</p></div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Send a Query</h3>
        <form id="supportForm" class="space-y-3">
            @csrf
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Subject</label><input type="text" name="subject" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none" required></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Message</label><textarea name="message" rows="4" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none" required></textarea></div>
            <button type="submit" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg">Send Query</button>
        </form>
    </div>
</div>
@push('scripts')
<script>
$('#supportForm').on('submit', function(e) {
    e.preventDefault();
    $.ajax({ url: '{{ route('portal.support') }}', type: 'POST', data: $(this).serialize(),
        success: function(r) { if (r.success) { showToast('success', 'Sent', r.message); $('#supportForm')[0].reset(); } },
        error: function(x) { if (x.responseJSON?.errors) { Object.values(x.responseJSON.errors).forEach(e => showToast('error', 'Validation', e[0])); } else { showToast('error', 'Error', 'Could not send query'); } }
    });
});
</script>
@endpush
@endsection
