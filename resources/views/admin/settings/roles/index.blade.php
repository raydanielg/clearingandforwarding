@extends('layouts.admin')
@section('title', 'Roles & Permissions - ' . config('app.name'))
@section('page_title', 'Roles & Permissions')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl border p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Roles</h3>
            <div class="space-y-2">
                @foreach($roles as $role)
                <button onclick="selectRole({{ $role->id }})" class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request('role') == $role->id ? 'bg-emerald-50 text-emerald-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    {{ ucfirst($role->name) }}
                    <span class="text-[10px] text-gray-400 ml-2">{{ $role->permissions->count() }} perms</span>
                </button>
                @endforeach
            </div>
        </div>
    </div>
    <div class="lg:col-span-2">
        @if($selectedRole)
        <div class="bg-white rounded-xl border p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-900">Permissions for: {{ ucfirst($selectedRole->name) }}</h3>
                <button onclick="savePermissions()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg">Save Changes</button>
            </div>
            <div class="grid grid-cols-2 gap-3" id="permGrid">
                @php $grouped = $permissions->groupBy(function($p) { return explode('.', $p->name)[0]; }); @endphp
                @foreach($grouped as $group => $perms)
                <div class="border border-gray-100 rounded-lg p-3">
                    <p class="text-xs font-bold text-gray-900 mb-2 capitalize">{{ str_replace('_', ' ', $group) }}</p>
                    <div class="space-y-1.5">
                        @foreach($perms as $perm)
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="permissions[]" value="{{ $perm->name }}" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" {{ $selectedRole->hasPermissionTo($perm->name) ? 'checked' : '' }}>
                            <span class="text-xs text-gray-700">{{ ucfirst(explode('.', $perm->name)[1] ?? $perm->name) }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="bg-white rounded-xl border p-12 text-center">
            <p class="text-sm text-gray-400">Select a role to view and manage its permissions.</p>
        </div>
        @endif
    </div>
</div>
@push('scripts')
<script>
function selectRole(id) { window.location.href = '{{ route('admin.settings.roles.index') }}?role=' + id; }
function savePermissions() {
    const perms = $('input[name="permissions[]"]:checked').map(function() { return $(this).val(); }).get();
    $.ajax({ url: '{{ route('admin.settings.roles.update', $selectedRole?->id ?? 0) }}', type: 'POST',
        data: { _method: 'PUT', _token: '{{ csrf_token() }}', permissions: perms },
        success: function(r) { if (r.success) showToast('success', 'Saved', r.message); },
        error: function() { showToast('error', 'Error', 'Could not save permissions'); }
    });
}
</script>
@endpush
@endsection
