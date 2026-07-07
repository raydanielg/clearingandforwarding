{{-- Stats Section with animated counters --}}
<section class="py-20 bg-gradient-to-r from-emerald-900 via-emerald-800 to-emerald-900 relative overflow-hidden">
    {{-- Animated background dots --}}
    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(rgba(255,255,255,0.15) 1px, transparent 1px); background-size: 24px 24px;"></div>
    <div class="absolute top-0 left-1/3 w-72 h-72 bg-gold-500/8 rounded-full blur-3xl"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
            {{-- Stat 1 --}}
            <div class="reveal text-center">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center mb-4 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <p class="text-4xl lg:text-5xl font-heading font-extrabold text-white counter" data-target="5000" data-suffix="+">0</p>
                <p class="mt-2 text-sm text-emerald-100/60 font-medium">Shipments Cleared</p>
            </div>

            {{-- Stat 2 --}}
            <div class="reveal reveal-delay-1 text-center">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-gold-300 to-gold-500 flex items-center justify-center mb-4 shadow-lg">
                    <svg class="w-8 h-8 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.062-.18-2.087-.514-3.056z"/></svg>
                </div>
                <p class="text-4xl lg:text-5xl font-heading font-extrabold text-white counter" data-target="1200" data-suffix="+">0</p>
                <p class="mt-2 text-sm text-emerald-100/60 font-medium">Insurance Policies Issued</p>
            </div>

            {{-- Stat 3 --}}
            <div class="reveal reveal-delay-2 text-center">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center mb-4 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 17h6m-6 0V5a1 1 0 011-1h8a1 1 0 011 1v12h-2m-6 0V7H5"/></svg>
                </div>
                <p class="text-4xl lg:text-5xl font-heading font-extrabold text-white counter" data-target="6" data-suffix="">0</p>
                <p class="mt-2 text-sm text-emerald-100/60 font-medium">Countries Served</p>
            </div>

            {{-- Stat 4 --}}
            <div class="reveal reveal-delay-3 text-center">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-gold-400 to-gold-600 flex items-center justify-center mb-4 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <p class="text-4xl lg:text-5xl font-heading font-extrabold text-white counter" data-target="10" data-suffix="+">0</p>
                <p class="mt-2 text-sm text-emerald-100/60 font-medium">Years Experience</p>
            </div>
        </div>
    </div>
</section>

<script>
(function() {
    const counters = document.querySelectorAll('.counter');
    let animated = false;

    function animateCounters() {
        if (animated) return;
        animated = true;

        counters.forEach(function(counter) {
            const target = parseInt(counter.dataset.target);
            const suffix = counter.dataset.suffix || '';
            let current = 0;
            const increment = Math.ceil(target / 60);
            const duration = 2000;
            const stepTime = duration / (target / increment);

            const timer = setInterval(function() {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                counter.textContent = current.toLocaleString() + suffix;
            }, stepTime);
        });
    }

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) animateCounters();
        });
    }, { threshold: 0.3 });

    counters.forEach(function(c) { observer.observe(c.parentElement); });
})();
</script>
