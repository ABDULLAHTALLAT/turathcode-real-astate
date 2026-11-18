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
