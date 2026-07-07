{{-- Hero Section with background image, green overlay, moving truck, and image gallery --}}
<section id="home" class="relative min-h-screen flex items-center overflow-hidden">

    {{-- Background image --}}
    <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ asset('2151468884.jpg') }}');"></div>

    {{-- Green overlay --}}
    <div class="absolute inset-0" style="background: linear-gradient(135deg, rgba(0,24,22,0.92) 0%, rgba(1,36,31,0.88) 30%, rgba(2,61,48,0.82) 60%, rgba(0,24,22,0.92) 100%); background-size: 400% 400%; animation: gradientShift 12s ease infinite;"></div>

    {{-- Dot pattern overlay --}}
    <div class="absolute inset-0 opacity-15" style="background-image: radial-gradient(rgba(255,255,255,0.12) 1.5px, transparent 1.5px); background-size: 28px 28px;"></div>

    {{-- Floating decorative orbs --}}
    <div class="absolute top-20 left-10 w-72 h-72 bg-emerald-500/10 rounded-full blur-3xl anim-float"></div>
    <div class="absolute bottom-20 right-10 w-96 h-96 bg-gold-500/8 rounded-full blur-3xl anim-float-slow"></div>
    <div class="absolute top-1/3 right-1/4 w-48 h-48 bg-emerald-400/8 rounded-full blur-2xl anim-float" style="animation-delay: 2s;"></div>

    {{-- Animated SVG cargo ship in background --}}
    <div class="absolute bottom-32 right-0 opacity-10 hidden lg:block">
        <svg width="600" height="300" viewBox="0 0 600 300" fill="none" xmlns="http://www.w3.org/2000/svg" class="anim-float-slow">
            <path d="M50 200 L550 200 L500 250 L100 250 Z" fill="#1a9f8e" opacity="0.6"/>
            <rect x="150" y="120" width="300" height="80" fill="#024938" opacity="0.5"/>
            <rect x="170" y="100" width="60" height="100" fill="#01241f" opacity="0.4"/>
            <rect x="250" y="100" width="60" height="100" fill="#01241f" opacity="0.4"/>
            <rect x="330" y="100" width="60" height="100" fill="#01241f" opacity="0.4"/>
            <rect x="410" y="80" width="40" height="120" fill="#001816" opacity="0.5"/>
            <circle cx="430" cy="60" r="8" fill="#f9ac00" opacity="0.5"/>
            <line x1="430" y1="68" x2="430" y2="80" stroke="#f9ac00" stroke-width="2" opacity="0.5"/>
        </svg>
    </div>

    {{-- Moving truck animation at bottom --}}
    <div class="truck-container h-24">
        <div class="truck-wrapper absolute bottom-2" style="animation-delay: 3s;">
            <div class="truck-body flex items-end gap-1">
                {{-- Truck cab --}}
                <svg width="80" height="50" viewBox="0 0 80 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M5 15 L35 15 L35 5 L55 5 L60 15 L70 15 L70 35 L5 35 Z" fill="#024938" stroke="#1a9f8e" stroke-width="1.5"/>
                    <rect x="38" y="8" width="16" height="10" fill="#80cbc0" opacity="0.7" rx="1"/>
                    <path d="M5 15 L35 15 L35 5 L40 5" stroke="#f9ac00" stroke-width="2" fill="none"/>
                    {{-- Wheels --}}
                    <circle cx="20" cy="40" r="8" fill="#001816" stroke="#4db5a8" stroke-width="1.5"/>
                    <circle cx="20" cy="40" r="4" fill="#1a9f8e"/>
                    <circle cx="55" cy="40" r="8" fill="#001816" stroke="#4db5a8" stroke-width="1.5"/>
                    <circle cx="55" cy="40" r="4" fill="#1a9f8e"/>
                </svg>
                {{-- Container/trailer --}}
                <svg width="120" height="50" viewBox="0 0 120 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="2" y="5" width="110" height="32" fill="#013028" stroke="#1a9f8e" stroke-width="1.5" rx="2"/>
                    <rect x="10" y="10" width="12" height="8" fill="#80cbc0" opacity="0.4" rx="1"/>
                    <rect x="28" y="10" width="12" height="8" fill="#80cbc0" opacity="0.4" rx="1"/>
                    <rect x="46" y="10" width="12" height="8" fill="#80cbc0" opacity="0.4" rx="1"/>
                    <rect x="64" y="10" width="12" height="8" fill="#80cbc0" opacity="0.4" rx="1"/>
                    <rect x="82" y="10" width="12" height="8" fill="#80cbc0" opacity="0.4" rx="1"/>
                    <rect x="96" y="10" width="12" height="8" fill="#80cbc0" opacity="0.4" rx="1"/>
                    <circle cx="100" cy="40" r="8" fill="#001816" stroke="#4db5a8" stroke-width="1.5"/>
                    <circle cx="100" cy="40" r="4" fill="#1a9f8e"/>
                </svg>
            </div>
        </div>
        {{-- Second truck (opposite direction, delayed) --}}
        <div class="truck-wrapper absolute bottom-2" style="animation-delay: 10s; animation-duration: 22s;">
            <div class="truck-body flex items-end gap-1">
                <svg width="80" height="45" viewBox="0 0 80 45" fill="none">
                    <path d="M5 10 L50 10 L55 20 L70 20 L70 32 L5 32 Z" fill="#023d30" stroke="#4db5a8" stroke-width="1"/>
                    <rect x="10" y="13" width="30" height="8" fill="#80cbc0" opacity="0.3" rx="1"/>
                    <circle cx="20" cy="36" r="6" fill="#001816" stroke="#4db5a8" stroke-width="1"/>
                    <circle cx="55" cy="36" r="6" fill="#001816" stroke="#4db5a8" stroke-width="1"/>
                </svg>
                <svg width="100" height="45" viewBox="0 0 100 45" fill="none">
                    <rect x="2" y="3" width="90" height="28" fill="#01241f" stroke="#4db5a8" stroke-width="1" rx="2"/>
                    <text x="50" y="22" text-anchor="middle" fill="#f9ac00" font-size="10" font-weight="bold" font-family="Poppins">CAF</text>
                    <circle cx="85" cy="36" r="6" fill="#001816" stroke="#4db5a8" stroke-width="1"/>
                </svg>
            </div>
        </div>
        {{-- Road line --}}
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-emerald-600/30 to-transparent"></div>
    </div>

    {{-- Content --}}
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-28 pb-32">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            {{-- Left: Text --}}
            <div class="text-center lg:text-left">
                {{-- Badge --}}
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass mb-6 anim-fade-up">
                    <span class="w-2 h-2 rounded-full bg-gold-400" style="animation: blink 2s infinite;"></span>
                    <span class="text-xs font-semibold text-emerald-100 tracking-wide">Tanzania's Trusted Logistics Partner</span>
                </div>

                {{-- Heading --}}
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-heading font-extrabold text-white leading-tight anim-fade-up" style="animation-delay: 0.1s;">
                    Clearing & Forwarding<br>
                    <span class="gradient-text">Made Seamless</span>
                </h1>

                {{-- Subtitle --}}
                <p class="mt-6 text-lg text-emerald-100/80 max-w-xl mx-auto lg:mx-0 anim-fade-up" style="animation-delay: 0.2s;">
                    We clear your cargo through TRA at all Tanzanian ports and borders, insure your goods through our partner insurers, and transport them to any destination — within Tanzania or across borders to Congo, Rwanda, Burundi, and beyond.
                </p>

                {{-- CTA buttons --}}
                <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center lg:justify-start anim-fade-up" style="animation-delay: 0.3s;">
                    <a href="#tracking" class="inline-flex items-center justify-center gap-2 px-8 py-3.5 text-sm font-bold text-gray-900 bg-gradient-to-r from-gold-300 to-gold-400 hover:from-gold-400 hover:to-gold-500 rounded-xl shadow-xl hover:shadow-2xl transition-all hover:scale-105">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Track Your Shipment
                    </a>
                    <a href="#services" class="inline-flex items-center justify-center gap-2 px-8 py-3.5 text-sm font-bold text-white glass hover:bg-white/15 rounded-xl border border-white/20 transition-all hover:scale-105">
                        Our Services
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                    </a>
                </div>

                {{-- Trust indicators --}}
                <div class="mt-10 flex flex-wrap gap-6 justify-center lg:justify-start anim-fade-up" style="animation-delay: 0.4s;">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-sm text-emerald-100/70">TRA Licensed</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-sm text-emerald-100/70">TISCAN Certified</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-sm text-emerald-100/70">24/7 Support</span>
                    </div>
                </div>
            </div>

            {{-- Right: Image gallery slideshow --}}
            <div class="hidden lg:block anim-fade-scale" style="animation-delay: 0.5s;">
                <div class="relative">
                    {{-- Glow effect --}}
                    <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/20 to-gold-500/10 rounded-3xl blur-2xl"></div>

                    {{-- Main slideshow card --}}
                    <div class="relative rounded-2xl overflow-hidden shadow-2xl border border-white/10" style="width: 520px; height: 420px;">
                        {{-- Slides --}}
                        <div class="hero-slide absolute inset-0 transition-opacity duration-1000" style="opacity: 1;">
                            <img src="{{ asset('130875.jpg') }}" alt="Port Operations" class="w-full h-full object-cover">
                            <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/70 to-transparent">
                                <p class="text-sm font-bold text-white">Port Operations</p>
                                <p class="text-xs text-white/70">Dar es Salaam Port</p>
                            </div>
                        </div>
                        <div class="hero-slide absolute inset-0 transition-opacity duration-1000" style="opacity: 0;">
                            <img src="{{ asset('735.jpg') }}" alt="Cargo Handling" class="w-full h-full object-cover">
                            <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/70 to-transparent">
                                <p class="text-sm font-bold text-white">Cargo Handling</p>
                                <p class="text-xs text-white/70">Professional Loading & Unloading</p>
                            </div>
                        </div>
                        <div class="hero-slide absolute inset-0 transition-opacity duration-1000" style="opacity: 0;">
                            <img src="{{ asset('1200.jpg') }}" alt="Container Terminal" class="w-full h-full object-cover">
                            <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/70 to-transparent">
                                <p class="text-sm font-bold text-white">Container Terminal</p>
                                <p class="text-xs text-white/70">Efficient Container Management</p>
                            </div>
                        </div>
                        <div class="hero-slide absolute inset-0 transition-opacity duration-1000" style="opacity: 0;">
                            <img src="{{ asset('1374.jpg') }}" alt="Logistics" class="w-full h-full object-cover">
                            <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/70 to-transparent">
                                <p class="text-sm font-bold text-white">Logistics Operations</p>
                                <p class="text-xs text-white/70">End-to-End Supply Chain</p>
                            </div>
                        </div>
                        <div class="hero-slide absolute inset-0 transition-opacity duration-1000" style="opacity: 0;">
                            <img src="{{ asset('63728.jpg') }}" alt="Warehouse" class="w-full h-full object-cover">
                            <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/70 to-transparent">
                                <p class="text-sm font-bold text-white">Warehousing</p>
                                <p class="text-xs text-white/70">Secure Storage Solutions</p>
                            </div>
                        </div>
                        <div class="hero-slide absolute inset-0 transition-opacity duration-1000" style="opacity: 0;">
                            <img src="{{ asset('71406.jpg') }}" alt="Transport" class="w-full h-full object-cover">
                            <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/70 to-transparent">
                                <p class="text-sm font-bold text-white">Transport Fleet</p>
                                <p class="text-xs text-white/70">Reliable Delivery Network</p>
                            </div>
                        </div>
                        <div class="hero-slide absolute inset-0 transition-opacity duration-1000" style="opacity: 0;">
                            <img src="{{ asset('73979.jpg') }}" alt="Freight" class="w-full h-full object-cover">
                            <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/70 to-transparent">
                                <p class="text-sm font-bold text-white">Freight Services</p>
                                <p class="text-xs text-white/70">Air, Sea & Land Cargo</p>
                            </div>
                        </div>
                        <div class="hero-slide absolute inset-0 transition-opacity duration-1000" style="opacity: 0;">
                            <img src="{{ asset('2151468884.jpg') }}" alt="Shipping" class="w-full h-full object-cover">
                            <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/70 to-transparent">
                                <p class="text-sm font-bold text-white">Shipping Lines</p>
                                <p class="text-xs text-white/70">Global Maritime Network</p>
                            </div>
                        </div>
                        <div class="hero-slide absolute inset-0 transition-opacity duration-1000" style="opacity: 0;">
                            <img src="{{ asset('2152005472.jpg') }}" alt="Cargo Delivery" class="w-full h-full object-cover">
                            <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/70 to-transparent">
                                <p class="text-sm font-bold text-white">Cargo Delivery</p>
                                <p class="text-xs text-white/70">Last-Mile Excellence</p>
                            </div>
                        </div>
                        <div class="hero-slide absolute inset-0 transition-opacity duration-1000" style="opacity: 0;">
                            <img src="{{ asset('air-freight-delivery-cargo-plane-truck-packages-boxes-transportation-logistics.png') }}" alt="Air Freight" class="w-full h-full object-cover">
                            <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/70 to-transparent">
                                <p class="text-sm font-bold text-white">Air Freight</p>
                                <p class="text-xs text-white/70">Express Air Cargo Services</p>
                            </div>
                        </div>

                        {{-- Slide indicators --}}
                        <div class="absolute bottom-4 right-4 flex gap-1.5 z-10">
                            <span class="hero-dot w-2 h-2 rounded-full bg-gold-400 transition-all"></span>
                            <span class="hero-dot w-2 h-2 rounded-full bg-white/30 transition-all"></span>
                            <span class="hero-dot w-2 h-2 rounded-full bg-white/30 transition-all"></span>
                            <span class="hero-dot w-2 h-2 rounded-full bg-white/30 transition-all"></span>
                            <span class="hero-dot w-2 h-2 rounded-full bg-white/30 transition-all"></span>
                            <span class="hero-dot w-2 h-2 rounded-full bg-white/30 transition-all"></span>
                            <span class="hero-dot w-2 h-2 rounded-full bg-white/30 transition-all"></span>
                            <span class="hero-dot w-2 h-2 rounded-full bg-white/30 transition-all"></span>
                            <span class="hero-dot w-2 h-2 rounded-full bg-white/30 transition-all"></span>
                            <span class="hero-dot w-2 h-2 rounded-full bg-white/30 transition-all"></span>
                        </div>
                    </div>

                    {{-- Floating badge --}}
                    <div class="absolute -top-5 -right-5 glass rounded-xl px-4 py-3 shadow-xl border border-gold-400/20 anim-float">
                        <div class="flex items-center gap-2">
                            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-gold-300 to-gold-500 flex items-center justify-center">
                                <svg class="w-5 h-5 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8v8M21 7l-9 9-3-3"/></svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-white">98.5%</p>
                                <p class="text-[9px] text-emerald-200/60">On-time delivery</p>
                            </div>
                        </div>
                    </div>

                    {{-- Thumbnail strip --}}
                    <div class="mt-3 flex gap-2 overflow-hidden" style="width: 520px;">
                        <img src="{{ asset('735.jpg') }}" alt="" class="hero-thumb w-16 h-12 rounded-lg object-cover border-2 border-transparent cursor-pointer hover:border-gold-400 transition-all">
                        <img src="{{ asset('1200.jpg') }}" alt="" class="hero-thumb w-16 h-12 rounded-lg object-cover border-2 border-transparent cursor-pointer hover:border-gold-400 transition-all">
                        <img src="{{ asset('1374.jpg') }}" alt="" class="hero-thumb w-16 h-12 rounded-lg object-cover border-2 border-transparent cursor-pointer hover:border-gold-400 transition-all">
                        <img src="{{ asset('63728.jpg') }}" alt="" class="hero-thumb w-16 h-12 rounded-lg object-cover border-2 border-transparent cursor-pointer hover:border-gold-400 transition-all">
                        <img src="{{ asset('71406.jpg') }}" alt="" class="hero-thumb w-16 h-12 rounded-lg object-cover border-2 border-transparent cursor-pointer hover:border-gold-400 transition-all">
                        <img src="{{ asset('73979.jpg') }}" alt="" class="hero-thumb w-16 h-12 rounded-lg object-cover border-2 border-transparent cursor-pointer hover:border-gold-400 transition-all">
                        <img src="{{ asset('2152005472.jpg') }}" alt="" class="hero-thumb w-16 h-12 rounded-lg object-cover border-2 border-transparent cursor-pointer hover:border-gold-400 transition-all">
                        <img src="{{ asset('air-freight-delivery-cargo-plane-truck-packages-boxes-transportation-logistics.png') }}" alt="" class="hero-thumb w-16 h-12 rounded-lg object-cover border-2 border-transparent cursor-pointer hover:border-gold-400 transition-all">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Scroll indicator --}}
    <div class="absolute bottom-28 left-1/2 -translate-x-1/2 hidden lg:block">
        <div class="flex flex-col items-center gap-2">
            <span class="text-[10px] text-emerald-200/40 font-medium tracking-widest uppercase">Scroll</span>
            <div class="w-5 h-9 rounded-full border-2 border-emerald-200/30 flex justify-center pt-1.5">
                <div class="w-1 h-2 rounded-full bg-gold-400 scroll-indicator"></div>
            </div>
        </div>
    </div>

    {{-- Wave divider at bottom --}}
    <div class="absolute bottom-0 left-0 right-0 z-10">
        <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto">
            <path d="M0 80L60 70C120 60 240 40 360 35C480 30 600 40 720 45C840 50 960 50 1080 45C1200 40 1320 30 1380 25L1440 20V80H0Z" fill="white"/>
        </svg>
    </div>
