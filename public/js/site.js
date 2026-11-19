(function () {
    var $ = function (s, r) { return (r || document).querySelector(s); };
    var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };

    // رسائل التنبيه: تختفي لوحدها بعد 6 ثواني (الأخطاء بتفضل)
    $$('[data-flash][role=status]').forEach(function (el) {
        setTimeout(function () { el.style.transition = 'opacity .4s'; el.style.opacity = '0'; setTimeout(function () { el.remove(); }, 400); }, 6000);
    });

    // منع الضغط المزدوج على زر الإرسال
    $$('form').forEach(function (f) {
        f.addEventListener('submit', function () {
            var b = f.querySelector('button:not([type=button])');
            if (b && !f.hasAttribute('data-allow-resubmit')) { setTimeout(function () { b.disabled = true; b.classList.add('opacity-60', 'cursor-wait'); }, 0); }
        });
    });

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

    // سلايدر الهيرو
    var slides = $$('.hero-slide'), dots = $$('.hero-dot'), cap = $('#heroCaption'), idxEl = $('#heroIndex');
    if (slides.length) {
        var cur = 0, ms = 6500, timer;
        var go = function (n) {
            slides[cur].classList.remove('is-active'); dots[cur] && dots[cur].classList.remove('is-active');
            cur = (n + slides.length) % slides.length;
            slides[cur].classList.add('is-active');
            if (dots[cur]) { void dots[cur].offsetWidth; dots[cur].classList.add('is-active'); }
            if (cap) cap.textContent = slides[cur].getAttribute('data-caption') || '';
            if (idxEl) idxEl.textContent = ('0' + (cur + 1)).slice(-2);
        };
        var start = function () { clearInterval(timer); if (slides.length > 1) timer = setInterval(function () { go(cur + 1); }, ms); };
        dots.forEach(function (d, i) { d.addEventListener('click', function () { go(i); start(); }); });
        start();
    }

    // ظهور الأقسام عند النزول + عدّاد الأرقام
    var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var countUp = function (el) {
        var to = parseInt(el.getAttribute('data-count'), 10) || 0, t0 = null;
        if (reduce) { el.textContent = to.toLocaleString('en-US'); return; }
        var step = function (t) { t0 = t0 || t; var p = Math.min((t - t0) / 1400, 1); el.textContent = Math.round(to * (1 - Math.pow(1 - p, 3))).toLocaleString('en-US'); if (p < 1) requestAnimationFrame(step); };
        requestAnimationFrame(step);
    };
    if ('IntersectionObserver' in window) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                if (!e.isIntersecting) return;
                e.target.classList.add('is-visible');
                $$('[data-count]', e.target).forEach(countUp);
                if (e.target.hasAttribute('data-count')) countUp(e.target);
                io.unobserve(e.target);
            });
        }, { threshold: .12 });
        $$('.reveal, [data-count]').forEach(function (el) { io.observe(el); });
    } else {
        $$('.reveal').forEach(function (el) { el.classList.add('is-visible'); });
        $$('[data-count]').forEach(countUp);
    }

    // أسهم الصفوف الأفقية
    $$('[data-snap-prev],[data-snap-next]').forEach(function (b) {
        b.addEventListener('click', function () {
            var row = document.getElementById(b.getAttribute('data-snap-prev') || b.getAttribute('data-snap-next'));
            if (!row) return;
            var dir = b.hasAttribute('data-snap-next') ? 1 : -1, rtl = getComputedStyle(row).direction === 'rtl';
            row.scrollBy({ left: dir * (rtl ? -1 : 1) * row.clientWidth * .8, behavior: 'smooth' });
        });
    });
})();

