@extends('layouts.admin')
@section('title', 'Debtors Aging - ' . config('app.name'))
@section('page_title', 'Debtors (Aging)')

@section('content')
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="px-5 py-4 border-b"><h3 class="text-sm font-semibold text-gray-900">Outstanding Invoices by Aging</h3></div>
    <table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50 border-b">
            <th class="px-5 py-3 font-medium">Client</th><th class="px-5 py-3 font-medium text-right">Current</th><th class="px-5 py-3 font-medium text-right">1-30 days</th><th class="px-5 py-3 font-medium text-right">31-60 days</th><th class="px-5 py-3 font-medium text-right">60+ days</th><th class="px-5 py-3 font-medium text-right">Total</th>
        </tr></thead>
        <tbody>
            @php $totals = ['current' => 0, 'd30' => 0, 'd60' => 0, 'd90' => 0, 'total' => 0]; @endphp
            @forelse($clients as $row)
            <tr class="border-b border-gray-100 hover:bg-gray-50/50">
                <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $row['client'] }}</td>
                <td class="px-5 py-3 text-xs text-right">{{ number_format($row['current']) }}</td>
                <td class="px-5 py-3 text-xs text-right">{{ number_format($row['d30']) }}</td>
                <td class="px-5 py-3 text-xs text-right {{ $row['d60'] > 0 ? 'text-amber-600 font-medium' : '' }}">{{ number_format($row['d60']) }}</td>
                <td class="px-5 py-3 text-xs text-right {{ $row['d90'] > 0 ? 'text-red-600 font-medium' : '' }}">{{ number_format($row['d90']) }}</td>
                <td class="px-5 py-3 text-xs text-right font-bold">{{ number_format($row['total']) }}</td>
            </tr>
            @php $totals['current'] += $row['current']; $totals['d30'] += $row['d30']; $totals['d60'] += $row['d60']; $totals['d90'] += $row['d90']; $totals['total'] += $row['total']; @endphp
            @empty
            <tr><td colspan="6" class="px-5 py-12 text-center text-sm text-gray-400">No outstanding invoices.</td></tr>
            @endforelse
        </tbody>
        @if($clients->isNotEmpty())
        <tfoot><tr class="border-t-2 border-gray-200 bg-gray-50/50">
            <td class="px-5 py-3 text-xs font-bold text-gray-900">TOTAL</td>
            <td class="px-5 py-3 text-xs text-right font-bold">{{ number_format($totals['current']) }}</td>
            <td class="px-5 py-3 text-xs text-right font-bold">{{ number_format($totals['d30']) }}</td>
            <td class="px-5 py-3 text-xs text-right font-bold text-amber-600">{{ number_format($totals['d60']) }}</td>
            <td class="px-5 py-3 text-xs text-right font-bold text-red-600">{{ number_format($totals['d90']) }}</td>
            <td class="px-5 py-3 text-xs text-right font-bold text-emerald-700">{{ number_format($totals['total']) }}</td>
        </tr></tfoot>
        @endif
    </table>
</div>
@endsection