</section>

<script>
(function() {
    const slides = document.querySelectorAll('.hero-slide');
    const dots = document.querySelectorAll('.hero-dot');
    const thumbs = document.querySelectorAll('.hero-thumb');
    let current = 0;
    let total = slides.length;

    function showSlide(index) {
        slides.forEach(function(s, i) {
            s.style.opacity = i === index ? '1' : '0';
        });
        dots.forEach(function(d, i) {
            if (i === index) {
                d.classList.remove('bg-white/30');
                d.classList.add('bg-gold-400');
                d.classList.add('w-4');
            } else {
                d.classList.add('bg-white/30');
                d.classList.remove('bg-gold-400');
                d.classList.remove('w-4');
            }
        });
        thumbs.forEach(function(t, i) {
            if (i === index) {
                t.classList.remove('border-transparent');
                t.classList.add('border-gold-400');
            } else {
                t.classList.add('border-transparent');
                t.classList.remove('border-gold-400');
            }
        });
        current = index;
    }

    function nextSlide() {
        showSlide((current + 1) % total);
    }

    let timer = setInterval(nextSlide, 3500);

    thumbs.forEach(function(thumb, i) {
        thumb.addEventListener('click', function() {
            clearInterval(timer);
            showSlide(i);
            timer = setInterval(nextSlide, 3500);
        });
    });
})();
</script>
