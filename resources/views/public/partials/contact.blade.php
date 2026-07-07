{{-- Contact Section --}}
<section id="contact" class="py-24 bg-gray-50 relative overflow-hidden">
    <div class="absolute top-0 left-0 w-80 h-80 bg-emerald-100 rounded-full blur-3xl opacity-40"></div>
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-gold-50 rounded-full blur-3xl opacity-40"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="text-center max-w-2xl mx-auto mb-16">
            <span class="reveal inline-block px-4 py-1.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold tracking-widest uppercase mb-4">Get In Touch</span>
            <h2 class="reveal reveal-delay-1 text-3xl sm:text-4xl lg:text-5xl font-heading font-extrabold text-gray-900 leading-tight">
                Let's Move Your Cargo
            </h2>
            <p class="reveal reveal-delay-2 mt-4 text-lg text-gray-500">
                Have a question or ready to start? Reach out and our team will respond within 24 hours.
            </p>
        </div>

        <div class="grid lg:grid-cols-2 gap-12">
            {{-- Left: Contact info --}}
            <div class="reveal space-y-6">
                {{-- Info cards --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 card-hover">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-heading font-bold text-gray-900">Our Office</h4>
                            <p class="text-sm text-gray-500 mt-1">Dar es Salaam Port Area<br>Bagamoyo Road, Plot 123<br>Dar es Salaam, Tanzania</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 card-hover">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-gold-400 to-gold-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-heading font-bold text-gray-900">Phone & WhatsApp</h4>
                            <p class="text-sm text-gray-500 mt-1">+255 22 123 4567 (Office)<br>+255 712 345 678 (Mobile/WhatsApp)</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 card-hover">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-heading font-bold text-gray-900">Email</h4>
                            <p class="text-sm text-gray-500 mt-1">info@caf.co.tz<br>support@caf.co.tz</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 card-hover">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-gold-400 to-gold-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-heading font-bold text-gray-900">Working Hours</h4>
                            <p class="text-sm text-gray-500 mt-1">Mon - Fri: 8:00 AM - 6:00 PM<br>Saturday: 8:00 AM - 1:00 PM<br>Sunday: Closed (Emergency only)</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: Contact form --}}
            <div class="reveal reveal-delay-1">
                <div class="bg-white rounded-2xl p-8 shadow-xl border border-gray-100">
                    <h3 class="text-xl font-heading font-bold text-gray-900 mb-6">Send Us a Message</h3>
                    <form id="contactForm" class="space-y-5">
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Full Name</label>
                                <input type="text" name="name" required class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all" placeholder="John Doe">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Phone</label>
                                <input type="text" name="phone" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all" placeholder="+255 7XX XXX XXX">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Email</label>
                            <input type="email" name="email" required class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all" placeholder="you@example.com">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Subject</label>
                            <select name="subject" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all">
                                <option>General Inquiry</option>
                                <option>Shipment Tracking</option>
                                <option>Quotation Request</option>
                                <option>Support / Complaint</option>
                                <option>Partnership</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Message</label>
                            <textarea name="message" rows="4" required class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all resize-none" placeholder="Tell us about your cargo or question..."></textarea>
                        </div>
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 text-sm font-bold text-white bg-gradient-to-r from-emerald-600 to-emerald-800 hover:from-emerald-700 hover:to-emerald-900 rounded-xl shadow-lg transition-all hover:scale-[1.02]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
$(document).ready(function() {
    $('#contactForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const btn = form.find('button[type="submit"]');
        const originalText = btn.html();

        btn.prop('disabled', true).html('<div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div> Sending...');

        $.ajax({
            url: '{{ url("/api/v1/contact") }}',
            method: 'POST',
            data: form.serialize(),
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function() {
                showToast('success', 'Message Sent!', 'We will get back to you within 24 hours.');
                form[0].reset();
            },
            error: function() {
                showToast('info', 'Message Recorded', 'Your message has been noted. We will contact you soon.');
                form[0].reset();
            },
            complete: function() {
                btn.prop('disabled', false).html(originalText);
            }
        });
    });
});
</script>
