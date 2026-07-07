@extends('layouts.admin')
@section('title', 'Disbursement Details - ' . config('app.name'))
@section('page_title', 'Disbursement Details')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl border p-6">
        <div class="flex items-start justify-between mb-4">
            <div><h2 class="text-lg font-bold text-gray-900">{{ $disbursement->payee ?? $disbursement->agency?->name }}</h2><p class="text-sm text-gray-500">{{ $disbursement->shipmentFile?->file_no }}</p></div>
            <div class="flex gap-2">
                @if(!$disbursement->approved_by)@can('disbursements.approve')<button onclick="approveDisb()" class="px-3 py-1.5 text-xs font-medium text-emerald-600 hover:bg-emerald-50 rounded-lg">Approve</button>@endcan @endif
                @can('disbursements.edit')<a href="{{ route('admin.disbursements.edit', $disbursement) }}" class="px-3 py-1.5 text-xs font-medium text-amber-600 hover:bg-amber-50 rounded-lg">Edit</a>@endcan
            </div>
        </div>
        <dl class="space-y-2 text-sm">
            <div class="flex justify-between"><dt class="text-gray-500">Agency</dt><dd class="font-medium">{{ $disbursement->agency?->name ?? '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Description</dt><dd class="font-medium">{{ $disbursement->description ?? '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Method</dt><dd class="font-medium">{{ ucfirst($disbursement->method) }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Reference</dt><dd class="font-medium">{{ $disbursement->reference_no ?? '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Paid At</dt><dd class="font-medium">{{ $disbursement->paid_at?->format('d M Y, H:i') }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Recorded By</dt><dd class="font-medium">{{ $disbursement->recordedBy?->name }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Approved By</dt><dd class="font-medium">{{ $disbursement->approvedBy?->name ?? 'Pending' }}</dd></div>
            <div class="flex justify-between border-t pt-2"><dt class="font-medium">Amount</dt><dd class="text-lg font-bold text-red-600">{{ number_format($disbursement->amount) }}</dd></div>
        </dl>
    </div>
</div>
@push('scripts')
<script>function approveDisb(){confirmAction('{{route('admin.disbursements.approve',$disbursement)}}',{title:'Approve Disbursement?',text:'This will approve the disbursement for payment.',successMsg:'Disbursement approved successfully!'});}</script>
@endpush
@endsection
