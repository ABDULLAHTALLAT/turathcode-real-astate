<style>
    html { font-size: 15px; }
    @media (min-width: 1024px) { html { font-size: 16px; } }
    body { font-family: 'Alexandria', sans-serif; background: #fff; }
    html { scroll-behavior: smooth; }
    .brand-gradient { background-image: linear-gradient(135deg, #a8d9f3 0%, #6bbce9 50%, #3a9ad0 100%); }
    .brand-text { background-image: linear-gradient(135deg, #a8d9f3, #6bbce9 60%, #3a9ad0); -webkit-background-clip: text; background-clip: text; color: transparent; }
    .section-title::after { content: ''; display: block; width: 56px; height: 3px; margin-top: 10px; border-radius: 3px; background-image: linear-gradient(90deg, #6bbce9, #a8d9f3); }
    .hero-bg { background:
        radial-gradient(1200px 500px at 85% -10%, rgba(107,188,233,.28), transparent 60%),
        linear-gradient(160deg, #0b2447 0%, #102d57 60%, #0b2447 100%); }
    .field { min-height: 48px; width: 100%; border-radius: .75rem; border: 1px solid #cbd5e1; background: #fff; padding: .65rem .9rem; font-size: .9rem; transition: .15s; }
    .field:focus { outline: none; border-color: #6bbce9; box-shadow: 0 0 0 3px rgba(107,188,233,.25); }
    .label { display: block; font-size: .8rem; font-weight: 600; color: #334155; margin-bottom: .35rem; }
    .btn { min-height: 44px; display: inline-flex; align-items: center; justify-content: center; gap: .5rem; border-radius: .75rem; padding: .65rem 1.25rem; font-weight: 600; font-size: .9rem; transition: .15s; cursor: pointer; }
    .btn-primary { background: #0b2447; color: #fff; } .btn-primary:hover { background: #1a3f6b; }
    .btn-brand { background-image: linear-gradient(135deg, #a8d9f3, #6bbce9 55%, #3a9ad0); color: #0b2447; } .btn-brand:hover { filter: brightness(1.06); }
    .btn-ghost { border: 1px solid #cbd5e1; color: #0b2447; background: #fff; } .btn-ghost:hover { border-color: #6bbce9; background: #eef7fd; }
    .btn-danger { background: #e11d48; color: #fff; } .btn-danger:hover { background: #be123c; }
    .card { background: #fff; border: 1px solid #e2e8f0; border-radius: 1rem; }

    /* ---------- الوضع الليلي ---------- */
    html.dark { color-scheme: dark; }
    html.dark body { background: #0b1220; color: #e2e8f0; }
    html.dark .bg-white, html.dark .card { background-color: #111a2e; }
    html.dark .bg-slate-50, html.dark .bg-slate-100 { background-color: #0f172a; }
    html.dark .bg-brand-50 { background-color: #10263f; }
    html.dark .text-ink, html.dark .text-slate-900, html.dark .text-slate-800 { color: #f1f5f9; }
    html.dark .text-slate-700, html.dark .text-slate-600 { color: #cbd5e1; }
    html.dark .text-slate-500, html.dark .text-slate-400 { color: #94a3b8; }
    html.dark .text-brand-dark { color: #6bbce9; }
    html.dark .border-slate-100, html.dark .border-slate-200, html.dark .border-slate-300, html.dark .card { border-color: #243049; }
    html.dark .field { background-color: #0f172a; color: #e2e8f0; border-color: #243049; }
    html.dark .label { color: #cbd5e1; }
    html.dark .btn-ghost { background: transparent; color: #e2e8f0; border-color: #334155; }
    html.dark .btn-ghost:hover { background: #1b2740; }
    html.dark .btn-primary { background: #6bbce9; color: #0b2447; }
    html.dark .divide-slate-100 > :not([hidden]) ~ :not([hidden]) { border-color: #243049; }
    html.dark .hover\:bg-slate-50:hover { background-color: #162036; }

    /* ---------- الهيرو والأقسام (تصميم Palmenburg) ---------- */
    .hero-slide { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; opacity:0; transition:opacity 1.8s ease-in-out; transform:scale(1.02); }
    .hero-slide.is-active { opacity:1; }
    .hero-dot { position:relative; overflow:hidden; height:4px; width:28px; border-radius:9999px; background:rgba(255,255,255,.35); border:0; padding:0; cursor:pointer; transition:width .4s; }
    .hero-dot.is-active { width:64px; }
    .hero-dot.is-active::after { content:''; position:absolute; inset:0; background:#6bbce9; transform-origin:right center; animation:dotfill var(--hero-ms,6500ms) linear forwards; }
    [dir=ltr] .hero-dot.is-active::after { transform-origin:left center; }
    @keyframes dotfill { from { transform:scaleX(0); } to { transform:scaleX(1); } }
    .hero-side { background:linear-gradient(to left, rgba(11,36,71,.8), rgba(11,36,71,.3) 75%, transparent); }
    html[dir=ltr] .hero-side { background:linear-gradient(to right, rgba(11,36,71,.8), rgba(11,36,71,.3) 75%, transparent); }
    html[lang=en] .eyebrow { letter-spacing:.12em; text-transform:uppercase; }
    .eyebrow { font-size:.8rem; font-weight:700; letter-spacing:.22em; color:#1f7fb8; }
    .bar { display:block; width:64px; height:4px; border-radius:4px; background-image:linear-gradient(90deg,#6bbce9,#a8d9f3); }
    .snap-row { display:flex; gap:1.5rem; overflow-x:auto; scroll-snap-type:x mandatory; scrollbar-width:none; padding-bottom:.5rem; }
    .snap-row::-webkit-scrollbar { display:none; }
    .snap-row > * { scroll-snap-align:start; flex:0 0 min(86%, 26rem); }
    details > summary { list-style:none; cursor:pointer; } details > summary::-webkit-details-marker { display:none; }
    details[open] .faq-sign { transform:rotate(45deg); }
    @media (prefers-reduced-motion: no-preference) {
        .hero-slide.is-active { animation:kb-in 9s ease-out forwards; }
        .hero-slide:nth-child(2n).is-active { animation-name:kb-out; }
        @keyframes kb-in  { from { transform:scale(1.02); } to { transform:scale(1.16) translate3d(-2%,-1%,0); } }
        @keyframes kb-out { from { transform:scale(1.16) translate3d(2%,1%,0); } to { transform:scale(1.03); } }
        .hero-rise { opacity:0; animation:rise 1s cubic-bezier(.2,.7,.2,1) forwards; animation-delay:var(--d,0s); }
        .reveal { opacity:0; transform:translate3d(0,32px,0); transition:opacity .9s cubic-bezier(.2,.7,.2,1), transform .9s cubic-bezier(.2,.7,.2,1); transition-delay:var(--d,0s); }
        .reveal.is-visible { opacity:1; transform:none; }
    }
    html.dark .eyebrow { color:#6bbce9; }
    @media (prefers-reduced-motion: no-preference) {
        .dock-pop { animation: dockpop .45s cubic-bezier(.2,1.4,.4,1) both; }
        @keyframes dockpop { from { opacity:0; transform:scale(.3) translateY(14px); } to { opacity:1; transform:none; } }
        .rise { animation: rise .5s ease-out both; }
        @keyframes rise { from { opacity:0; transform:translateY(14px); } to { opacity:1; transform:none; } }
    }
    /* ---------- وصولية: حلقة تركيز واضحة لمستخدمي الكيبورد ---------- */
    :focus-visible { outline: 3px solid #6bbce9; outline-offset: 2px; border-radius: .4rem; }
    .skip-link { position: absolute; inset-inline-start: 1rem; top: -4rem; z-index: 100; background: #0b2447; color: #fff; padding: .6rem 1rem; border-radius: .75rem; font-weight: 600; transition: top .15s; }
    .skip-link:focus { top: 1rem; }
    @media (prefers-reduced-motion: reduce) {
        *, *::before, *::after { animation-duration: .01ms !important; animation-iteration-count: 1 !important; transition-duration: .01ms !important; scroll-behavior: auto !important; }
    }
    /* حقول فيها خطأ تبان بوضوح (مش بس باللون الأحمر) */
    .field[aria-invalid=true], .field.is-invalid { border-color: #e11d48; background-image: none; box-shadow: 0 0 0 3px rgba(225,29,72,.15); }
    .field-error { color: #be123c; font-size: .75rem; margin-top: .3rem; display: flex; gap: .35rem; align-items: center; }
    /* كروت العقارات: رفعة خفيفة عند المرور + صورة بتظهر بنعومة */
    .card { transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease; }
    @media (hover: hover) { article.card:hover { transform: translateY(-3px); } }
    img[loading=lazy] { background: linear-gradient(110deg, #eef2f6 8%, #f8fafc 18%, #eef2f6 33%); background-size: 200% 100%; }
    /* مساحة آمنة للجوالات اللي فيها شريط سفلي */
    .safe-bottom { padding-bottom: max(1.25rem, env(safe-area-inset-bottom)); }
    @media print {
        header, footer, .dock, [data-theme-toggle], [data-to-top], .no-print { display: none !important; }
        body { background: #fff !important; color: #000 !important; }
        .card { border: 1px solid #ccc !important; box-shadow: none !important; break-inside: avoid; }
    }
</style>
<style>
    /* ---------- الهيرو: عنوان بسطرين منفصلين + حركة دخول ---------- */
    .hero-title { line-height: 1.5; letter-spacing: 0; }
    .hero-title .ln { display: block; overflow: hidden; padding: .12em 0 .18em; }
    .hero-title .ln > span { display: inline-block; }
    .hero-title .ln + .ln { margin-top: .1em; }
    .brand-shine { background-image: linear-gradient(100deg, #a8d9f3 0%, #6bbce9 35%, #fff 50%, #6bbce9 65%, #a8d9f3 100%); background-size: 220% 100%; -webkit-background-clip: text; background-clip: text; color: transparent; }
    @media (prefers-reduced-motion: no-preference) {
        .hero-title .ln > span { transform: translateY(110%); animation: lineup 1s cubic-bezier(.2,.8,.2,1) forwards; animation-delay: var(--d, 0s); }
        .brand-shine { animation: shine 5s ease-in-out 1.4s infinite; }
        .hero-float { animation: floaty 6s ease-in-out infinite; animation-delay: var(--d, 0s); }
        .scroll-cue i { animation: cue 1.8s ease-in-out infinite; }
        @keyframes lineup { to { transform: none; } }
        @keyframes shine { 0%, 100% { background-position: 100% 0; } 50% { background-position: 0 0; } }
        @keyframes floaty { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-8px); } }
        @keyframes cue { 0% { transform: translateY(-4px); opacity: 0; } 50% { opacity: 1; } 100% { transform: translateY(6px); opacity: 0; } }
        .count-pop { animation: pop .35s cubic-bezier(.2,1.5,.4,1); }
        @keyframes pop { from { transform: scale(.7); opacity: .4; } to { transform: none; opacity: 1; } }
        .dock-sun, .dock-moon { transition: transform .45s cubic-bezier(.2,1.2,.4,1), opacity .3s; }
    }
    .dock-btn { position: relative; }
    .dock-sun, .dock-moon { position: absolute; display: flex; }
    .dock-moon { opacity: 0; transform: rotate(-70deg) scale(.4); }
    html.dark .dock-sun { opacity: 0; transform: rotate(70deg) scale(.4); }
    html.dark .dock-moon { opacity: 1; transform: none; }

    /* ---------- السيرش العصري ---------- */
    .sb { background: rgba(255,255,255,.92); backdrop-filter: blur(18px); -webkit-backdrop-filter: blur(18px); }
    html.dark .sb { background: rgba(15,23,42,.9); }
    .sb-field { position: relative; display: flex; align-items: center; gap: .7rem; padding: .7rem 1rem; border-radius: 1rem; background: #f4f7fb; border: 1px solid transparent; transition: .18s; min-height: 62px; }
    .sb-field:hover { background: #eaf1f9; }
    .sb-field:focus-within { background: #fff; border-color: #6bbce9; box-shadow: 0 0 0 4px rgba(107,188,233,.18); }
    html.dark .sb-field { background: #16213a; } html.dark .sb-field:hover { background: #1b2842; } html.dark .sb-field:focus-within { background: #0f172a; }
    .sb-field > svg { flex: none; color: #1f7fb8; }
    .sb-field small { display: block; font-size: .68rem; font-weight: 700; letter-spacing: .04em; color: #64748b; line-height: 1.2; }
    .sb-field input, .sb-field select { width: 100%; background: transparent; border: 0; outline: 0; padding: 0; font-size: .92rem; font-weight: 600; color: #0b2447; appearance: none; -webkit-appearance: none; cursor: pointer; text-overflow: ellipsis; }
    .sb-field input { cursor: text; } .sb-field input::placeholder { color: #94a3b8; font-weight: 500; }
    html.dark .sb-field input, html.dark .sb-field select { color: #f1f5f9; }
    html.dark .sb-field select option { color: #0b2447; }
    .sb-chip { display: inline-flex; align-items: center; gap: .4rem; padding: .42rem .9rem; border-radius: 9999px; font-size: .78rem; font-weight: 600; border: 1px solid #dbe3ec; color: #334155; background: transparent; transition: .15s; cursor: pointer; }
    .sb-chip:hover { border-color: #6bbce9; color: #1f7fb8; }
    .sb-chip.is-on { background: #0b2447; border-color: #0b2447; color: #fff; }
    html.dark .sb-chip { border-color: #2a3857; color: #cbd5e1; } html.dark .sb-chip.is-on { background: #6bbce9; color: #0b2447; border-color: #6bbce9; }
    .sb-go { display: flex; align-items: center; justify-content: center; gap: .6rem; border-radius: 1rem; min-height: 62px; padding: 0 1.6rem; font-weight: 800; font-size: .95rem; color: #fff; background: linear-gradient(135deg, #0b2447, #1a3f6b); box-shadow: 0 12px 28px -10px rgba(11,36,71,.6); transition: .2s; width: 100%; }
    .sb-go:hover { transform: translateY(-2px); box-shadow: 0 18px 32px -10px rgba(11,36,71,.65); }
    .sb-go b { background: #6bbce9; color: #0b2447; border-radius: 9999px; padding: .1rem .65rem; font-size: .85rem; min-width: 2rem; text-align: center; }
    [dir=rtl] .sb-go svg { transform: scaleX(-1); }

    /* ---------- شريط السعر ---------- */
    .pr { padding: .15rem 0; }
    .pr-bins { display: flex; align-items: flex-end; gap: 3px; height: 46px; }
    .pr-bins i { flex: 1; border-radius: 3px 3px 0 0; background: #dbe5ef; transition: background .2s; }
    .pr-bins i.on { background: #6bbce9; }
    html.dark .pr-bins i { background: #243049; } html.dark .pr-bins i.on { background: #3a9ad0; }
    .pr-track { position: relative; height: 5px; border-radius: 5px; background: #dbe5ef; margin-top: 4px; }
    html.dark .pr-track { background: #243049; }
    .pr-fill { position: absolute; top: 0; bottom: 0; border-radius: 5px; background: linear-gradient(90deg, #3a9ad0, #6bbce9); }
    .pr-avg { position: absolute; top: -9px; width: 2px; height: 23px; margin-left: -1px; background: #eb6834; border-radius: 2px; pointer-events: none; }
    .pr-avg::before { content: ''; position: absolute; top: -3px; left: -2px; width: 6px; height: 6px; border-radius: 50%; background: #eb6834; }
    .pr-in { position: absolute; left: 0; right: 0; bottom: -7px; width: 100%; height: 18px; margin: 0; background: none; pointer-events: none; -webkit-appearance: none; appearance: none; }
    .pr-in::-webkit-slider-thumb { -webkit-appearance: none; pointer-events: auto; width: 22px; height: 22px; border-radius: 50%; background: #fff; border: 2px solid #1f7fb8; box-shadow: 0 3px 10px rgba(11,36,71,.28); cursor: grab; transition: transform .15s; }
    .pr-in::-webkit-slider-thumb:active { transform: scale(1.15); cursor: grabbing; }
    .pr-in::-moz-range-thumb { pointer-events: auto; width: 18px; height: 18px; border-radius: 50%; background: #fff; border: 2px solid #1f7fb8; box-shadow: 0 3px 10px rgba(11,36,71,.28); cursor: grab; }
    .pr-in:focus-visible::-webkit-slider-thumb { outline: 3px solid #6bbce9; outline-offset: 2px; }
    .pr-in::-webkit-slider-runnable-track { background: none; } .pr-in::-moz-range-track { background: none; }
</style>
<style>
    /* ---------- أسهم الصفحات ---------- */
    .pg-arrow { width: 52px; height: 52px; border-radius: 9999px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid #d5deea; background: #fff; color: #0b2447; box-shadow: 0 6px 16px -8px rgba(11,36,71,.35); transition: .18s; }
    a.pg-arrow:hover { background: #0b2447; color: #fff; border-color: #0b2447; transform: translateY(-2px); }
    .pg-arrow.is-off { opacity: .38; box-shadow: none; cursor: not-allowed; }
    html.dark .pg-arrow { background: #111a2e; border-color: #2a3857; color: #e2e8f0; }
    html.dark a.pg-arrow:hover { background: #6bbce9; color: #0b2447; }
    /* في الإنجليزي السهم بيتعكس عشان "السابق" يبقى شمال */
    [dir=ltr] .pg-arrow svg { transform: scaleX(-1); }
</style>
<style>
    .sb-group { border: 0; padding: 0; margin: 0; min-width: 0; }
    .sb-step { display: flex; align-items: center; gap: .55rem; font-size: .78rem; font-weight: 800; color: #1f7fb8; letter-spacing: .02em; margin-bottom: .6rem; padding: 0; }
    .sb-step b { width: 22px; height: 22px; border-radius: 50%; background: #0b2447; color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: .7rem; }
    html.dark .sb-step b { background: #6bbce9; color: #0b2447; } html.dark .sb-step { color: #6bbce9; }
</style>
