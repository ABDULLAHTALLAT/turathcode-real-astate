(function () {
    var $ = function (s, r) { return (r || document).querySelector(s); };
    var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };

    // الوضع الليلي
    $$('[data-theme-toggle]').forEach(function (b) {
        b.addEventListener('click', function () {
            var dark = document.documentElement.classList.toggle('dark');
            try { localStorage.setItem('bf-theme', dark ? 'dark' : 'light'); } catch (e) {}
        });
    });

    // الهيدر الثابت + زر الرجوع للأعلى
    var sticky = $('#stickyHeader'), top = $('[data-to-top]');
    function onScroll() {
        var y = window.scrollY || 0;
        if (sticky) { sticky.classList.toggle('-translate-y-full', y < 240); sticky.setAttribute('aria-hidden', y < 240 ? 'true' : 'false'); }
        if (top) { top.classList.toggle('hidden', y < 500); top.classList.toggle('flex', y >= 500); if (y >= 500) top.classList.add('dock-pop'); }
    }
    window.addEventListener('scroll', onScroll, { passive: true }); onScroll();
    if (top) top.addEventListener('click', function () { window.scrollTo({ top: 0, behavior: 'smooth' }); });

    // القائمة الجانبية
    var drawer = $('[data-drawer]'), overlay = $('[data-drawer-overlay]');
    function setDrawer(open) {
        if (!drawer) return;
        drawer.classList.toggle('translate-x-full', !open); drawer.classList.toggle('rtl:-translate-x-full', !open);
        drawer.classList.toggle('invisible', !open);
        overlay.classList.toggle('opacity-0', !open); overlay.classList.toggle('pointer-events-none', !open);
        document.body.style.overflow = open ? 'hidden' : '';
    }
    $$('[data-menu-toggle]').forEach(function (b) { b.addEventListener('click', function () { setDrawer(true); }); });
    $$('[data-drawer-close]').forEach(function (b) { b.addEventListener('click', function () { setDrawer(false); }); });
    if (overlay) overlay.addEventListener('click', function () { setDrawer(false); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { setDrawer(false); closeLb(); } });

    // معرض الصور (Lightbox)
    var lb = $('[data-lightbox]'), lbImg = lb && $('img', lb), lbList = [], lbIdx = 0;
    function showLb(i) { lbIdx = (i + lbList.length) % lbList.length; lbImg.src = lbList[lbIdx]; $('[data-lb-count]', lb).textContent = (lbIdx + 1) + ' / ' + lbList.length; }
    function closeLb() { if (lb) { lb.classList.add('hidden'); lb.classList.remove('flex'); } }
    if (lb) {
        lbList = $$('[data-lb-src]').map(function (e) { return e.getAttribute('data-lb-src'); });
        $$('[data-lb-src]').forEach(function (e, i) { e.addEventListener('click', function () { lb.classList.remove('hidden'); lb.classList.add('flex'); showLb(i); }); });
        $('[data-lb-close]', lb).addEventListener('click', closeLb);
        $('[data-lb-next]', lb).addEventListener('click', function () { showLb(lbIdx + 1); });
        $('[data-lb-prev]', lb).addEventListener('click', function () { showLb(lbIdx - 1); });
        lb.addEventListener('click', function (e) { if (e.target === lb) closeLb(); });
    }

    // حاسبة الإيجار في صفحة العقار / نموذج الطلب
    var calc = $('[data-rent-calc]');
    if (calc) {
        var rent = $('[name=proposed_rent]', calc), years = $('[name=lease_years]', calc), out = $('[data-calc-out]', calc);
        var rate = [parseFloat(calc.dataset.ownerRate || 0), parseFloat(calc.dataset.tenantRate || 0)];
        var fmt = function (n) { return Math.round(n).toLocaleString('ar-EG') + ' ج.م'; };
        var upd = function () {
            var total = (parseInt(rent.value) || 0) * 12 * (parseInt(years.value) || 0);
            out.innerHTML = 'إجمالي العقد: <b>' + fmt(total) + '</b> · نسبة المنصة عليك: <b>' + fmt(total * rate[1] / 100) + '</b>';
        };
        rent.addEventListener('input', upd); years.addEventListener('input', upd); upd();
    }
})();
