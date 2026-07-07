@extends('layouts.admin')

@section('title', 'New Claim - ' . config('app.name', 'CAF'))
@section('page_title', 'Create Insurance Claim')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.insurance.claims.index') }}" class="text-sm text-gray-500 hover:text-emerald-600 flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Claims
    </a>
</div>

<div class="bg-white rounded-xl border max-w-2xl mx-auto">
    <div class="px-6 py-4 border-b">
        <h3 class="text-sm font-bold text-gray-900">New Insurance Claim</h3>
        <p class="text-xs text-gray-500">File a claim against an existing insurance policy</p>
    </div>
    <form action="{{ route('admin.insurance.claims.store') }}" method="POST" class="p-6 space-y-5">
        @csrf
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Policy *</label>
            <select name="insurance_policy_id" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none">
                <option value="">Select policy...</option>
                @foreach($policies as $pol)
                <option value="{{ $pol->id }}" @selected(request('policy') == $pol->id)>{{ $pol->policy_no }} — {{ $pol->client?->name }} ({{ ucfirst(str_replace('_', ' ', $pol->type)) }})</option>
                @endforeach
            </select>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-xs font-medium text-gray-700 mb-1">Incident Date</label><input type="date" name="incident_date" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-700 mb-1">Loss Location</label><input type="text" name="loss_location" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
        </div>
        <div><label class="block text-xs font-medium text-gray-700 mb-1">Incident Description</label><textarea name="incident_description" rows="3" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></textarea></div>
        <div><label class="block text-xs font-medium text-gray-700 mb-1">Claimed Amount *</label><input type="number" step="0.01" name="claimed_amount" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
        <div><label class="block text-xs font-medium text-gray-700 mb-1">Notes</label><textarea name="notes" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></textarea></div>
        <div class="flex justify-end gap-3 pt-4 border-t">
            <a href="{{ route('admin.insurance.claims.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg">Create Claim</button>
        </div>
    </form>
</div>
@endsection