/* ===== شريط السعر + العدّاد الحي في السيرش ===== */
(function () {
    var fmt = function (n) { return Number(n).toLocaleString('en-US'); };
    document.querySelectorAll('[data-price-range]').forEach(function (box) {
        var min = +box.dataset.min, max = +box.dataset.max, step = +box.dataset.step;
        var lo = box.querySelector('[data-pr-lo]'), hi = box.querySelector('[data-pr-hi]');
        var fill = box.querySelector('[data-pr-fill]'), bins = box.querySelectorAll('.pr-bins i');
        var nLo = box.querySelector('[data-pr-num-lo]'), nHi = box.querySelector('[data-pr-num-hi]');
        var tFrom = box.querySelector('[data-pr-from]'), tTo = box.querySelector('[data-pr-to]');
        var oMin = box.querySelector('[data-pr-min-out]'), oMax = box.querySelector('[data-pr-max-out]');
        function paint(who) {
            var a = +lo.value, b = +hi.value;
            if (a > b - step) { if (who === lo) { a = b - step; lo.value = a; } else { b = a + step; hi.value = b; } }
            var pa = (a - min) / (max - min) * 100, pb = (b - min) / (max - min) * 100;
            fill.style.left = pa + '%'; fill.style.width = (pb - pa) + '%';
            bins.forEach(function (el, i) { var mid = (i + .5) / bins.length * 100; el.classList.toggle('on', mid >= pa && mid <= pb); });
            if (tFrom) { tFrom.textContent = fmt(a); tTo.textContent = fmt(b); }
            if (nLo) { nLo.value = a; nHi.value = b; }
            var sum = document.querySelector('[data-pr-summary="' + (box.dataset.id || '') + '"]');
            if (sum) sum.textContent = (a > min || b < max) ? fmt(a) + ' – ' + fmt(b) : sum.dataset.empty;
            oMin.value = a > min ? a : ''; oMax.value = b < max ? b : '';
            lo.style.zIndex = pa > 90 ? 5 : 3;
        }
        lo.addEventListener('input', function () { paint(lo); box.dispatchEvent(new Event('change', { bubbles: true })); });
        hi.addEventListener('input', function () { paint(hi); box.dispatchEvent(new Event('change', { bubbles: true })); });
        [[nLo, lo], [nHi, hi]].forEach(function (pr) {
            if (!pr[0]) return;
            pr[0].addEventListener('change', function () { var v = Math.round((+pr[0].value || 0) / step) * step; pr[1].value = Math.min(max, Math.max(min, v)); paint(pr[1]); box.dispatchEvent(new Event('change', { bubbles: true })); });
        });
        paint();
    });

    // قائمة منسدلة لنطاق السعر + تبويبات الفئة
    document.querySelectorAll('[data-pop]').forEach(function (pop) {
        var btn = pop.querySelector('[data-pop-btn]'), panel = pop.querySelector('[data-pop-panel]');
        function set(open) { panel.classList.toggle('hidden', !open); btn.setAttribute('aria-expanded', open ? 'true' : 'false'); }
        btn.addEventListener('click', function (e) { e.stopPropagation(); set(panel.classList.contains('hidden')); });
        pop.querySelectorAll('[data-pop-close]').forEach(function (b) { b.addEventListener('click', function () { set(false); }); });
        document.addEventListener('click', function (e) { if (!pop.contains(e.target)) set(false); });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') set(false); });
    });
    document.querySelectorAll('[data-tabs]').forEach(function (tabs) {
        var form = tabs.closest('form'), input = form.querySelector('[name=category]');
        tabs.querySelectorAll('[data-tab]').forEach(function (t) {
            t.addEventListener('click', function () {
                input.value = t.dataset.tab;
                tabs.querySelectorAll('[data-tab]').forEach(function (x) { var on = x === t; x.classList.toggle('is-on', on); x.setAttribute('aria-selected', on); });
                input.dispatchEvent(new Event('change', { bubbles: true }));
            });
        });
    });

    // عدّاد النتائج الحي: بيتحدّث وإنت بتغيّر أي فلتر
    document.querySelectorAll('form[data-live-count]').forEach(function (form) {
        var out = form.querySelectorAll('[data-count-out]'), timer, ctl;
        function run() {
            var q = new URLSearchParams(new FormData(form));
            ['_token', 'sort'].forEach(function (k) { q.delete(k); });
            Array.from(q.keys()).forEach(function (k) { if (!q.get(k)) q.delete(k); });
            if (ctl) ctl.abort(); ctl = new AbortController();
            fetch(form.dataset.liveCount + '?' + q, { signal: ctl.signal, headers: { Accept: 'application/json' } })
                .then(function (r) { return r.json(); })
                .then(function (d) { out.forEach(function (o) { o.textContent = d.count; o.classList.remove('count-pop'); void o.offsetWidth; o.classList.add('count-pop'); }); })
                .catch(function () {});
        }
        ['input', 'change'].forEach(function (ev) { form.addEventListener(ev, function () { clearTimeout(timer); timer = setTimeout(run, 220); }); });
        form.querySelectorAll('[data-chip]').forEach(function (c) {
            c.addEventListener('click', function () {
                var f = form.querySelector('[name=' + c.dataset.chip + ']'); if (!f) return;
                f.value = f.value === c.dataset.value ? '' : c.dataset.value;
                form.querySelectorAll('[data-chip="' + c.dataset.chip + '"]').forEach(function (x) { x.classList.toggle('is-on', x.dataset.value === f.value); });
                f.dispatchEvent(new Event('change', { bubbles: true }));
            });
        });
    });
})();
