@extends('layouts.admin')

@section('title', 'Dashboard - ' . config('app.name', 'CAF'))
@section('page_title', 'Dashboard Overview')

@section('content')
@php
    $fmt = fn($n) => $n >= 1000000000 ? number_format($n/1000000000, 2).'B' : ($n >= 1000000 ? number_format($n/1000000, 2).'M' : ($n >= 1000 ? number_format($n/1000, 1).'K' : number_format($n)));
@endphp

{{-- Stats Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    @foreach([
        ['label'=>'Open Files','value'=>number_format($stats['openFiles']),'change'=>'+'.$stats['filesThisWeek'].' this week','icon'=>'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z','from'=>'emerald-600','to'=>'emerald-700','border'=>'emerald-500','text'=>'emerald-100','sub'=>'emerald-200'],
        ['label'=>'Demurrage Risk','value'=>number_format($stats['demurrageRisk']),'change'=>'Files past free days','icon'=>'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z','from'=>'amber-400','to'=>'amber-500','border'=>'amber-300','text'=>'amber-50','sub'=>'amber-100'],
        ['label'=>'Unpaid Invoices','value'=>number_format($stats['unpaidInvoices']),'change'=>'TZS '.$fmt($stats['revenueThisMonth']).' this month','icon'=>'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21l-7-4-7 4V5a2 2 0 012-2h10a2 2 0 012 2v16z','from'=>'sky-500','to'=>'sky-600','border'=>'sky-400','text'=>'sky-100','sub'=>'sky-200'],
        ['label'=>"Today's Deliveries",'value'=>number_format($stats['deliveriesToday']),'change'=>'Scheduled for today','icon'=>'M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z','from'=>'violet-500','to'=>'violet-600','border'=>'violet-400','text'=>'violet-100','sub'=>'violet-200']
    ] as $card)
    <div class="bg-gradient-to-br from-{{ $card['from'] }} to-{{ $card['to'] }} rounded-xl border border-{{ $card['border'] }} p-4 text-white relative overflow-hidden hover:shadow-lg transition-shadow">
        <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
        <div class="relative z-10">
            <div class="flex items-start justify-between mb-2">
                <span class="text-[10px] font-medium {{ $card['text'] }}">{{ $card['label'] }}</span>
                <svg class="w-4 h-4 {{ $card['sub'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/></svg>
            </div>
            <p class="text-xl font-bold tracking-tight text-white">{{ $card['value'] }}</p>
            <p class="text-[10px] {{ $card['sub'] }} font-medium mt-1">{{ $card['change'] }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- Insurance Stats (Line 2) --}}
@can('insurance.view')
<div class="grid grid-cols-2 lg:grid-cols-3 gap-3 mb-6">
    <div class="bg-gradient-to-br from-sky-500 to-sky-700 rounded-xl border border-sky-400 p-4 text-white relative overflow-hidden hover:shadow-lg transition-shadow">
        <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
        <div class="relative z-10">
            <div class="flex items-start justify-between mb-2">
                <span class="text-[10px] font-medium text-sky-100">Active Policies</span>
                <svg class="w-4 h-4 text-sky-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.062-.18-2.087-.514-3.056z"/></svg>
            </div>
            <p class="text-xl font-bold tracking-tight text-white">{{ number_format($stats['activePolicies']) }}</p>
            <p class="text-[10px] text-sky-200 font-medium mt-1">Currently in force</p>
        </div>
    </div>
    <div class="bg-gradient-to-br from-amber-400 to-amber-600 rounded-xl border border-amber-300 p-4 text-white relative overflow-hidden hover:shadow-lg transition-shadow">
        <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
        <div class="relative z-10">
            <div class="flex items-start justify-between mb-2">
                <span class="text-[10px] font-medium text-amber-50">Open Claims</span>
                <svg class="w-4 h-4 text-amber-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <p class="text-xl font-bold tracking-tight text-white">{{ number_format($stats['openClaims']) }}</p>
            <p class="text-[10px] text-amber-100 font-medium mt-1">Awaiting settlement</p>
        </div>
    </div>
    <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-xl border border-emerald-400 p-4 text-white relative overflow-hidden hover:shadow-lg transition-shadow">
        <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
        <div class="relative z-10">
            <div class="flex items-start justify-between mb-2">
                <span class="text-[10px] font-medium text-emerald-100">Premium This Month</span>
                <svg class="w-4 h-4 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-xl font-bold tracking-tight text-white">TZS {{ $fmt($stats['premiumThisMonth']) }}</p>
            <p class="text-[10px] text-emerald-200 font-medium mt-1">Collected this month</p>
        </div>
    </div>
</div>
@endcan

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    {{-- Files by Status --}}
    <div class="lg:col-span-2 bg-white rounded-xl border p-5">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Files by Status</h3>
                <p class="text-xs text-gray-400">Current distribution across all shipments</p>
            </div>
        </div>
        <div class="space-y-3">
            @php $maxStatus = max($statusBreakdown) ?: 1; @endphp
            @foreach($statusBreakdown as $status => $count)
            <div class="flex items-center gap-3">
                <div class="w-28 text-xs font-medium text-gray-600 shrink-0">{{ $status }}</div>
                <div class="flex-1 bg-gray-100 rounded-full h-6 relative overflow-hidden">
                    <div class="absolute top-0 left-0 h-full bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-full transition-all duration-300" style="width: {{ max(($count / $maxStatus) * 100, 2) }}%"></div>
                </div>
                <div class="w-8 text-right text-xs font-bold text-gray-900">{{ $count }}</div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="bg-white rounded-xl border p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Quick Actions</h3>
        <div class="space-y-2">
            <a href="{{ route('admin.files.create') }}" class="flex items-center gap-3 p-3 rounded-lg bg-emerald-50 hover:bg-emerald-100 transition-colors">
                <div class="w-8 h-8 rounded-lg bg-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-900">Open New File</p>
                    <p class="text-[10px] text-gray-500">Create a new shipment</p>
                </div>
            </a>
            <a href="{{ route('admin.invoices.create') }}" class="flex items-center gap-3 p-3 rounded-lg bg-amber-50 hover:bg-amber-100 transition-colors">
                <div class="w-8 h-8 rounded-lg bg-amber-500 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21l-7-4-7 4V5a2 2 0 012-2h10a2 2 0 012 2v16z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-900">Create Invoice</p>
                    <p class="text-[10px] text-gray-500">Bill a client</p>
                </div>
            </a>
            <a href="{{ route('admin.transport.trips.create') }}" class="flex items-center gap-3 p-3 rounded-lg bg-sky-50 hover:bg-sky-100 transition-colors">
                <div class="w-8 h-8 rounded-lg bg-sky-500 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-900">Assign Transport</p>
                    <p class="text-[10px] text-gray-500">Dispatch a delivery</p>
                </div>
            </a>
            @can('insurance.create')
            <a href="{{ route('admin.insurance.policies.create') }}" class="flex items-center gap-3 p-3 rounded-lg bg-violet-50 hover:bg-violet-100 transition-colors">
                <div class="w-8 h-8 rounded-lg bg-violet-500 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.062-.18-2.087-.514-3.056z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-900">New Insurance Policy</p>
                    <p class="text-[10px] text-gray-500">Issue cover for a client</p>
                </div>
            </a>
            @endcan
            <a href="{{ route('admin.reports.operations') }}" class="flex items-center gap-3 p-3 rounded-lg bg-violet-50 hover:bg-violet-100 transition-colors">
                <div class="w-8 h-8 rounded-lg bg-violet-500 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-900">View Reports</p>
                    <p class="text-[10px] text-gray-500">Operations &amp; finance</p>
                </div>
            </a>
        </div>
    </div>
</div>

{{-- Recent Files --}}
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="px-5 py-4 border-b flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-900">Recent Files</h3>
        <a href="{{ route('admin.files.index') }}" class="text-xs font-medium text-emerald-600 hover:text-emerald-700">View All</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                    <th class="px-5 py-2.5 font-medium">File No</th>
                    <th class="px-5 py-2.5 font-medium">Client</th>
                    <th class="px-5 py-2.5 font-medium">Type</th>
                    <th class="px-5 py-2.5 font-medium">BL / AWB</th>
                    <th class="px-5 py-2.5 font-medium">Status</th>
                    <th class="px-5 py-2.5 font-medium">Opened</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentFiles as $file)
                <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">
                    <td class="px-5 py-2.5"><a href="{{ route('admin.files.show', $file) }}" class="font-mono text-xs text-emerald-600 hover:underline">{{ $file->file_no }}</a></td>
                    <td class="px-5 py-2.5 text-xs text-gray-700">{{ $file->client?->name ?? '—' }}</td>
                    <td class="px-5 py-2.5">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $file->type === 'import' ? 'bg-sky-50 text-sky-700 border border-sky-100' : ($file->type === 'export' ? 'bg-violet-50 text-violet-700 border border-violet-100' : 'bg-amber-50 text-amber-700 border border-amber-100') }}">{{ ucfirst($file->type) }}</span>
                    </td>
                    <td class="px-5 py-2.5 font-mono text-xs text-gray-500">{{ $file->bl_awb_no ?? '—' }}</td>
                    <td class="px-5 py-2.5">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">{{ ucfirst(str_replace('_', ' ', $file->status)) }}</span>
                    </td>
                    <td class="px-5 py-2.5 text-xs text-gray-400">{{ $file->opened_at?->format('d M Y') ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center">
                        <div class="w-14 h-14 mx-auto bg-gray-50 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                        </div>
                        <p class="text-sm text-gray-400">No files yet. Files will appear here once created.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
