@extends('layouts.portal')
@section('title', 'No Client Linked - ' . config('app.name'))
@section('page_title', 'Account Setup Required')

@section('content')
<div class="max-w-md mx-auto mt-20">
    <div class="bg-white rounded-xl border p-8 text-center">
        <div class="w-16 h-16 mx-auto bg-amber-50 rounded-full flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h2 class="text-lg font-bold text-gray-900">Account Not Linked</h2>
        <p class="text-sm text-gray-500 mt-2">Your user account is not linked to a client profile. Please contact the administrator to link your account to a client record.</p>
    </div>
</div>
@endsection
