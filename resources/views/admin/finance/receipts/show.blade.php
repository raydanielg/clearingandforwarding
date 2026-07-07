@extends('layouts.admin')
@section('title', $receipt->receipt_no . ' - ' . config('app.name'))
@section('page_title', $receipt->receipt_no)

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl border p-6">
        <div class="flex items-start justify-between mb-4">
            <div><h2 class="text-xl font-bold text-gray-900 font-mono">{{ $receipt->receipt_no }}</h2><p class="text-sm text-gray-500">{{ $receipt->client?->name }}</p></div>
            @can('receipts.create')<button onclick="voidReceipt()" class="px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 rounded-lg">Void</button>@endcan
        </div>
        <dl class="space-y-2 text-sm">
            <div class="flex justify-between"><dt class="text-gray-500">Invoice</dt><dd class="font-medium">{{ $receipt->invoice?->invoice_no ?? '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Date</dt><dd class="font-medium">{{ $receipt->date?->format('d M Y') }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Method</dt><dd class="font-medium">{{ ucfirst(str_replace('_', ' ', $receipt->method)) }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Reference</dt><dd class="font-medium">{{ $receipt->reference ?? '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Received By</dt><dd class="font-medium">{{ $receipt->receivedBy?->name }}</dd></div>
            <div class="flex justify-between border-t pt-2"><dt class="text-gray-500 font-medium">Amount</dt><dd class="text-lg font-bold text-emerald-600">{{ number_format($receipt->amount) }}</dd></div>
        </dl>
    </div>
</div>
@push('scripts')
<script>
function voidReceipt(){if(!confirm('Void this receipt? This will reverse the payment.'))return;$.ajax({url:'{{route('admin.receipts.destroy',$receipt)}}',type:'POST',data:{_method:'DELETE',_token:'{{csrf_token()}}'},success:function(r){showToast('success','Voided',r.message);setTimeout(()=>window.location.href='{{route('admin.receipts.index')}}',800);},error:function(){showToast('error','Error','Could not void receipt');}});}
</script>
@endpush
@endsection
