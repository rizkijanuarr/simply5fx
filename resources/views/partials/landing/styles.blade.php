<style>
    @font-face {
        font-family: 'Boldonse';
        src: url('/boldense/Boldonse-Regular.ttf') format('truetype');
        font-weight: 400;
        font-style: normal;
        font-display: swap;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Inter', system-ui, sans-serif; -webkit-font-smoothing: antialiased; background: #FAFAFA; color: #1a1a1a; overflow-x: hidden; }
    .font-boldonse { font-family: 'Boldonse', sans-serif; }

    /* Brand gradient */
    .text-brand { background: linear-gradient(180deg, #337DFF 0%, #666666 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
    .bg-brand { background: linear-gradient(135deg, #337DFF 0%, #5B9AFF 100%); }
    .border-brand { border-color: #337DFF; }

    /* Rotating quotes orbit */
    .orbit-ring {
        position: absolute;
        border-radius: 50%;
        pointer-events: none;
    }
    .orbit-quote {
        position: absolute;
        white-space: nowrap;
        font-size: 11px;
        font-weight: 500;
        color: rgba(51,125,255,0.35);
        letter-spacing: 0.02em;
        pointer-events: none;
        transition: color 0.3s ease;
    }
    .orbit-ring:hover .orbit-quote { color: rgba(51,125,255,0.6); }

    @keyframes spin-slow    { from { transform: rotate(0deg); }   to { transform: rotate(360deg); } }
    @keyframes spin-reverse { from { transform: rotate(360deg); } to { transform: rotate(0deg); } }
    @keyframes spin-medium  { from { transform: rotate(0deg); }   to { transform: rotate(360deg); } }
    .animate-orbit-1 { animation: spin-slow    60s linear infinite; }
    .animate-orbit-2 { animation: spin-reverse 45s linear infinite; }
    .animate-orbit-3 { animation: spin-medium  75s linear infinite; }

    /* Counter-rotate text so it stays readable */
    .counter-spin-1 { animation: spin-reverse 60s linear infinite; }
    .counter-spin-2 { animation: spin-slow    45s linear infinite; }
    .counter-spin-3 { animation: spin-reverse 75s linear infinite; }

    /* Fade in */
    .fade-up { opacity: 0; transform: translateY(30px); transition: opacity 0.7s cubic-bezier(0.16,1,0.3,1), transform 0.7s cubic-bezier(0.16,1,0.3,1); }
    .fade-up.visible { opacity: 1; transform: translateY(0); }
    .delay-1 { transition-delay: 0.1s; }
    .delay-2 { transition-delay: 0.2s; }
    .delay-3 { transition-delay: 0.3s; }
    .delay-4 { transition-delay: 0.4s; }

    /* Gallery tab */
    .gallery-tab {
        padding: 8px 18px; border-radius: 100px; font-size: 13px; font-weight: 500;
        color: #888; background: transparent; border: 1px solid #e5e5e5;
        cursor: pointer; transition: all 0.2s ease; display: inline-flex; align-items: center; gap: 6px;
    }
    .gallery-tab:hover { color: #337DFF; border-color: rgba(51,125,255,0.3); }
    .gallery-tab.active { color: #337DFF; background: rgba(51,125,255,0.06); border-color: rgba(51,125,255,0.3); font-weight: 600; }

    /* Scrollbar */
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #ddd; border-radius: 3px; }

    /* Top bar ticker */
    #top-bar-text { transition: opacity 0.25s ease, transform 0.25s cubic-bezier(0.16,1,0.3,1); }

    /* Hero text behind avatar */
    .hero-text-bg {
        font-family: 'Boldonse', sans-serif;
        line-height: 0.9;
        letter-spacing: -0.02em;
        user-select: none;
    }

    @media (prefers-reduced-motion: reduce) {
        .animate-orbit-1, .animate-orbit-2, .animate-orbit-3,
        .counter-spin-1, .counter-spin-2, .counter-spin-3 { animation: none; }
    }
</style>
