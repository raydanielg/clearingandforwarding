{{-- Tracking Section --}}
<section id="tracking" class="py-24 bg-white relative overflow-hidden">
    <div class="absolute top-0 right-0 w-96 h-96 bg-emerald-50 rounded-full blur-3xl opacity-50"></div>

    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="text-center mb-12">
            <span class="reveal inline-block px-4 py-1.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold tracking-widest uppercase mb-4">Track & Trace</span>
            <h2 class="reveal reveal-delay-1 text-3xl sm:text-4xl lg:text-5xl font-heading font-extrabold text-gray-900 leading-tight">
                Track Your Shipment
            </h2>
            <p class="reveal reveal-delay-2 mt-4 text-lg text-gray-500">
                Enter your File Number or Bill of Lading to see real-time status.
            </p>
        </div>

        {{-- Track form --}}
        <div class="reveal reveal-delay-2">
            <div class="bg-gradient-to-br from-emerald-800 to-emerald-900 rounded-3xl p-8 lg:p-10 shadow-2xl relative overflow-hidden">
                {{-- Decorative --}}
                <div class="absolute top-0 right-0 w-64 h-64 bg-gold-500/10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-emerald-400/10 rounded-full blur-2xl"></div>

                <div class="relative">
                    <form id="trackForm" class="flex flex-col sm:flex-row gap-3">
                        <div class="flex-1 relative">
                            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input type="text" id="trackInput" placeholder="e.g. CAF/IMP/2026/0001 or BL No." class="w-full pl-12 pr-4 py-4 rounded-xl bg-white/10 border border-white/20 text-white placeholder-emerald-200/40 focus:outline-none focus:ring-2 focus:ring-gold-400 focus:bg-white/15 transition-all">
                        </div>
                        <button type="submit" class="inline-flex items-center justify-center gap-2 px-8 py-4 text-sm font-bold text-gray-900 bg-gradient-to-r from-gold-300 to-gold-400 hover:from-gold-400 hover:to-gold-500 rounded-xl shadow-lg transition-all hover:scale-105 whitespace-nowrap">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            Track Now
                        </button>
                    </form>

                    {{-- Result area --}}
                    <div id="trackResult" class="mt-6 hidden">
                        {{-- Will be filled by AJAX --}}
                    </div>

                    {{-- Quick examples --}}
                    <div class="mt-6 flex flex-wrap items-center gap-2">
                        <span class="text-xs text-emerald-200/50">Try:</span>
                        <button class="track-example text-xs px-3 py-1 rounded-full bg-white/10 text-emerald-200 hover:bg-white/20 transition-all" data-query="CAF/IMP/2026/0001">CAF/IMP/2026/0001</button>
                        <button class="track-example text-xs px-3 py-1 rounded-full bg-white/10 text-emerald-200 hover:bg-white/20 transition-all" data-query="MAEU1234567">MAEU1234567</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- How it works --}}
        <div class="reveal reveal-delay-3 mt-12 grid sm:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="w-12 h-12 mx-auto rounded-xl bg-emerald-100 flex items-center justify-center mb-3">
                    <span class="text-emerald-700 font-bold">1</span>
                </div>
                <h4 class="text-sm font-bold text-gray-900">Enter File No. or BL</h4>
                <p class="text-xs text-gray-500 mt-1">Type your tracking number above</p>
            </div>
            <div class="text-center">
                <div class="w-12 h-12 mx-auto rounded-xl bg-gold-100 flex items-center justify-center mb-3">
                    <span class="text-gold-600 font-bold">2</span>
                </div>
                <h4 class="text-sm font-bold text-gray-900">Instant Results</h4>
                <p class="text-xs text-gray-500 mt-1">See current status and milestones</p>
            </div>
            <div class="text-center">
                <div class="w-12 h-12 mx-auto rounded-xl bg-emerald-100 flex items-center justify-center mb-3">
                    <span class="text-emerald-700 font-bold">3</span>
                </div>
                <h4 class="text-sm font-bold text-gray-900">Stay Updated</h4>
                <p class="text-xs text-gray-500 mt-1">Get SMS/Email notifications</p>
            </div>
        </div>
    </div>
</section>

<script>
$(document).ready(function() {
    function performTrack(query) {
        const resultDiv = $('#trackResult');
        resultDiv.removeClass('hidden').html(`
            <div class="flex items-center justify-center py-8">
                <div class="w-8 h-8 border-3 border-emerald-300 border-t-gold-400 rounded-full animate-spin"></div>
                <span class="ml-3 text-sm text-emerald-100">Searching...</span>
            </div>
        `);

        $.ajax({
            url: '{{ route("public.track") }}',
            method: 'POST',
            data: { query: query, _token: '{{ csrf_token() }}' },
            success: function(data) {
                if (!data.success) {
                    resultDiv.html(`
                        <div class="bg-red-500/10 rounded-xl p-5 border border-red-400/20 anim-fade-up text-center">
                            <svg class="w-10 h-10 mx-auto text-red-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <p class="text-sm text-red-200">${data.message || 'No shipment found.'}</p>
                        </div>
                    `);
                    return;
                }

                let milestonesHtml = '';
                if (data.milestones && data.milestones.length > 0) {
                    milestonesHtml = '<div class="mt-4 space-y-2">';
                    data.milestones.forEach(function(m) {
                        milestonesHtml += `
                            <div class="flex items-start gap-3 p-2 rounded-lg bg-white/5">
                                <div class="w-2 h-2 rounded-full bg-gold-400 mt-1.5 flex-shrink-0"></div>
                                <div>
                                    <p class="text-xs text-white capitalize font-semibold">${m.stage}</p>
                                    <p class="text-[10px] text-emerald-200/50">${m.date}${m.remarks ? ' · ' + m.remarks : ''}</p>
                                </div>
                            </div>
                        `;
                    });
                    milestonesHtml += '</div>';
                }

                resultDiv.html(`
                    <div class="bg-white/10 rounded-xl p-5 border border-white/10 anim-fade-up">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <p class="text-xs text-emerald-200/50">File Number</p>
                                <p class="text-lg font-bold text-white">${data.file_no}</p>
                            </div>
                            <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-semibold capitalize">${data.status.replace(/_/g, ' ')}</span>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                            <div><p class="text-emerald-200/50 text-xs">BL/AWB</p><p class="text-white">${data.bl_awb || '—'}</p></div>
                            <div><p class="text-emerald-200/50 text-xs">Origin</p><p class="text-white">${data.origin || '—'}</p></div>
                            <div><p class="text-emerald-200/50 text-xs">Destination</p><p class="text-white">${data.destination || '—'}</p></div>
                            <div><p class="text-emerald-200/50 text-xs">ETA</p><p class="text-white">${data.eta || '—'}</p></div>
                        </div>
                        ${milestonesHtml}
                    </div>
                `);
            },
            error: function() {
                resultDiv.html(`
                    <div class="bg-red-500/10 rounded-xl p-5 border border-red-400/20 anim-fade-up text-center">
                        <svg class="w-10 h-10 mx-auto text-red-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <p class="text-sm text-red-200">No shipment found. Please check your tracking number and try again.</p>
                    </div>
                `);
            }
        });
    }

    $('#trackForm').on('submit', function(e) {
        e.preventDefault();
        const query = $('#trackInput').val().trim();
        if (query) performTrack(query);
    });

    $('.track-example').on('click', function() {
        const query = $(this).data('query');
        $('#trackInput').val(query);
        performTrack(query);
    });
});
</script>
