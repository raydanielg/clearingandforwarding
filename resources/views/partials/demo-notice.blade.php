{{-- Demo Notice Modal + Floating Button --}}
<style>
@keyframes demoModalIn { from { opacity:0; transform:scale(0.92) translateY(20px); } to { opacity:1; transform:scale(1) translateY(0); } }
@keyframes demoModalOut { from { opacity:1; transform:scale(1); } to { opacity:0; transform:scale(0.92); } }
@keyframes demoOverlayIn { from { opacity:0; } to { opacity:1; } }
@keyframes demoOverlayOut { from { opacity:1; } to { opacity:0; } }
@keyframes demoFloatPulse { 0%,100% { box-shadow:0 0 0 0 rgba(37,211,102,0.5); } 50% { box-shadow:0 0 0 12px rgba(37,211,102,0); } }
@keyframes demoFloatIn { from { opacity:0; transform:translateY(30px) scale(0.8); } to { opacity:1; transform:translateY(0) scale(1); } }
.demo-modal-in { animation: demoModalIn 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.demo-modal-out { animation: demoModalOut 0.3s ease-in both; }
.demo-overlay-in { animation: demoOverlayIn 0.3s ease-out both; }
.demo-overlay-out { animation: demoOverlayOut 0.3s ease-in both; }
.demo-float-pulse { animation: demoFloatPulse 2.5s ease-in-out infinite; }
.demo-float-in { animation: demoFloatIn 0.5s cubic-bezier(0.16,1,0.3,1) both; }
</style>

{{-- Demo Notice Modal --}}
<div id="demoOverlay" class="fixed inset-0 z-[9999] hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.55); backdrop-filter:blur(6px); -webkit-backdrop-filter:blur(6px);">
    <div id="demoModal" class="demo-modal-in relative w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden">
        {{-- Header bar --}}
        <div class="px-6 pt-6 pb-4" style="background: linear-gradient(135deg, #024938, #013028);">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white font-heading">Demo Website</h3>
                    <p class="text-xs text-emerald-200/80 mt-0.5">For demonstration purposes only</p>
                </div>
            </div>
        </div>

        {{-- Body --}}
        <div class="px-6 py-5">
            <p class="text-sm text-gray-600 leading-relaxed">
                This is a <span class="font-semibold text-gray-800">demo website</span> built for showcase purposes. It is
                <span class="font-semibold text-gray-800">not owned by any company</span> and no real transactions are processed here.
            </p>
            <p class="text-sm text-gray-500 leading-relaxed mt-3">
                Interested in this system? Want to customize it for your business? Let's talk!
            </p>

            {{-- Action buttons --}}
            <div class="mt-5 flex flex-col gap-2.5">
                <a href="https://wa.me/255613976254?text=Hello%2C%20I%20saw%20your%20demo%20clearing%20and%20forwarding%20system%20and%20I%27m%20interested.%20Can%20we%20negotiate%3F"
                   target="_blank" rel="noopener noreferrer"
                   class="w-full flex items-center justify-center gap-2.5 px-5 py-3 rounded-xl text-white text-sm font-semibold transition-all hover:scale-[1.02] active:scale-[0.98]"
                   style="background: linear-gradient(135deg, #25D366, #128C7E);">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                    Negotiate on WhatsApp
                </a>
                <button onclick="closeDemoModal()" class="w-full px-5 py-2.5 rounded-xl text-gray-500 text-sm font-medium hover:bg-gray-100 transition-colors">
                    Maybe later
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Floating Reopen Button (bottom-left) --}}
<button id="demoFloatBtn" onclick="openDemoModal()" class="demo-float-in demo-float-pulse fixed bottom-5 left-5 z-[9998] hidden items-center justify-center w-14 h-14 rounded-full text-white shadow-lg transition-all hover:scale-110 active:scale-95"
        style="background: linear-gradient(135deg, #25D366, #128C7E);"
        title="Demo info & negotiate">
    <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
</button>

<script>
(function() {
    var overlay = document.getElementById('demoOverlay');
    var modal = document.getElementById('demoModal');
    var floatBtn = document.getElementById('demoFloatBtn');
    var STORAGE_KEY = 'demo_notice_dismissed';

    function openDemoModal() {
        overlay.classList.remove('hidden');
        overlay.classList.add('flex', 'demo-overlay-in');
        modal.classList.remove('demo-modal-out');
        modal.classList.add('demo-modal-in');
        floatBtn.style.display = 'none';
    }
    window.openDemoModal = openDemoModal;

    function closeDemoModal() {
        modal.classList.remove('demo-modal-in');
        modal.classList.add('demo-modal-out');
        overlay.classList.remove('demo-overlay-in');
        overlay.classList.add('demo-overlay-out');
        setTimeout(function() {
            overlay.classList.remove('flex', 'demo-overlay-out');
            overlay.classList.add('hidden');
            floatBtn.style.display = 'flex';
        }, 300);
        localStorage.setItem(STORAGE_KEY, '1');
    }
    window.closeDemoModal = closeDemoModal;

    // Close on overlay click
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) closeDemoModal();
    });

    // Close on Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !overlay.classList.contains('hidden')) closeDemoModal();
    });

    // On load: show modal if first visit, otherwise show floating button
    document.addEventListener('DOMContentLoaded', function() {
        if (localStorage.getItem(STORAGE_KEY)) {
            floatBtn.style.display = 'flex';
        } else {
            setTimeout(function() {
                openDemoModal();
            }, 800);
        }
    });
})();
</script>
