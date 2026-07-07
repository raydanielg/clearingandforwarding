@extends('layouts.admin')
@section('title', 'Users - ' . config('app.name'))
@section('page_title', 'User Management')

@section('content')
<div class="flex items-center justify-between mb-4">
    <div class="relative">
        <input type="text" id="userSearch" placeholder="Search name / email..." class="pl-9 pr-4 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none w-56">
        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
    </div>
    @can('users.create')
    <button onclick="openUserModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>Add User</button>
    @endcan
</div>
<div class="bg-white rounded-xl border overflow-hidden"><div id="userTable">@include('admin.settings.users.partials.table')</div></div>

<div id="userModal" class="fixed inset-0 bg-black/40 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6 max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-bold text-gray-900 mb-4" id="userModalTitle">Add User</h3>
        <form id="userForm" class="space-y-3">
            @csrf <input type="hidden" name="id" id="userId">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label><input type="text" name="name" id="userName" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none" required></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label><input type="email" name="email" id="userEmail" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none" required></div>
            <div id="passwordField"><label class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-red-500">*</span></label><input type="password" name="password" id="userPassword" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Phone</label><input type="text" name="phone" id="userPhone" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Role <span class="text-red-500">*</span></label><select name="role" id="userRole" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none" required><option value="">Select role...</option>@foreach($roles as $r)<option value="{{ $r->name }}">{{ ucfirst($r->name) }}</option>@endforeach</select></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Active</label><select name="is_active" id="userActive" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="1">Yes</option><option value="0">No</option></select></div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeUserModal()" class="px-4 py-2 text-sm font-medium text-gray-600">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg">Save</button>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
let ut;$('#userSearch').on('input',function(){clearTimeout(ut);ut=setTimeout(function(){const p=new URLSearchParams();if($('#userSearch').val())p.set('search',$('#userSearch').val());$.ajax({url:'{{route('admin.settings.users.index')}}?'+p.toString(),type:'GET',dataType:'json',success:function(r){$('#userTable').html(r.html);}});},300);});
function openUserModal(d){$('#userModal').removeClass('hidden').addClass('flex');if(d){$('#userModalTitle').text('Edit User');$('#userId').val(d.id);$('#userName').val(d.name);$('#userEmail').val(d.email);$('#userPhone').val(d.phone);$('#userRole').val(d.role);$('#userActive').val(d.is_active?1:0);$('#passwordField').hide();$('#userPassword').removeAttr('required');}else{$('#userModalTitle').text('Add User');$('#userForm')[0].reset();$('#userId').val('');$('#passwordField').show();$('#userPassword').attr('required','required');}}
function closeUserModal(){$('#userModal').addClass('hidden').removeClass('flex');}
function editUser(id,name,email,phone,role,active){openUserModal({id,name,email,phone,role,is_active:active});}
function deleteUser(id){if(!confirm('Delete this user?'))return;$.ajax({url:'{{route('admin.settings.users.destroy','__id__')}}'.replace('__id__',id),type:'POST',data:{_method:'DELETE',_token:'{{csrf_token()}}'},success:function(r){showToast('success','Deleted',r.message);setTimeout(()=>location.reload(),800);},error:function(){showToast('error','Error','Could not delete');}});}
$('#userForm').on('submit',function(e){e.preventDefault();const id=$('#userId').val();const url=id?'{{route('admin.settings.users.update','__id__')}}'.replace('__id__',id):'{{route('admin.settings.users.store')}}';let data=$(this).serialize();if(id)data+='&_method=PUT';$.ajax({url,type:'POST',data,success:function(r){if(r.success){showToast('success','Success',r.message);closeUserModal();setTimeout(()=>location.reload(),800);}},error:function(x){if(x.responseJSON?.errors){Object.values(x.responseJSON.errors).forEach(e=>showToast('error','Validation',e[0]));}}});});
</script>
@endpush
@endsection
