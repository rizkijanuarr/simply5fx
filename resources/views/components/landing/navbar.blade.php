<nav class="fixed top-8 left-0 right-0 z-50 bg-white/80 backdrop-blur-xl" style="border-bottom: 1px solid rgba(0,0,0,0.06);">
    <div class="max-w-6xl mx-auto px-5 flex items-center justify-between h-14">
        <a href="/" class="flex items-center gap-2">
            <span class="font-boldonse text-lg text-brand">S5FX</span>
        </a>
        <div class="flex items-center gap-3">
            <a href="/backoffice"
               class="hidden sm:inline-flex items-center gap-2 px-5 py-2 rounded-full text-sm font-semibold text-white bg-brand shadow-[0_2px_12px_rgba(51,125,255,0.3)] hover:shadow-[0_4px_20px_rgba(51,125,255,0.4)] transition-all duration-200 hover:-translate-y-px">
                Buka Dashboard
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M5 12h14M12 5l7 7-7 7"/>
                </svg>
            </a>
            <button id="hamburger" class="md:hidden p-2 rounded-lg text-gray-500 hover:text-gray-900">
                <svg id="icon-menu" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <line x1="3" y1="12" x2="21" y2="12"/>
                    <line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
                <svg id="icon-close" class="hidden" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
    </div>
    <!-- Mobile menu -->
    <div id="mobile-menu" style="max-height:0; overflow:hidden; opacity:0; transition: max-height 0.3s ease, opacity 0.3s ease;">
        <div class="max-w-6xl mx-auto px-5 py-3 flex flex-col gap-2 border-t border-gray-100">
            <a href="/backoffice" class="block py-2 text-sm font-semibold text-[#337DFF]">Buka Dashboard</a>
        </div>
    </div>
</nav>
