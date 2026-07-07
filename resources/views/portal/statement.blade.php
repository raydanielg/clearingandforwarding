@extends('layouts.portal')
@section('title', 'My Statement - ' . config('app.name'))
@section('page_title', 'My Statement')

@section('content')
<div class="space-y-6">
    {{-- Summary Cards --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border p-5">
            <p class="text-xs text-gray-500 font-medium">Total Invoiced</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalInvoiced) }}</p>
        </div>
        <div class="bg-white rounded-xl border p-5">
            <p class="text-xs text-gray-500 font-medium">Total Paid</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($totalPaid) }}</p>
        </div>
        <div class="bg-white rounded-xl border p-5">
            <p class="text-xs text-gray-500 font-medium">Outstanding Balance</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ number_format($balance) }}</p>
        </div>
    </div>

    {{-- Statement Table --}}
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b"><h3 class="text-sm font-semibold text-gray-900">Account Statement - {{ $client->name }}</h3></div>
        <table class="w-full text-sm">
            <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50 border-b">
                <th class="px-5 py-3 font-medium">Date</th><th class="px-5 py-3 font-medium">Reference</th><th class="px-5 py-3 font-medium">Description</th><th class="px-5 py-3 font-medium text-right">Debit</th><th class="px-5 py-3 font-medium text-right">Credit</th>
            </tr></thead>
            <tbody>
                @forelse($transactions as $t)
                <tr class="border-b border-gray-100 hover:bg-gray-50/50">
                    <td class="px-5 py-3 text-xs text-gray-500">{{ $t['date']?->format('d M Y') }}</td>
                    <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $t['ref'] }}</td>
                    <td class="px-5 py-3 text-xs text-gray-700">{{ $t['description'] }}</td>
                    <td class="px-5 py-3 text-xs text-right {{ $t['debit'] > 0 ? 'text-gray-900 font-medium' : 'text-gray-300' }}">{{ $t['debit'] > 0 ? number_format($t['debit']) : '—' }}</td>
                    <td class="px-5 py-3 text-xs text-right {{ $t['credit'] > 0 ? 'text-emerald-600 font-medium' : 'text-gray-300' }}">{{ $t['credit'] > 0 ? number_format($t['credit']) : '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-5 py-12 text-center text-sm text-gray-400">No transactions yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
