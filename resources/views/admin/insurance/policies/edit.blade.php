@extends('layouts.admin')

@section('title', 'Edit Policy - ' . config('app.name', 'CAF'))
@section('page_title', 'Edit Insurance Policy')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.insurance.policies.show', $policy) }}" class="text-sm text-gray-500 hover:text-emerald-600 flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Policy
    </a>
</div>

<div class="bg-white rounded-xl border max-w-3xl mx-auto">
    <div class="px-6 py-4 border-b">
        <h3 class="text-sm font-bold text-gray-900">Edit Policy: {{ $policy->policy_no }}</h3>
    </div>
    <form action="{{ route('admin.insurance.policies.update', $policy) }}" method="POST" class="p-6 space-y-5">
        @csrf @method('PUT')
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Client *</label>
                <select name="client_id" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none">
                    <option value="">Select client...</option>
                    @foreach($clients as $c)
                    <option value="{{ $c->id }}" @selected($policy->client_id === $c->id)>{{ $c->name }} ({{ $c->code }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Insurer</label>
                <select name="insurer_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none">
                    <option value="">Select insurer...</option>
                    @foreach($insurers as $ins)
                    <option value="{{ $ins->id }}" @selected($policy->insurer_id === $ins->id)>{{ $ins->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Insurance Type *</label>
                <select name="type" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none">
                    @php $types = ['marine_cargo'=>'Marine Cargo','goods_in_transit'=>'Goods in Transit (GIT)','warehouse'=>'Warehouse','other'=>'Other']; @endphp
                    @foreach($types as $val => $label)
                    <option value="{{ $val }}" @selected($policy->type === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none">
                    @php $statuses = ['draft'=>'Draft','quoted'=>'Quoted','issued'=>'Issued','active'=>'Active','expired'=>'Expired','cancelled'=>'Cancelled','claimed'=>'Claimed']; @endphp
                    @foreach($statuses as $val => $label)
                    <option value="{{ $val }}" @selected($policy->status === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Linked Shipment File</label>
                <select name="shipment_file_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none">
                    <option value="">None (standalone)</option>
                    @foreach($files as $f)
                    <option value="{{ $f->id }}" @selected($policy->shipment_file_id === $f->id)>{{ $f->file_no }} — {{ $f->client?->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="border-t pt-4">
            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Cover Details</h4>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Cover Start Date</label><input type="date" name="cover_start_date" value="{{ $policy->cover_start_date?->format('Y-m-d') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Cover End Date</label><input type="date" name="cover_end_date" value="{{ $policy->cover_end_date?->format('Y-m-d') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Origin</label><input type="text" name="origin" value="{{ $policy->origin }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Destination</label><input type="text" name="destination" value="{{ $policy->destination }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            </div>
            <div class="mt-4"><label class="block text-xs font-medium text-gray-700 mb-1">Cargo Description</label><textarea name="cargo_description" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none">{{ $policy->cargo_description }}</textarea></div>
        </div>

        <div class="border-t pt-4">
            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Financial</h4>
            <div class="grid grid-cols-3 gap-4">
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Cargo Value</label><input type="number" step="0.01" name="cargo_value" value="{{ $policy->cargo_value }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Currency</label><input type="text" name="cargo_currency" value="{{ $policy->cargo_currency ?? 'TZS' }}" maxlength="3" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Sum Insured</label><input type="number" step="0.01" name="sum_insured" value="{{ $policy->sum_insured }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Premium Amount</label><input type="number" step="0.01" name="premium_amount" value="{{ $policy->premium_amount }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Premium Currency</label><input type="text" name="premium_currency" value="{{ $policy->premium_currency ?? 'TZS' }}" maxlength="3" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Commission Rate (%)</label><input type="number" step="0.01" name="commission_rate" value="{{ $policy->commission_rate }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            </div>
        </div>

        <div class="border-t pt-4">
            <div class="mb-4"><label class="block text-xs font-medium text-gray-700 mb-1">Terms & Conditions</label><textarea name="terms_conditions" rows="3" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none">{{ $policy->terms_conditions }}</textarea></div>
            <div><label class="block text-xs font-medium text-gray-700 mb-1">Notes</label><textarea name="notes" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none">{{ $policy->notes }}</textarea></div>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t">
            <a href="{{ route('admin.insurance.policies.show', $policy) }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg">Update Policy</button>
        </div>
    </form>
</div>
@endsection
