@extends('layouts.portal')

@section('title', 'Policy ' . $policy->policy_no . ' - ' . config('app.name', 'CAF'))
@section('page_title', 'Policy Details')

@section('content')
<div class="mb-4">
    <a href="{{ route('portal.insurance.index') }}" class="text-sm text-gray-500 hover:text-emerald-600 flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Insurance
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl border p-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">{{ $policy->policy_no }}</h2>
                    <p class="text-sm text-gray-500">{{ ucfirst(str_replace('_', ' ', $policy->type)) }} Insurance</p>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium @if($policy->status === 'active') bg-emerald-50 text-emerald-700 border border-emerald-100 @elseif($policy->status === 'expired') bg-red-50 text-red-700 border border-red-100 @else bg-amber-50 text-amber-700 border border-amber-100 @endif">{{ ucfirst($policy->status) }}</span>
            </div>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div><p class="text-xs text-gray-400">Insurer</p><p class="font-medium text-gray-900">{{ $policy->insurer?->name ?? '—' }}</p></div>
                <div><p class="text-xs text-gray-400">Linked File</p><p class="font-medium text-gray-900">{{ $policy->shipmentFile?->file_no ?? 'Standalone' }}</p></div>
                <div><p class="text-xs text-gray-400">Cover Start</p><p class="font-medium text-gray-900">{{ $policy->cover_start_date?->format('d M Y') ?? '—' }}</p></div>
                <div><p class="text-xs text-gray-400">Cover End</p><p class="font-medium text-gray-900">{{ $policy->cover_end_date?->format('d M Y') ?? '—' }}</p></div>
                <div><p class="text-xs text-gray-400">Origin</p><p class="font-medium text-gray-900">{{ $policy->origin ?? '—' }}</p></div>
                <div><p class="text-xs text-gray-400">Destination</p><p class="font-medium text-gray-900">{{ $policy->destination ?? '—' }}</p></div>
            </div>
        </div>

        <div class="bg-white rounded-xl border p-6">
            <h3 class="text-sm font-bold text-gray-900 mb-3">Cargo Details</h3>
            <p class="text-sm text-gray-700">{{ $policy->cargo_description ?? '—' }}</p>
            <div class="grid grid-cols-3 gap-4 mt-4 text-sm">
                <div><p class="text-xs text-gray-400">Cargo Value</p><p class="font-bold text-gray-900">{{ $policy->cargo_value ? number_format($policy->cargo_value) . ' ' . ($policy->cargo_currency ?? 'TZS') : '—' }}</p></div>
                <div><p class="text-xs text-gray-400">Sum Insured</p><p class="font-bold text-gray-900">{{ $policy->sum_insured ? number_format($policy->sum_insured) : '—' }}</p></div>
                <div><p class="text-xs text-gray-400">Premium</p><p class="font-bold text-emerald-700">{{ $policy->premium_amount ? number_format($policy->premium_amount) . ' ' . ($policy->premium_currency ?? 'TZS') : '—' }}</p></div>
            </div>
        </div>

        @if($policy->claims->isNotEmpty())
        <div class="bg-white rounded-xl border overflow-hidden">
            <div class="px-5 py-4 border-b"><h3 class="text-sm font-bold text-gray-900">Claims</h3></div>
            <table class="w-full text-sm">
                <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50 border-b">
                    <th class="px-5 py-2.5 font-medium">Claim No</th><th class="px-5 py-2.5 font-medium">Date</th><th class="px-5 py-2.5 font-medium">Amount</th><th class="px-5 py-2.5 font-medium">Status</th>
                </tr></thead>
                <tbody>
                    @foreach($policy->claims as $claim)
                    <tr class="border-b border-gray-100">
                        <td class="px-5 py-2.5 font-mono text-xs text-gray-700">{{ $claim->claim_no }}</td>
                        <td class="px-5 py-2.5 text-xs text-gray-500">{{ $claim->incident_date?->format('d M Y') ?? '—' }}</td>
                        <td class="px-5 py-2.5 text-xs font-medium">{{ $claim->claimed_amount ? number_format($claim->claimed_amount) : '—' }}</td>
                        <td class="px-5 py-2.5"><span class="text-[10px] px-2 py-0.5 rounded-full bg-violet-50 text-violet-700 border border-violet-100">{{ ucfirst(str_replace('_', ' ', $claim->status)) }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    <div class="space-y-4">
        @if($policy->terms_conditions)
        <div class="bg-white rounded-xl border p-5">
            <h3 class="text-sm font-bold text-gray-900 mb-2">Terms & Conditions</h3>
            <p class="text-xs text-gray-600 whitespace-pre-wrap">{{ $policy->terms_conditions }}</p>
        </div>
        @endif
        @if($policy->notes)
        <div class="bg-white rounded-xl border p-5">
            <h3 class="text-sm font-bold text-gray-900 mb-2">Notes</h3>
            <p class="text-xs text-gray-600 whitespace-pre-wrap">{{ $policy->notes }}</p>
        </div>
        @endif
    </div>
</div>
@endsection
