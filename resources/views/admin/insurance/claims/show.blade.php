@extends('layouts.admin')

@section('title', 'Claim ' . $claim->claim_no . ' - ' . config('app.name', 'CAF'))
@section('page_title', 'Claim Details')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.insurance.claims.index') }}" class="text-sm text-gray-500 hover:text-emerald-600 flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Claims
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl border p-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">{{ $claim->claim_no }}</h2>
                    <p class="text-sm text-gray-500">Insurance Claim</p>
                </div>
                @php $stColors = ['submitted'=>'bg-sky-50 text-sky-700 border-sky-100','under_review'=>'bg-amber-50 text-amber-700 border-amber-100','approved'=>'bg-emerald-50 text-emerald-700 border-emerald-100','rejected'=>'bg-red-50 text-red-700 border-red-100','settled'=>'bg-violet-50 text-violet-700 border-violet-100']; @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium border {{ $stColors[$claim->status] ?? $stColors['submitted'] }}">{{ ucfirst(str_replace('_', ' ', $claim->status)) }}</span>
            </div>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div><p class="text-xs text-gray-400">Client</p><p class="font-medium text-gray-900">{{ $claim->client?->name ?? '—' }}</p></div>
                <div><p class="text-xs text-gray-400">Policy</p><a href="{{ route('admin.insurance.policies.show', $claim->policy) }}" class="font-medium text-emerald-600 hover:underline">{{ $claim->policy?->policy_no ?? '—' }}</a></div>
                <div><p class="text-xs text-gray-400">Incident Date</p><p class="font-medium text-gray-900">{{ $claim->incident_date?->format('d M Y') ?? '—' }}</p></div>
                <div><p class="text-xs text-gray-400">Loss Location</p><p class="font-medium text-gray-900">{{ $claim->loss_location ?? '—' }}</p></div>
                <div><p class="text-xs text-gray-400">Submitted At</p><p class="font-medium text-gray-900">{{ $claim->submitted_at?->format('d M Y, H:i') ?? '—' }}</p></div>
                <div><p class="text-xs text-gray-400">Handled By</p><p class="font-medium text-gray-900">{{ $claim->handledBy?->name ?? '—' }}</p></div>
            </div>
        </div>

        <div class="bg-white rounded-xl border p-6">
            <h3 class="text-sm font-bold text-gray-900 mb-3">Incident Description</h3>
            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $claim->incident_description ?? '—' }}</p>
        </div>

        <div class="bg-white rounded-xl border p-6">
            <h3 class="text-sm font-bold text-gray-900 mb-4">Financial</h3>
            <div class="grid grid-cols-3 gap-4 text-sm">
                <div><p class="text-xs text-gray-400">Claimed Amount</p><p class="font-bold text-gray-900">{{ $claim->claimed_amount ? number_format($claim->claimed_amount) : '—' }}</p></div>
                <div><p class="text-xs text-gray-400">Approved Amount</p><p class="font-bold text-emerald-700">{{ $claim->approved_amount ? number_format($claim->approved_amount) : '—' }}</p></div>
                <div><p class="text-xs text-gray-400">Settled Amount</p><p class="font-bold text-violet-700">{{ $claim->settled_amount ? number_format($claim->settled_amount) : '—' }}</p></div>
            </div>
        </div>

        @if($claim->insurer_response)
        <div class="bg-white rounded-xl border p-6">
            <h3 class="text-sm font-bold text-gray-900 mb-2">Insurer Response</h3>
            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $claim->insurer_response }}</p>
        </div>
        @endif
    </div>

    <div class="space-y-4">
        <div class="bg-white rounded-xl border p-5">
            <h3 class="text-sm font-bold text-gray-900 mb-3">Actions</h3>
            @can('insurance.edit')
            <form action="{{ route('admin.insurance.claims.update', $claim) }}" method="POST" class="space-y-3">
                @csrf @method('PUT')
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Update Status</label>
                    <select name="status" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none">
                        @php $statuses = ['submitted'=>'Submitted','under_review'=>'Under Review','approved'=>'Approved','rejected'=>'Rejected','settled'=>'Settled']; @endphp
                        @foreach($statuses as $val => $label)
                        <option value="{{ $val }}" @selected($claim->status === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Approved Amount</label><input type="number" step="0.01" name="approved_amount" value="{{ $claim->approved_amount }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Settled Amount</label><input type="number" step="0.01" name="settled_amount" value="{{ $claim->settled_amount }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Insurer Response</label><textarea name="insurer_response" rows="3" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none">{{ $claim->insurer_response }}</textarea></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Settled At</label><input type="date" name="settled_at" value="{{ $claim->settled_at?->format('Y-m-d') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Notes</label><textarea name="notes" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none">{{ $claim->notes }}</textarea></div>
                <button type="submit" class="w-full px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg">Update Claim</button>
            </form>
            @endcan
            @can('insurance.delete')
            <div class="pt-3 border-t mt-3">
                <button onclick="deleteClaim({{ $claim->id }})" class="w-full px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg">Delete Claim</button>
            </div>
            @endcan
        </div>
    </div>
</div>

@push('scripts')
<script>
function deleteClaim(id){confirmDelete('{{route('admin.insurance.claims.destroy','__id__')}}'.replace('__id__',id),{title:'Delete Claim?',text:'This insurance claim will be permanently removed.',redirect:'{{route('admin.insurance.claims.index')}}'});}
</script>
@endpush
@endsection
