@extends('layouts.admin')

@section('title', $client->name . ' - ' . config('app.name'))
@section('page_title', $client->name)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    {{-- Client Info --}}
    <div class="bg-white rounded-xl border p-6">
        <div class="flex items-start justify-between mb-4">
            <div>
                <h2 class="text-lg font-bold text-gray-900">{{ $client->name }}</h2>
                <p class="text-xs text-gray-400 font-mono">{{ $client->code }}</p>
            </div>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $client->type === 'company' ? 'bg-sky-50 text-sky-700' : 'bg-violet-50 text-violet-700' }}">{{ ucfirst($client->type) }}</span>
        </div>
        <dl class="space-y-2 text-sm">
            <div class="flex justify-between"><dt class="text-gray-500">TIN</dt><dd class="text-gray-900 font-medium">{{ $client->tin ?? '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">VRN</dt><dd class="text-gray-900 font-medium">{{ $client->vrn ?? '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Phone</dt><dd class="text-gray-900 font-medium">{{ $client->phone ?? '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Email</dt><dd class="text-gray-900 font-medium">{{ $client->email ?? '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Credit Limit</dt><dd class="text-gray-900 font-medium">TZS {{ number_format($client->credit_limit) }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Payment Terms</dt><dd class="text-gray-900 font-medium">{{ $client->payment_terms_days }} days</dd></div>
        </dl>
        <div class="mt-4 pt-4 border-t flex gap-2">
            @can('clients.edit')
            <a href="{{ route('admin.clients.edit', $client) }}" class="px-3 py-1.5 text-xs font-medium text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors">Edit</a>
            @endcan
            @can('clients.delete')
            <button onclick="deleteClient({{ $client->id }})" class="px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 rounded-lg transition-colors">Delete</button>
            @endcan
        </div>
    </div>

    {{-- Stats --}}
    <div class="lg:col-span-2 grid grid-cols-3 gap-3">
        <div class="bg-white rounded-xl border p-4">
            <p class="text-[10px] font-medium text-gray-500">Total Files</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $client->shipmentFiles->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border p-4">
            <p class="text-[10px] font-medium text-gray-500">Total Invoices</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $client->invoices->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border p-4">
            <p class="text-[10px] font-medium text-gray-500">Outstanding</p>
            <p class="text-2xl font-bold text-red-600 mt-1">TZS {{ number_format($client->invoices->whereNotIn('status', ['paid', 'cancelled', 'draft'])->sum(fn($i) => $i->total - $i->amount_paid)) }}</p>
        </div>
    </div>
</div>

{{-- Recent Files --}}
<div class="bg-white rounded-xl border overflow-hidden mb-6">
    <div class="px-5 py-4 border-b flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-900">Recent Files</h3>
        <a href="{{ route('admin.files.index') }}?search={{ $client->name }}" class="text-xs font-medium text-emerald-600 hover:text-emerald-700">View All</a>
    </div>
    <table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50 border-b">
            <th class="px-5 py-2.5 font-medium">File No</th><th class="px-5 py-2.5 font-medium">Type</th><th class="px-5 py-2.5 font-medium">Status</th><th class="px-5 py-2.5 font-medium">Opened</th>
        </tr></thead>
        <tbody>
            @forelse($client->shipmentFiles as $file)
            <tr class="border-b border-gray-100 hover:bg-gray-50/50">
                <td class="px-5 py-2.5"><a href="{{ route('admin.files.show', $file) }}" class="font-mono text-xs text-emerald-600 hover:underline">{{ $file->file_no }}</a></td>
                <td class="px-5 py-2.5"><span class="text-xs px-2 py-0.5 rounded-full bg-gray-50 text-gray-600">{{ ucfirst($file->type) }}</span></td>
                <td class="px-5 py-2.5"><span class="text-xs px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700">{{ ucfirst(str_replace('_', ' ', $file->status)) }}</span></td>
                <td class="px-5 py-2.5 text-xs text-gray-400">{{ $file->opened_at?->format('d M Y') ?? '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-5 py-8 text-center text-sm text-gray-400">No files yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Recent Invoices --}}
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="px-5 py-4 border-b flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-900">Recent Invoices</h3>
    </div>
    <table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50 border-b">
            <th class="px-5 py-2.5 font-medium">Invoice No</th><th class="px-5 py-2.5 font-medium">Date</th><th class="px-5 py-2.5 font-medium">Total</th><th class="px-5 py-2.5 font-medium">Paid</th><th class="px-5 py-2.5 font-medium">Status</th>
        </tr></thead>
        <tbody>
            @forelse($client->invoices as $inv)
            <tr class="border-b border-gray-100 hover:bg-gray-50/50">
                <td class="px-5 py-2.5"><a href="{{ route('admin.invoices.show', $inv) }}" class="font-mono text-xs text-emerald-600 hover:underline">{{ $inv->invoice_no }}</a></td>
                <td class="px-5 py-2.5 text-xs text-gray-500">{{ $inv->date?->format('d M Y') }}</td>
                <td class="px-5 py-2.5 text-xs font-medium">{{ number_format($inv->total) }}</td>
                <td class="px-5 py-2.5 text-xs text-gray-500">{{ number_format($inv->amount_paid) }}</td>
                <td class="px-5 py-2.5"><span class="text-xs px-2 py-0.5 rounded-full {{ $inv->status === 'paid' ? 'bg-emerald-50 text-emerald-700' : ($inv->status === 'overdue' ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700') }}">{{ ucfirst($inv->status) }}</span></td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-5 py-8 text-center text-sm text-gray-400">No invoices yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@push('scripts')
<script>
function deleteClient(id) {
    confirmDelete('{{ route('admin.clients.destroy', '__id__') }}'.replace('__id__', id), {
        title: 'Delete Client?',
        text: 'This client and all associated data will be permanently removed.',
        redirect: '{{ route('admin.clients.index') }}'
    });
}
</script>
@endpush
@endsection
