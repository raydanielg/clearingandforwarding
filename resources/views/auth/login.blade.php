@extends('layouts.guest')

@section('title', 'Login - ' . config('app.name', 'Clearing'))

@section('content')
<div class="w-full max-w-md" style="animation: simpleFadeIn 0.4s ease-out both;">
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        {{-- Header --}}
        <div class="bg-gradient-to-br from-emerald-600 to-emerald-700 px-8 py-8 text-center">
            <div class="w-16 h-16 mx-auto bg-white/10 backdrop-blur-sm rounded-2xl flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
            </div>
            <h2 class="text-2xl font-extrabold text-white">Welcome Back</h2>
            <p class="text-emerald-100 text-sm mt-1">Sign in to your {{ config('app.name', 'Clearing') }} account</p>
        </div>

        {{-- Form --}}
        <div class="p-8">
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
                        </div>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                            class="w-full pl-11 pr-4 py-2.5 rounded-lg border @error('email') border-red-300 ring-2 ring-red-100 @else border-gray-200 @enderror focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all text-sm"
                            placeholder="you@example.com">
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1"><svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                            class="w-full pl-11 pr-4 py-2.5 rounded-lg border @error('password') border-red-300 ring-2 ring-red-100 @else border-gray-200 @enderror focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all text-sm"
                            placeholder="Enter your password">
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1"><svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember + Forgot --}}
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                        <span class="text-sm text-gray-600">Remember me</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-700 transition-colors">Forgot password?</a>
                    @endif
                </div>

                {{-- Submit --}}
                <button type="submit" class="w-full py-3 text-sm font-bold text-gray-900 bg-gradient-to-r from-gold-300 to-gold-400 hover:from-gold-400 hover:to-gold-500 rounded-lg shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    Sign In
                </button>
            </form>

            {{-- Divider --}}
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
                <div class="relative flex justify-center text-sm"><span class="px-3 bg-white text-gray-400">or</span></div>
            </div>

            {{-- Register link --}}
            @if (Route::has('register'))
                <p class="text-center text-sm text-gray-500">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="font-semibold text-emerald-600 hover:text-emerald-700 transition-colors">Create account</a>
                </p>
            @endif

            {{-- Demo Quick Login --}}
            <div class="mt-6 pt-6 border-t border-gray-100">
                <div class="flex items-center gap-2 mb-3">
                    <svg class="w-4 h-4 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Quick Demo Login</p>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" onclick="quickLogin('admin@caf.co.tz')" class="quick-login-btn group flex items-center gap-2 px-3 py-2.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 border border-emerald-100 transition-all text-left">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.062-.18-2.087-.514-3.056z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-gray-900 truncate">Super Admin</p>
                            <p class="text-[10px] text-gray-400 truncate">Full access</p>
                        </div>
                    </button>

                    <button type="button" onclick="quickLogin('ops@caf.co.tz')" class="quick-login-btn group flex items-center gap-2 px-3 py-2.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 border border-emerald-100 transition-all text-left">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-600 to-emerald-800 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-gray-900 truncate">Ops Manager</p>
                            <p class="text-[10px] text-gray-400 truncate">Operations</p>
                        </div>
                    </button>

                    <button type="button" onclick="quickLogin('declaration@caf.co.tz')" class="quick-login-btn group flex items-center gap-2 px-3 py-2.5 rounded-lg bg-gold-50 hover:bg-gold-100 border border-gold-100 transition-all text-left">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-gold-400 to-gold-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-gray-900 truncate">Declaration</p>
                            <p class="text-[10px] text-gray-400 truncate">TRA Customs</p>
                        </div>
                    </button>

                    <button type="button" onclick="quickLogin('transport@caf.co.tz')" class="quick-login-btn group flex items-center gap-2 px-3 py-2.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 border border-emerald-100 transition-all text-left">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 17h6m-6 0V5a1 1 0 011-1h8a1 1 0 011 1v12h-2m-6 0V7H5"/></svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-gray-900 truncate">Transport</p>
                            <p class="text-[10px] text-gray-400 truncate">Fleet & Trips</p>
                        </div>
                    </button>

                    <button type="button" onclick="quickLogin('accounts@caf.co.tz')" class="quick-login-btn group flex items-center gap-2 px-3 py-2.5 rounded-lg bg-gold-50 hover:bg-gold-100 border border-gold-100 transition-all text-left">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-gold-400 to-gold-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-gray-900 truncate">Accountant</p>
                            <p class="text-[10px] text-gray-400 truncate">Finance</p>
                        </div>
                    </button>

                    <button type="button" onclick="quickLogin('insurance@caf.co.tz')" class="quick-login-btn group flex items-center gap-2 px-3 py-2.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 border border-emerald-100 transition-all text-left">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-sky-500 to-sky-700 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.062-.18-2.087-.514-3.056z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-gray-900 truncate">Insurance Officer</p>
                            <p class="text-[10px] text-gray-400 truncate">Policies & Claims</p>
                        </div>
                    </button>

                    <button type="button" onclick="quickLogin('client@demo.co.tz')" class="quick-login-btn group flex items-center gap-2 px-3 py-2.5 rounded-lg bg-gradient-to-br from-emerald-700 to-emerald-900 hover:from-emerald-800 hover:to-emerald-950 border border-emerald-700 transition-all text-left">
                        <div class="w-8 h-8 rounded-lg bg-white/15 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-gold-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-white truncate">Client Portal</p>
                            <p class="text-[10px] text-emerald-200/60 truncate">Track shipments</p>
                        </div>
                    </button>
                </div>
                <p class="mt-3 text-center text-[10px] text-gray-300">All demo passwords: <code class="px-1.5 py-0.5 rounded bg-gray-100 text-gray-500 font-mono text-[10px]">password</code></p>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <p class="mt-6 text-center text-xs text-gray-400">&copy; {{ date('Y') }} {{ config('app.name', 'Clearing') }}. All rights reserved.</p>
</div>

<script>
function quickLogin(email) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = 'password';
    document.querySelector('form[action="{{ route('login') }}"]').submit();
}
</script>
@endsection
