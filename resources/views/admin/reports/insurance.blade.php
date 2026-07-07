@extends('layouts.admin')

@section('title', 'Insurance Report - ' . config('app.name', 'CAF'))
@section('page_title', 'Insurance Production Report')

@section('content')
@php
    $fmt = fn($n) => $n >= 1000000000 ? number_format($n/1000000000, 2).'B' : ($n >= 1000000 ? number_format($n/1000000, 2).'M' : ($n >= 1000 ? number_format($n/1000, 1).'K' : number_format($n)));
@endphp

<div class="mb-6">
    <form method="GET" class="bg-white rounded-xl border p-4 flex items-end gap-3 flex-wrap">
        <div><label class="block text-xs font-medium text-gray-700 mb-1">From</label><input type="date" name="from" value="{{ request('from', now()->startOfMonth()->format('Y-m-d')) }}" class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
        <div><label class="block text-xs font-medium text-gray-700 mb-1">To</label><input type="date" name="to" value="{{ request('to', now()->format('Y-m-d')) }}" class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg">Filter</button>
    </form>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    <div class="bg-gradient-to-br from-sky-500 to-sky-700 rounded-xl border border-sky-400 p-4 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
        <div class="relative z-10">
            <span class="text-[10px] font-medium text-sky-100">Total Policies</span>
            <p class="text-xl font-bold text-white mt-2">{{ number_format($stats['total_policies']) }}</p>
            <p class="text-[10px] text-sky-200 mt-1">{{ $stats['active_policies'] }} active</p>
        </div>
    </div>
    <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-xl border border-emerald-400 p-4 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
        <div class="relative z-10">
            <span class="text-[10px] font-medium text-emerald-100">Total Premium</span>
            <p class="text-xl font-bold text-white mt-2">TZS {{ $fmt($stats['total_premium']) }}</p>
            <p class="text-[10px] text-emerald-200 mt-1">Written</p>
        </div>
    </div>
    <div class="bg-gradient-to-br from-gold-400 to-gold-600 rounded-xl border border-gold-300 p-4 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
        <div class="relative z-10">
            <span class="text-[10px] font-medium text-gold-50">Commission Earned</span>
            <p class="text-xl font-bold text-white mt-2">TZS {{ $fmt($stats['total_commission']) }}</p>
            <p class="text-[10px] text-gold-100 mt-1">From insurers</p>
        </div>
    </div>
    <div class="bg-gradient-to-br from-violet-500 to-violet-700 rounded-xl border border-violet-400 p-4 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
        <div class="relative z-10">
            <span class="text-[10px] font-medium text-violet-100">Claims</span>
            <p class="text-xl font-bold text-white mt-2">{{ number_format($stats['total_claims']) }}</p>
            <p class="text-[10px] text-violet-200 mt-1">{{ $stats['open_claims'] }} open · TZS {{ $fmt($stats['claims_settled']) }} settled</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl border overflow-hidden">
    <div class="px-5 py-4 border-b">
        <h3 class="text-sm font-semibold text-gray-900">Policies in Period</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs text-gray-500 bg-gray-50/50 border-b">
                    <th class="px-5 py-2.5 font-medium">Policy No</th>
                    <th class="px-5 py-2.5 font-medium">Client</th>
                    <th class="px-5 py-2.5 font-medium">Type</th>
                    <th class="px-5 py-2.5 font-medium">Insurer</th>
                    <th class="px-5 py-2.5 font-medium">Premium</th>
                    <th class="px-5 py-2.5 font-medium">Commission</th>
                    <th class="px-5 py-2.5 font-medium">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($policies as $pol)
                <tr class="border-t border-gray-100 hover:bg-gray-50/50">
                    <td class="px-5 py-2.5"><a href="{{ route('admin.insurance.policies.show', $pol) }}" class="font-mono text-xs text-emerald-600 hover:underline">{{ $pol->policy_no }}</a></td>
                    <td class="px-5 py-2.5 text-xs text-gray-700">{{ $pol->client?->name ?? '—' }}</td>
                    <td class="px-5 py-2.5 text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $pol->type)) }}</td>
                    <td class="px-5 py-2.5 text-xs text-gray-500">{{ $pol->insurer?->name ?? '—' }}</td>
                    <td class="px-5 py-2.5 text-xs font-medium text-gray-900">{{ $pol->premium_amount ? number_format($pol->premium_amount) : '—' }}</td>
                    <td class="px-5 py-2.5 text-xs font-medium text-gold-600">{{ $pol->commission_amount ? number_format($pol->commission_amount) : '—' }}</td>
                    <td class="px-5 py-2.5"><span class="text-[10px] px-2 py-0.5 rounded-full @if($pol->status === 'active') bg-emerald-50 text-emerald-700 @elseif($pol->status === 'expired') bg-red-50 text-red-700 @else bg-amber-50 text-amber-700 @endif capitalize">{{ $pol->status }}</span></td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-5 py-12 text-center text-sm text-gray-400">No policies in this period.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($policies->hasPages())<div class="px-5 py-3 border-t border-gray-100">{{ $policies->links() }}</div>@endif
</div>
@endsection
