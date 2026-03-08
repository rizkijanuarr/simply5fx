<script>
    // ── Mobile menu ──────────────────────────────────────────────────────────
    const hamburger  = document.getElementById('hamburger');
    const mobileMenu = document.getElementById('mobile-menu');
    const iconMenu   = document.getElementById('icon-menu');
    const iconClose  = document.getElementById('icon-close');

    hamburger.addEventListener('click', () => {
        const isOpen = mobileMenu.style.maxHeight !== '0px' && mobileMenu.style.maxHeight !== '';
        if (isOpen) {
            mobileMenu.style.maxHeight = '0px';
            mobileMenu.style.opacity   = '0';
            iconMenu.classList.remove('hidden');
            iconClose.classList.add('hidden');
        } else {
            mobileMenu.style.maxHeight = '300px';
            mobileMenu.style.opacity   = '1';
            iconMenu.classList.add('hidden');
            iconClose.classList.remove('hidden');
        }
    });

    mobileMenu.querySelectorAll('a').forEach(link => link.addEventListener('click', () => {
        mobileMenu.style.maxHeight = '0px';
        mobileMenu.style.opacity   = '0';
        iconMenu.classList.remove('hidden');
        iconClose.classList.add('hidden');
    }));

    // ── Scroll reveal ────────────────────────────────────────────────────────
    const revealObs = new IntersectionObserver(entries => {
        entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); });
    }, { threshold: 0.15 });
    document.querySelectorAll('.fade-up').forEach(el => revealObs.observe(el));

    // ── Smooth scroll ────────────────────────────────────────────────────────
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', e => {
            e.preventDefault();
            const target = document.querySelector(a.getAttribute('href'));
            if (target) target.scrollIntoView({ behavior: 'smooth' });
        });
    });

    // ── Gallery ──────────────────────────────────────────────────────────────
    const gData = [
        { img: '/01-dashboard.png',                  label: 'Dashboard Komprehensif',  desc: 'Pantau performa trading real-time dengan grafik profit dan win rate.' },
        { img: '/03-create-transactions.png',         label: 'AI Auto-fill Harga',     desc: 'Drop screenshot, Gemini AI mengisi harga entry, SL, dan TP otomatis.' },
        { img: '/02-transactions.png',                label: 'Riwayat Transaksi',       desc: 'Semua posisi tercatat rapi. Filter market, posisi, dan hasil.' },
        { img: '/04-edit-transactions.png',           label: 'Tutup Posisi',            desc: 'Upload screenshot after, isi HIT, transaksi terkunci otomatis.' },
        { img: '/05-proffesional-ai-consistency.png', label: 'AI Konsisten',            desc: 'Alasan analisa dalam Bahasa Indonesia untuk setiap transaksi.' },
    ];

    const gImg  = document.getElementById('g-img');
    const gLabel = document.getElementById('g-label-text');
    const gDesc  = document.getElementById('g-desc');
    const gTabs  = document.querySelectorAll('.gallery-tab');
    const gDots  = document.querySelectorAll('.g-dot');

    function switchGallery(i) {
        if (!gImg) return;
        gImg.style.opacity = '0';
        setTimeout(() => {
            gImg.src             = gData[i].img;
            gLabel.textContent   = gData[i].label;
            gDesc.textContent    = gData[i].desc;
            gImg.style.opacity   = '1';
        }, 200);
        gTabs.forEach((t, j) => t.classList.toggle('active', j === i));
        gDots.forEach((d, j) => {
            d.style.width      = j === i ? '20px' : '6px';
            d.style.background = j === i ? '#337DFF' : 'rgba(0,0,0,0.12)';
        });
    }

    gTabs.forEach((t, i) => t.addEventListener('click', () => switchGallery(i)));
    gDots.forEach((d, i) => d.addEventListener('click', () => switchGallery(i)));

    // ── Top bar ticker ───────────────────────────────────────────────────────
    const barQuotes = [
        'Disaat semua Trader rugi tanpa jurnal — <span class="text-blue-400 font-semibold">s5fx solusinya!</span>',
        'Biasakan entry dengan mencatat jurnal — <span class="text-blue-400 font-semibold">kebiasaan kecil, hasil besar.</span>',
        'Jadikan Trading bukan sekadar freelance — <span class="text-blue-400 font-semibold">melainkan Bisnis!</span>',
        'Perlakukan Trading sebagai Profesi — <span class="text-blue-400 font-semibold">bukan asal klik!</span>',
        'Konsisten itu kunci — <span class="text-blue-400 font-semibold">di Trader, hasilnya pasti datang.</span>',
        'Modal kecil bukan halangan — <span class="text-blue-400 font-semibold">pahami struktur market.</span>',
        'Kalau rugi jangan bilang judi — <span class="text-blue-400 font-semibold">yang salah: tanpa analisa!</span>',
    ];

    let barIdx = 0;
    const barText = document.getElementById('top-bar-text');

    if (barText) {
        setInterval(() => {
            barIdx = (barIdx + 1) % barQuotes.length;
            barText.style.opacity   = '0';
            barText.style.transform = 'translateY(6px)';
            setTimeout(() => {
                barText.innerHTML       = barQuotes[barIdx];
                barText.style.opacity   = '1';
                barText.style.transform = 'translateY(0)';
            }, 250);
        }, 3000);
    }
</script>
