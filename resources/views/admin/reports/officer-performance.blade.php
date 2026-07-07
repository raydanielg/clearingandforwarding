@extends('layouts.admin')
@section('title', 'Officer Performance - ' . config('app.name'))
@section('page_title', 'Officer Performance Report')

@section('content')
<div class="bg-white rounded-xl border p-6 mb-6">
    <form id="filterForm" class="flex items-end gap-4">
        <div><label class="block text-xs font-medium text-gray-700 mb-1">From Date</label><input type="date" name="from" value="{{ request('from', date('Y-m-01')) }}" class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
        <div><label class="block text-xs font-medium text-gray-700 mb-1">To Date</label><input type="date" name="to" value="{{ request('to', date('Y-m-d')) }}" class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg">Filter</button>
    </form>
</div>

<div class="bg-white rounded-xl border overflow-hidden">
    <div class="px-5 py-4 border-b"><h3 class="text-sm font-semibold text-gray-900">Officer Performance</h3></div>
    <table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50 border-b">
            <th class="px-5 py-3 font-medium">Officer</th><th class="px-5 py-3 font-medium text-right">Files Assigned</th><th class="px-5 py-3 font-medium text-right">Declarations</th><th class="px-5 py-3 font-medium text-right">Files Closed</th><th class="px-5 py-3 font-medium text-right">Revenue Generated</th>
        </tr></thead>
        <tbody>
            @forelse($officers as $o)
            <tr class="border-b border-gray-100 hover:bg-gray-50/50">
                <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $o['name'] }}</td>
                <td class="px-5 py-3 text-xs text-right">{{ $o['files_assigned'] }}</td>
                <td class="px-5 py-3 text-xs text-right">{{ $o['declarations'] }}</td>
                <td class="px-5 py-3 text-xs text-right">{{ $o['files_closed'] }}</td>
                <td class="px-5 py-3 text-xs text-right font-medium text-emerald-600">{{ number_format($o['revenue']) }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-5 py-12 text-center text-sm text-gray-400">No data for this period.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@push('scripts')
<script>$('#filterForm').on('submit',function(e){e.preventDefault();const p=new URLSearchParams();p.set('from',$('input[name="from"]').val());p.set('to',$('input[name="to"]').val());window.location.href='{{route('admin.reports.officer-performance')}}?'+p.toString();});</script>
@endpush
@endsection
