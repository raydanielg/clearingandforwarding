<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="CAF Clearing & Forwarding — Tanzania's trusted logistics partner for customs clearance, port operations, and cargo delivery.">
    <title>@yield('title', config('app.name', 'CAF') . ' — Clearing & Forwarding')</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900|Poppins:400,500,600,700,800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        emerald: { 50:'#e6f5f1',100:'#b3e0d4',200:'#80cbc0',300:'#4db5a8',400:'#1a9f8e',500:'#024938',600:'#023d30',700:'#013028',800:'#01241f',900:'#001816' },
                        gold: { 50:'#fff5e0',100:'#ffe6b3',200:'#ffd680',300:'#ffc64d',400:'#ffb71a',500:'#f9ac00',600:'#d49700',700:'#b07c00',800:'#8c6100',900:'#684600' }
                    },
                    fontFamily: {
                        heading: ['Poppins', 'sans-serif'],
                        body: ['Nunito', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        /* ── Base ── */
        * { scroll-behavior: smooth; }
        body { font-family: 'Nunito', sans-serif; }
        h1,h2,h3,h4,h5 { font-family: 'Poppins', sans-serif; }

        /* ── Keyframes ── */
        @keyframes fadeInUp { from { opacity:0; transform:translateY(30px); } to { opacity:1; transform:translateY(0); } }
        @keyframes fadeIn { from { opacity:0; } to { opacity:1; } }
        @keyframes slideInLeft { from { opacity:0; transform:translateX(-50px); } to { opacity:1; transform:translateX(0); } }
        @keyframes slideInRight { from { opacity:0; transform:translateX(50px); } to { opacity:1; transform:translateX(0); } }
        @keyframes float { 0%,100% { transform:translateY(0); } 50% { transform:translateY(-20px); } }
        @keyframes floatSlow { 0%,100% { transform:translateY(0) rotate(0deg); } 50% { transform:translateY(-30px) rotate(5deg); } }
        @keyframes truckMove { 0% { transform:translateX(-120%); } 100% { transform:translateX(calc(100vw + 200px)); } }
        @keyframes truckBounce { 0%,100% { transform:translateY(0); } 50% { transform:translateY(-3px); } }
        @keyframes wheelSpin { from { transform:rotate(0deg); } to { transform:rotate(360deg); } }
        @keyframes gradientShift { 0% { background-position:0% 50%; } 50% { background-position:100% 50%; } 100% { background-position:0% 50%; } }
        @keyframes pulseGlow { 0%,100% { box-shadow:0 0 20px rgba(249,172,0,0.3); } 50% { box-shadow:0 0 40px rgba(249,172,0,0.6); } }
        @keyframes shimmer { 0% { background-position:-200% 0; } 100% { background-position:200% 0; } }
        @keyframes wave { 0% { transform:translateX(0) translateZ(0) scaleY(1); } 50% { transform:translateX(-25%) translateZ(0) scaleY(0.8); } 100% { transform:translateX(-50%) translateZ(0) scaleY(1); } }
        @keyframes containerRock { 0%,100% { transform:rotate(-2deg); } 50% { transform:rotate(2deg); } }
        @keyframes fadeScale { from { opacity:0; transform:scale(0.9); } to { opacity:1; transform:scale(1); } }
        @keyframes scrollDown { 0% { transform:translateY(0); opacity:1; } 100% { transform:translateY(20px); opacity:0; } }
        @keyframes blink { 0%,100% { opacity:1; } 50% { opacity:0.3; } }

        /* ── Animation Classes ── */
        .anim-fade-up { animation: fadeInUp 0.7s cubic-bezier(0.16,1,0.3,1) both; }
        .anim-fade { animation: fadeIn 0.8s ease-out both; }
        .anim-slide-left { animation: slideInLeft 0.7s cubic-bezier(0.16,1,0.3,1) both; }
        .anim-slide-right { animation: slideInRight 0.7s cubic-bezier(0.16,1,0.3,1) both; }
        .anim-float { animation: float 4s ease-in-out infinite; }
        .anim-float-slow { animation: floatSlow 6s ease-in-out infinite; }
        .anim-fade-scale { animation: fadeScale 0.6s cubic-bezier(0.16,1,0.3,1) both; }
        .anim-pulse-glow { animation: pulseGlow 2.5s ease-in-out infinite; }

        /* ── Scroll Reveal ── */
        .reveal { opacity:0; transform:translateY(40px); transition: opacity 0.8s cubic-bezier(0.16,1,0.3,1), transform 0.8s cubic-bezier(0.16,1,0.3,1); }
        .reveal.visible { opacity:1; transform:translateY(0); }
        .reveal-delay-1 { transition-delay: 0.15s; }
        .reveal-delay-2 { transition-delay: 0.3s; }
        .reveal-delay-3 { transition-delay: 0.45s; }
        .reveal-delay-4 { transition-delay: 0.6s; }

        /* ── Truck Animation ── */
        .truck-container { position:absolute; bottom:0; left:0; width:100%; overflow:hidden; pointer-events:none; }
        .truck-wrapper { animation: truckMove 18s linear infinite; }
        .truck-body { animation: truckBounce 0.3s ease-in-out infinite; }
        .wheel-spin { animation: wheelSpin 0.5s linear infinite; transform-origin: center; }

        /* ── Gradient Text ── */
        .gradient-text { background: linear-gradient(135deg, #024938, #1a9f8e, #f9ac00); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; background-size: 200% auto; animation: gradientShift 4s ease infinite; }

        /* ── Glassmorphism ── */
        .glass { background: rgba(255,255,255,0.08); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.12); }
        .glass-dark { background: rgba(1,36,31,0.6); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.08); }

        /* ── Wave Decoration ── */
        .wave-divider { position:relative; width:100%; overflow:hidden; line-height:0; }
        .wave-divider svg { display:block; width:calc(100% + 1.3px); height:60px; }
        .wave-fill { fill: #fff; }

        /* ── Shimmer Loading ── */
        .shimmer { background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent); background-size: 200% 100%; animation: shimmer 2s infinite; }

        /* ── Scroll Indicator ── */
        .scroll-indicator { animation: scrollDown 1.5s ease-in-out infinite; }

        /* ── Card Hover ── */
        .card-hover { transition: all 0.4s cubic-bezier(0.16,1,0.3,1); }
        .card-hover:hover { transform: translateY(-8px); box-shadow: 0 25px 50px -12px rgba(2,73,56,0.15); }

        /* ── Nav Link ── */
        .nav-link { position: relative; }
        .nav-link::after { content:''; position:absolute; bottom:-2px; left:0; width:0; height:2px; background: linear-gradient(90deg, #f9ac00, #ffb71a); transition: width 0.3s ease; }
        .nav-link:hover::after { width:100%; }

        /* ── Particles ── */
        .particle { position:absolute; border-radius:50%; pointer-events:none; }

        /* ── Custom Scrollbar ── */
        ::-webkit-scrollbar { width:8px; }
        ::-webkit-scrollbar-track { background:#f1f5f9; }
        ::-webkit-scrollbar-thumb { background:linear-gradient(180deg, #024938, #1a9f8e); border-radius:4px; }
        ::-webkit-scrollbar-thumb:hover { background:#f9ac00; }

        /* ── Toast ── */
        @keyframes toastIn { from { opacity:0; transform:translateX(100%); } to { opacity:1; transform:translateX(0); } }
        @keyframes toastOut { from { opacity:1; transform:translateX(0); } to { opacity:0; transform:translateX(100%); } }
        .toast-in { animation: toastIn 0.4s cubic-bezier(0.16,1,0.3,1) both; }
        .toast-out { animation: toastOut 0.3s ease-in both; }
    </style>
</head>
<body class="font-body antialiased text-slate-800 bg-white overflow-x-hidden">

    {{-- Toast Container --}}
    <div id="toastContainer" class="fixed top-5 right-5 z-[100] flex flex-col gap-3 w-full max-w-sm pointer-events-none"></div>

    @yield('content')

    {{-- Toast System --}}
    <script>
    (function() {
        const container = document.getElementById('toastContainer');
        function showToast(type, title, message) {
            const toast = document.createElement('div');
            toast.className = 'toast-in pointer-events-auto flex items-start gap-3 p-4 rounded-xl shadow-lg border backdrop-blur-sm';
            let iconSvg, bgClass, borderClass;
            if (type === 'success') { iconSvg = '<svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'; bgClass = 'bg-emerald-50/95'; borderClass = 'border-emerald-200'; }
            else if (type === 'error') { iconSvg = '<svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'; bgClass = 'bg-red-50/95'; borderClass = 'border-red-200'; }
            else { iconSvg = '<svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'; bgClass = 'bg-blue-50/95'; borderClass = 'border-blue-200'; }
            toast.classList.add(...bgClass.split(' '), ...borderClass.split(' '));
            toast.innerHTML = iconSvg + '<div class="flex-1 min-w-0"><p class="text-sm font-semibold text-gray-800">' + title + '</p>' + (message ? '<p class="text-sm text-gray-500 mt-0.5">' + message + '</p>' : '') + '</div><button onclick="this.parentElement.classList.add(\'toast-out\'); setTimeout(()=>this.parentElement.remove(), 300)" class="flex-shrink-0 text-gray-400 hover:text-gray-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>';
            container.appendChild(toast);
            setTimeout(() => { toast.classList.add('toast-out'); setTimeout(() => toast.remove(), 300); }, 5000);
        }
        window.showToast = showToast;
        @if(session('status')) showToast('success', 'Success', '{{ session('status') }}'); @endif
        @if(session('error')) showToast('error', 'Error', '{{ session('error') }}'); @endif
    })();
    </script>

    {{-- Scroll Reveal Observer --}}
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });
        document.querySelectorAll('.reveal').forEach(function(el) { observer.observe(el); });
    });
    </script>

    @include('partials.demo-notice')
    @stack('scripts')
</body>
</html>
