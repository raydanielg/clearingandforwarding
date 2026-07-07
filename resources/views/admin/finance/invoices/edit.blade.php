@extends('layouts.admin')
@section('title', 'Edit Invoice - ' . config('app.name'))
@section('page_title', 'Edit: ' . $invoice->invoice_no)

@section('content')
<form id="invEditForm" action="{{ route('admin.invoices.update', $invoice) }}" method="POST" class="bg-white rounded-xl border p-6 space-y-4 max-w-4xl">
    @csrf @method('PUT')
    <div class="grid grid-cols-3 gap-4">
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Client</label><select name="client_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none" required>@foreach($clients as $c)<option value="{{ $c->id }}" @selected($invoice->client_id === $c->id)>{{ $c->name }}</option>@endforeach</select></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Date</label><input type="date" name="date" value="{{ old('date', $invoice->date?->format('Y-m-d')) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none" required></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label><input type="date" name="due_date" value="{{ old('due_date', $invoice->due_date?->format('Y-m-d')) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
    </div>

    <div class="border-t pt-4">
        <h4 class="text-sm font-semibold text-gray-900 mb-2">Line Items</h4>
        <div id="invEditItems" class="space-y-2">
            @foreach($invoice->items as $i)
            <div class="grid grid-cols-12 gap-2 items-start">
                <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $i->id }}">
                <select name="items[{{ $loop->index }}][charge_item_id]" class="col-span-3 px-2 py-1.5 text-xs rounded-lg border border-gray-200 focus:border-emerald-500 outline-none"><option value="">Charge...</option>@foreach($chargeItems as $c)<option value="{{ $c->id }}" @selected($i->charge_item_id === $c->id)>{{ $c->name }}</option>@endforeach</select>
                <input type="text" name="items[{{ $loop->index }}][description]" value="{{ $i->description ?? '' }}" placeholder="Description" class="col-span-4 px-2 py-1.5 text-xs rounded-lg border border-gray-200 focus:border-emerald-500 outline-none">
                <input type="number" name="items[{{ $loop->index }}][qty]" value="{{ $i->qty }}" placeholder="Qty" step="0.01" class="col-span-1 px-2 py-1.5 text-xs rounded-lg border border-gray-200 focus:border-emerald-500 outline-none">
                <input type="number" name="items[{{ $loop->index }}][unit_price]" value="{{ $i->unit_price }}" placeholder="Price" step="0.01" class="col-span-2 px-2 py-1.5 text-xs rounded-lg border border-gray-200 focus:border-emerald-500 outline-none">
                <input type="number" name="items[{{ $loop->index }}][vat_rate]" value="{{ $i->vat_rate }}" placeholder="VAT%" step="0.01" class="col-span-2 px-2 py-1.5 text-xs rounded-lg border border-gray-200 focus:border-emerald-500 outline-none">
            </div>
            @endforeach
        </div>
        <button type="button" onclick="addEditItem()" class="mt-2 text-xs font-medium text-emerald-600 hover:text-emerald-700">+ Add Line</button>
    </div>

    <div class="flex items-center gap-3 pt-4 border-t">
        <button type="submit" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">Update Invoice</button>
        <a href="{{ route('admin.invoices.show', $invoice) }}" class="px-4 py-2 text-sm font-medium text-gray-600">Cancel</a>
    </div>
</form>
@push('scripts')
<script>
let ei = {{ $invoice->items->count() }};
function addEditItem() {
    const html = `<div class="grid grid-cols-12 gap-2 items-start">
        <select name="items[${ei}][charge_item_id]" class="col-span-3 px-2 py-1.5 text-xs rounded-lg border border-gray-200 focus:border-emerald-500 outline-none"><option value="">Charge...</option>@foreach($chargeItems as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select>
        <input type="text" name="items[${ei}][description]" placeholder="Description" class="col-span-4 px-2 py-1.5 text-xs rounded-lg border border-gray-200 focus:border-emerald-500 outline-none">
        <input type="number" name="items[${ei}][qty]" placeholder="Qty" step="0.01" class="col-span-1 px-2 py-1.5 text-xs rounded-lg border border-gray-200 focus:border-emerald-500 outline-none">
        <input type="number" name="items[${ei}][unit_price]" placeholder="Price" step="0.01" class="col-span-2 px-2 py-1.5 text-xs rounded-lg border border-gray-200 focus:border-emerald-500 outline-none">
        <input type="number" name="items[${ei}][vat_rate]" placeholder="VAT%" step="0.01" class="col-span-2 px-2 py-1.5 text-xs rounded-lg border border-gray-200 focus:border-emerald-500 outline-none">
    </div>`;
    $('#invEditItems').append(html); ei++;
}
$('#invEditForm').on('submit', function(e) {
    e.preventDefault(); const form = this; const btn = form.querySelector('button[type="submit"]');
    btn.disabled = true; btn.textContent = 'Saving...';
    $.ajax({ url: form.action, type: 'POST', data: new FormData(form), processData: false, contentType: false,
        success: function(r) { if (r.success) { showToast('success', 'Success', r.message); setTimeout(() => window.location.href = '{{ route('admin.invoices.show', $invoice) }}', 800); } },
        error: function(x) { btn.disabled = false; btn.textContent = 'Update Invoice'; if (x.responseJSON?.errors) { Object.values(x.responseJSON.errors).forEach(e => showToast('error', 'Validation', e[0])); } else { showToast('error', 'Error', 'Something went wrong'); } }
    });
});
</script>
@endpush
@endsection
