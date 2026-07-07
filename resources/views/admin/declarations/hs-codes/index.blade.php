@extends('layouts.admin')
@section('title', 'HS Codes - ' . config('app.name'))
@section('page_title', 'Tariff / HS Codes')

@section('content')
<div class="flex items-center justify-between mb-4">
    <div class="relative">
        <input type="text" id="hsSearch" placeholder="Search code or description..." class="pl-9 pr-4 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none w-64">
        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
    </div>
    @can('hscodes.create')
    <button onclick="openHsModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>Add HS Code
    </button>
    @endcan
</div>
<div class="bg-white rounded-xl border overflow-hidden"><div id="hsTable">@include('admin.declarations.hs-codes.partials.table')</div></div>

{{-- Modal --}}
<div id="hsModal" class="fixed inset-0 bg-black/40 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4" id="hsModalTitle">Add HS Code</h3>
        <form id="hsForm" class="space-y-3">
            @csrf
            <input type="hidden" name="id" id="hsId">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Code <span class="text-red-500">*</span></label><input type="text" name="code" id="hsCode" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none" required></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-red-500">*</span></label><textarea name="description" id="hsDesc" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none" required></textarea></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Duty Rate (%)</label><input type="number" name="duty_rate" id="hsDuty" step="0.01" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">VAT Rate (%)</label><input type="number" name="vat_rate" id="hsVat" step="0.01" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            </div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Notes</label><textarea name="notes" id="hsNotes" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></textarea></div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeHsModal()" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg">Save</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let hsTimer;
$('#hsSearch').on('input', function() {
    clearTimeout(hsTimer);
    hsTimer = setTimeout(function() {
        const p = new URLSearchParams(); if ($('#hsSearch').val()) p.set('search', $('#hsSearch').val());
        $.ajax({ url: '{{ route('admin.hs-codes.index') }}?' + p.toString(), type: 'GET', dataType: 'json', success: function(r) { $('#hsTable').html(r.html); } });
    }, 300);
});
function openHsModal(data) {
    $('#hsModal').removeClass('hidden').addClass('flex');
    if (data) {
        $('#hsModalTitle').text('Edit HS Code');
        $('#hsId').val(data.id); $('#hsCode').val(data.code); $('#hsDesc').val(data.description);
        $('#hsDuty').val(data.duty_rate); $('#hsVat').val(data.vat_rate); $('#hsNotes').val(data.notes);
    } else { $('#hsModalTitle').text('Add HS Code'); $('#hsForm')[0].reset(); $('#hsId').val(''); }
}
function closeHsModal() { $('#hsModal').addClass('hidden').removeClass('flex'); }
function editHsCode(id, code, desc, duty, vat, notes) { openHsModal({id, code, description: desc, duty_rate: duty, vat_rate: vat, notes}); }
function deleteHsCode(id) {
    confirmDelete('{{ route('admin.hs-codes.destroy', '__id__') }}'.replace('__id__', id), {
        title: 'Delete HS Code?',
        text: 'This tariff code will be permanently removed.'
    });
}
$('#hsForm').on('submit', function(e) {
    e.preventDefault(); const id = $('#hsId').val();
    const url = id ? '{{ route('admin.hs-codes.update', '__id__') }}'.replace('__id__', id) : '{{ route('admin.hs-codes.store') }}';
    const method = id ? 'POST' : 'POST';
    const data = $(this).serialize(); if (id) data += '&_method=PUT';
    $.ajax({ url, type: method, data,
        success: function(r) { if (r.success) { showToast('success', 'Success', r.message); closeHsModal(); setTimeout(() => location.reload(), 800); } },
        error: function(x) { if (x.responseJSON?.errors) { Object.values(x.responseJSON.errors).forEach(e => showToast('error', 'Validation', e[0])); } }
    });
});
</script>
@endpush
@endsection
