<style>
    body { font-family: 'Alexandria', sans-serif; background: #fff; }
    html { scroll-behavior: smooth; }
    .brand-gradient { background-image: linear-gradient(135deg, #a8d9f3 0%, #6bbce9 50%, #3a9ad0 100%); }
    .brand-text { background-image: linear-gradient(135deg, #a8d9f3, #6bbce9 60%, #3a9ad0); -webkit-background-clip: text; background-clip: text; color: transparent; }
    .section-title::after { content: ''; display: block; width: 56px; height: 3px; margin-top: 10px; border-radius: 3px; background-image: linear-gradient(90deg, #6bbce9, #a8d9f3); }
    .hero-bg { background:
        radial-gradient(1200px 500px at 85% -10%, rgba(107,188,233,.28), transparent 60%),
        linear-gradient(160deg, #0b2447 0%, #102d57 60%, #0b2447 100%); }
    .field { width: 100%; border-radius: .75rem; border: 1px solid #cbd5e1; background: #fff; padding: .65rem .9rem; font-size: .9rem; transition: .15s; }
    .field:focus { outline: none; border-color: #6bbce9; box-shadow: 0 0 0 3px rgba(107,188,233,.25); }
    .label { display: block; font-size: .8rem; font-weight: 600; color: #334155; margin-bottom: .35rem; }
    .btn { display: inline-flex; align-items: center; justify-content: center; gap: .5rem; border-radius: .75rem; padding: .65rem 1.25rem; font-weight: 600; font-size: .9rem; transition: .15s; cursor: pointer; }
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

    @media (prefers-reduced-motion: no-preference) {
        .dock-pop { animation: dockpop .45s cubic-bezier(.2,1.4,.4,1) both; }
        @keyframes dockpop { from { opacity:0; transform:scale(.3) translateY(14px); } to { opacity:1; transform:none; } }
        .rise { animation: rise .5s ease-out both; }
        @keyframes rise { from { opacity:0; transform:translateY(14px); } to { opacity:1; transform:none; } }
    }
</style>
