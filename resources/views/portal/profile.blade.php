@extends('layouts.portal')
@section('title', 'Profile - ' . config('app.name'))
@section('page_title', 'Profile / Users')

@section('content')
<div class="max-w-2xl space-y-6">
    {{-- Client Info --}}
    <div class="bg-white rounded-xl border p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Company Information</h3>
        <div class="grid grid-cols-2 gap-4">
            <div><p class="text-[10px] text-gray-500 font-medium">Company Name</p><p class="text-sm text-gray-900 mt-0.5">{{ $client->name }}</p></div>
            <div><p class="text-[10px] text-gray-500 font-medium">Client Code</p><p class="text-sm text-gray-900 mt-0.5">{{ $client->code }}</p></div>
            <div><p class="text-[10px] text-gray-500 font-medium">TIN</p><p class="text-sm text-gray-900 mt-0.5">{{ $client->tin ?? '—' }}</p></div>
            <div><p class="text-[10px] text-gray-500 font-medium">VRN</p><p class="text-sm text-gray-900 mt-0.5">{{ $client->vrn ?? '—' }}</p></div>
            <div><p class="text-[10px] text-gray-500 font-medium">Email</p><p class="text-sm text-gray-900 mt-0.5">{{ $client->email ?? '—' }}</p></div>
            <div><p class="text-[10px] text-gray-500 font-medium">Phone</p><p class="text-sm text-gray-900 mt-0.5">{{ $client->phone ?? '—' }}</p></div>
            <div><p class="text-[10px] text-gray-500 font-medium">Payment Terms</p><p class="text-sm text-gray-900 mt-0.5">{{ $client->payment_terms_days }} days</p></div>
            <div><p class="text-[10px] text-gray-500 font-medium">Credit Limit</p><p class="text-sm text-gray-900 mt-0.5">{{ number_format($client->credit_limit) }}</p></div>
        </div>
        @if($client->address)
        <div class="mt-4 pt-4 border-t"><p class="text-[10px] text-gray-500 font-medium">Address</p><p class="text-sm text-gray-700 mt-0.5">{{ $client->address }}</p></div>
        @endif
    </div>

    {{-- Account Info --}}
    <div class="bg-white rounded-xl border p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Your Account</h3>
        <div class="grid grid-cols-2 gap-4">
            <div><p class="text-[10px] text-gray-500 font-medium">Name</p><p class="text-sm text-gray-900 mt-0.5">{{ auth()->user()->name }}</p></div>
            <div><p class="text-[10px] text-gray-500 font-medium">Email</p><p class="text-sm text-gray-900 mt-0.5">{{ auth()->user()->email }}</p></div>
            <div><p class="text-[10px] text-gray-500 font-medium">Phone</p><p class="text-sm text-gray-900 mt-0.5">{{ auth()->user()->phone ?? '—' }}</p></div>
            <div><p class="text-[10px] text-gray-500 font-medium">Last Login</p><p class="text-sm text-gray-900 mt-0.5">{{ auth()->user()->last_login_at?->format('d M Y, H:i') ?? '—' }}</p></div>
        </div>
    </div>
</div>
@endsection
