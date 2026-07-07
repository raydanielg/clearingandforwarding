{{-- Navigation Header --}}
<header id="navbar" class="fixed top-0 left-0 right-0 z-50 transition-all duration-500" style="background: transparent;">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            {{-- Logo --}}
            <a href="{{ url('/') }}" class="flex items-center group">
                <img src="{{ asset('logo-white.svg') }}" alt="CAF — Clearing & Forwarding" class="h-11 w-auto group-hover:scale-105 transition-transform duration-300">
            </a>

            {{-- Desktop Nav --}}
            <div class="hidden lg:flex items-center gap-8">
                <a href="#home" class="nav-link text-sm font-semibold text-white/90 hover:text-gold-400 transition-colors">Home</a>
                <a href="#services" class="nav-link text-sm font-semibold text-white/90 hover:text-gold-400 transition-colors">Services</a>
                <a href="#about" class="nav-link text-sm font-semibold text-white/90 hover:text-gold-400 transition-colors">About</a>
                <a href="#tracking" class="nav-link text-sm font-semibold text-white/90 hover:text-gold-400 transition-colors">Track</a>
                <a href="#contact" class="nav-link text-sm font-semibold text-white/90 hover:text-gold-400 transition-colors">Contact</a>
            </div>

            {{-- CTA --}}
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ auth()->user()->hasRole('Client') ? route('portal.dashboard') : route('admin.dashboard') }}" class="hidden sm:inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-gray-900 bg-gradient-to-r from-gold-300 to-gold-400 hover:from-gold-400 hover:to-gold-500 rounded-lg shadow-lg hover:shadow-xl transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8v8M21 7l-9 9-3-3"/></svg>
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="hidden sm:inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-gray-900 bg-gradient-to-r from-gold-300 to-gold-400 hover:from-gold-400 hover:to-gold-500 rounded-lg shadow-lg hover:shadow-xl transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        Sign In
                    </a>
                @endauth
                {{-- Mobile toggle --}}
                <button id="mobileToggle" class="lg:hidden text-white p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>
    </nav>

    {{-- Mobile Menu --}}
    <div id="mobileMenu" class="lg:hidden hidden glass-dark border-t border-white/10">
        <div class="px-4 py-4 space-y-3">
            <a href="#home" class="block py-2 text-sm font-semibold text-white/90 hover:text-gold-400">Home</a>
            <a href="#services" class="block py-2 text-sm font-semibold text-white/90 hover:text-gold-400">Services</a>
            <a href="#about" class="block py-2 text-sm font-semibold text-white/90 hover:text-gold-400">About</a>
            <a href="#tracking" class="block py-2 text-sm font-semibold text-white/90 hover:text-gold-400">Track</a>
            <a href="#contact" class="block py-2 text-sm font-semibold text-white/90 hover:text-gold-400">Contact</a>
            @auth
                <a href="{{ auth()->user()->hasRole('Client') ? route('portal.dashboard') : route('admin.dashboard') }}" class="block py-2.5 text-center text-sm font-bold text-gray-900 bg-gradient-to-r from-gold-300 to-gold-400 rounded-lg">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="block py-2.5 text-center text-sm font-bold text-gray-900 bg-gradient-to-r from-gold-300 to-gold-400 rounded-lg">Sign In</a>
            @endauth
        </div>
    </div>
</header>

<script>
(function() {
    const navbar = document.getElementById('navbar');
    const mobileToggle = document.getElementById('mobileToggle');
    const mobileMenu = document.getElementById('mobileMenu');

    window.addEventListener('scroll', function() {
        if (window.scrollY > 60) {
            navbar.style.background = 'rgba(1,24,22,0.92)';
            navbar.style.backdropFilter = 'blur(12px)';
            navbar.style.boxShadow = '0 4px 20px rgba(0,0,0,0.15)';
        } else {
            navbar.style.background = 'transparent';
            navbar.style.backdropFilter = 'none';
            navbar.style.boxShadow = 'none';
        }
    });

    mobileToggle?.addEventListener('click', function() {
        mobileMenu.classList.toggle('hidden');
    });

    document.querySelectorAll('#mobileMenu a').forEach(function(link) {
        link.addEventListener('click', function() { mobileMenu.classList.add('hidden'); });
    });
})();
</script>
