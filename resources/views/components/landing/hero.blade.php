<section class="relative min-h-screen flex flex-col items-center justify-center pt-28 pb-10 overflow-hidden">
    <!-- Subtle grid background -->
    <div class="absolute inset-0 pointer-events-none"
         style="background-image: linear-gradient(rgba(51,125,255,0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(51,125,255,0.03) 1px, transparent 1px); background-size: 80px 80px;">
    </div>

    <div class="relative max-w-6xl mx-auto px-5 w-full">
        <!-- AI badge -->
        <div class="text-center mb-2 fade-up">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-semibold tracking-wider uppercase"
                  style="color: #337DFF; background: rgba(51,125,255,0.06); border: 1px solid rgba(51,125,255,0.15);">
                <span class="w-1.5 h-1.5 rounded-full bg-[#337DFF]" style="animation: pulse 2s ease-in-out infinite;"></span>
                Bertenaga Gemini AI
            </span>
        </div>

        <!-- Composition: giant bg text + orbiting quotes + avatar -->
        <div class="relative flex flex-col items-center justify-center" style="min-height: 520px;">

            <!-- BACK LAYER: giant decorative text -->
            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none select-none z-0" aria-hidden="true">
                <div class="hero-text-bg text-center">
                    <div class="text-5xl sm:text-7xl md:text-8xl lg:text-[120px] font-boldonse" style="color: rgba(26,26,26,0.08);">JURNAL</div>
                    <div class="text-5xl sm:text-7xl md:text-8xl lg:text-[120px] font-boldonse text-brand" style="opacity: 0.15;">TRADING</div>
                    <div class="text-3xl sm:text-5xl md:text-6xl lg:text-[80px] font-boldonse" style="color: rgba(102,102,102,0.08);">PROFESIONAL</div>
                </div>
            </div>

            <!-- ORBIT RING 1 (outer) -->
            <div class="orbit-ring animate-orbit-1 z-[1]"
                 style="width:520px;height:520px;top:50%;left:50%;margin-top:-260px;margin-left:-260px;border:1px dashed rgba(51,125,255,0.08);">
                <span class="orbit-quote counter-spin-1" style="top:0;left:50%;transform:translateX(-50%);">Biasakan mencatat jurnal!</span>
                <span class="orbit-quote counter-spin-1" style="top:50%;right:-10px;transform:translateY(-50%);">Bukan asal klik!</span>
                <span class="orbit-quote counter-spin-1" style="bottom:0;left:50%;transform:translateX(-50%);">Trading = Bisnis</span>
                <span class="orbit-quote counter-spin-1" style="top:50%;left:-10px;transform:translateY(-50%);">Konsisten itu kunci</span>
            </div>

            <!-- ORBIT RING 2 (middle) -->
            <div class="orbit-ring animate-orbit-2 z-[1]"
                 style="width:420px;height:420px;top:50%;left:50%;margin-top:-210px;margin-left:-210px;border:1px dashed rgba(51,125,255,0.06);">
                <span class="orbit-quote counter-spin-2" style="top:-6px;left:30%;">Trader ≠ Judi</span>
                <span class="orbit-quote counter-spin-2" style="bottom:-6px;right:20%;">Pahami supply &amp; demand</span>
                <span class="orbit-quote counter-spin-2" style="top:40%;left:-10px;">Modal kecil bukan halangan</span>
                <span class="orbit-quote counter-spin-2" style="top:35%;right:-10px;">Keputusan tepat = cuan</span>
            </div>

            <!-- FRONT LAYER: Avatar -->
            <div class="relative z-[5] flex flex-col items-center">
                <img src="/hero.png" alt="S5FX Trader"
                     class="w-56 sm:w-64 md:w-72 lg:w-80 drop-shadow-2xl"
                     style="filter: drop-shadow(0 20px 40px rgba(51,125,255,0.15));">
            </div>
        </div>

        <!-- Text & CTA below avatar -->
        <div class="relative z-10 text-center -mt-4">
            <h1 class="font-boldonse text-3xl sm:text-4xl md:text-5xl tracking-tight leading-tight mb-4 fade-up delay-1">
                <span class="text-gray-900">Jurnal Trading</span><br>
                <span class="text-brand">Otomatis &amp; Konsisten</span>
            </h1>
            <p class="text-sm sm:text-base md:text-lg text-gray-500 leading-relaxed max-w-lg mx-auto mb-8 fade-up delay-2">
                Drop screenshot chart, AI membaca harga entry otomatis.
                Catat setiap posisi dari <strong>-$10 / +$30</strong> setiap transaksi.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 fade-up delay-3">
                <a href="/backoffice"
                   class="inline-flex items-center gap-2 px-7 py-3 rounded-full text-sm font-semibold text-white bg-brand shadow-[0_4px_16px_rgba(51,125,255,0.35)] hover:shadow-[0_6px_24px_rgba(51,125,255,0.45)] transition-all duration-200 hover:-translate-y-px">
                    Mulai Jurnal Sekarang
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </a>
                <a href="#fitur"
                   class="inline-flex items-center gap-2 px-7 py-3 rounded-full text-sm font-medium text-gray-600 bg-white border border-gray-200 hover:border-[#337DFF]/30 hover:text-gray-900 shadow-sm transition-all duration-200">
                    Lihat Fitur
                </a>
            </div>
        </div>
    </div>
</section>
