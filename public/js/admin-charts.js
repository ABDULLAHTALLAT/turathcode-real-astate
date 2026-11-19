/* رسوم لوحة الأدمن — Chart.js. الألوان من متغيرات CSS على .viz-root (فاتح/ليلي) */
(function () {
    var dataEl = document.getElementById('adminData');
    if (!dataEl || typeof Chart === 'undefined') return;
    var D = JSON.parse(dataEl.textContent);
    var root = document.querySelector('.viz-root');
    var charts = [];
    var rtl = document.documentElement.dir === 'rtl';
    var nf = new Intl.NumberFormat('en-US');

    function tok() {
        var s = getComputedStyle(root), g = function (n) { return s.getPropertyValue(n).trim(); };
        return { c1: g('--c1'), c2: g('--c2'), c3: g('--c3'), muted: g('--c-muted'), ink: g('--v-ink'), sub: g('--v-sub'), grid: g('--v-grid'), surface: g('--v-surface') };
    }
    function fmtEgp(v) { return nf.format(Math.round(v)) + ' ج'; }
    function short(v) { return v >= 1e6 ? (v / 1e6).toFixed(1) + 'M' : v >= 1e3 ? Math.round(v / 1e3) + 'k' : v; }

    function base(t, opts) {
        opts = opts || {};
        var multi = !!opts.legend;
        return {
            indexAxis: opts.horizontal ? 'y' : 'x',
            responsive: true, maintainAspectRatio: false, animation: { duration: 500 },
            interaction: { mode: opts.horizontal ? 'nearest' : 'index', intersect: false, axis: opts.horizontal ? 'y' : 'x' },
            plugins: {
                legend: { display: multi, position: 'bottom', labels: { color: t.sub, usePointStyle: true, pointStyle: 'rectRounded', boxWidth: 9, boxHeight: 9, padding: 14, font: { family: 'Alexandria', size: 11 } } },
                tooltip: { rtl: rtl, textDirection: rtl ? 'rtl' : 'ltr', backgroundColor: t.surface, titleColor: t.ink, bodyColor: t.sub, borderColor: t.grid, borderWidth: 1, padding: 10, cornerRadius: 8, boxPadding: 4,
                    titleFont: { family: 'Alexandria', weight: '600' }, bodyFont: { family: 'Alexandria' },
                    callbacks: opts.money ? { label: function (c) { return ' ' + c.dataset.label + ': ' + fmtEgp(c.parsed.y); } } : {} }
            },
            scales: opts.noScales ? {} : (opts.horizontal ? {
                x: { beginAtZero: true, grid: { color: t.grid }, border: { display: false }, ticks: { color: t.sub, precision: 0, font: { size: 11 } } },
                y: { position: rtl ? 'right' : 'left', grid: { display: false }, border: { display: false }, ticks: { color: t.sub, font: { family: 'Alexandria', size: 11 } } }
            } : {
                x: { stacked: !!opts.stacked, reverse: rtl, grid: { display: false }, border: { color: t.grid }, ticks: { color: t.sub, maxRotation: 0, autoSkip: true, maxTicksLimit: 10, font: { size: 11 } } },
                y: { stacked: !!opts.stacked, beginAtZero: true, position: rtl ? 'right' : 'left', grid: { color: t.grid }, border: { display: false }, ticks: { color: t.sub, precision: 0, font: { size: 11 }, callback: opts.money ? short : undefined } }
            })
        };
    }
    function line(label, data, color, extra) {
        return Object.assign({ label: label, data: data, borderColor: color, backgroundColor: color, borderWidth: 2, tension: .25, pointRadius: 0, pointHoverRadius: 5, pointHoverBackgroundColor: color, pointHoverBorderColor: '#fff', pointHoverBorderWidth: 2, fill: false }, extra || {});
    }
    function bar(label, data, color, extra) {
        return Object.assign({ label: label, data: data, backgroundColor: color, borderRadius: 4, borderSkipped: false, maxBarThickness: 22 }, extra || {});
    }

    var builders = {
        growth: function (t) { var c = D.charts.growth; return { type: 'line', data: { labels: c.labels, datasets: [line('ملّاك', c.owners, t.c1), line('مستأجرين', c.tenants, t.c2)] }, options: base(t, { legend: true }) }; },
        revenue: function (t) { var c = D.charts.revenue; return { type: 'bar', data: { labels: c.labels, datasets: [bar('محصّل', c.collected, t.c3, { borderColor: t.surface, borderWidth: 2 }), bar('مستحق', c.pending, t.c1, { borderColor: t.surface, borderWidth: 2 })] }, options: base(t, { legend: true, stacked: true, money: true }) }; },
        requests: function (t) { var c = D.charts.requests; return { type: 'line', data: { labels: c.labels, datasets: [line('طلبات جديدة', c.created, t.c1), line('اتفاقات تمت', c.deals, t.c3)] }, options: base(t, { legend: true }) }; },
        funnel: function (t) { var c = D.charts.funnel; return { type: 'bar', data: { labels: c.labels, datasets: [bar('طلبات', c.values, c.muted.map(function (m) { return m ? t.muted : t.c1; }), { maxBarThickness: 18 })] }, options: base(t, { horizontal: true }) }; },
        weekday: function (t) { var c = D.charts.weekday; return { type: 'bar', data: { labels: c.labels, datasets: [bar('طلبات', c.values, t.c1)] }, options: base(t, {}) }; },
        stages: function (t) { var c = D.charts.stages; return { type: 'bar', data: { labels: c.labels, datasets: [bar('عقارات', c.values, t.c1)] }, options: base(t, {}) }; },
        rent: function (t) { var c = D.charts.rent; return { type: 'bar', data: { labels: c.labels, datasets: [bar('إيجار السوق', c.market, t.c2), bar('الإيجار المعروض', c.offered, t.c1)] }, options: base(t, { legend: true, money: true }) }; },
        types: function (t) { var c = D.charts.types; return { type: 'bar', data: { labels: c.labels, datasets: [bar('عقارات', c.values, t.c1)] }, options: base(t, { horizontal: true }) }; },
        govs: function (t) { var c = D.charts.govs; return { type: 'bar', data: { labels: c.labels, datasets: [bar('عقارات', c.values, t.c1)] }, options: base(t, { horizontal: true }) }; },
        status: function (t) { var c = D.charts.status; return { type: 'bar', data: { labels: c.labels, datasets: [bar('عقارات', c.values, t.c1)] }, options: base(t, { horizontal: true }) }; },
        purposes: function (t) { var c = D.charts.purposes; return { type: 'bar', data: { labels: c.labels, datasets: [bar('طلبات', c.values, t.c1)] }, options: base(t, { horizontal: true }) }; },
        users: function (t) { var c = D.charts.users; return { type: 'doughnut', data: { labels: c.labels, datasets: [{ data: c.values, backgroundColor: [t.c1, t.c2], borderColor: t.surface, borderWidth: 3, hoverOffset: 4 }] }, options: Object.assign(base(t, { legend: true, noScales: true }), { cutout: '68%' }) }; }
    };

    function render() {
        var t = tok();
        charts.forEach(function (c) { c.destroy(); }); charts = [];
        Object.keys(builders).forEach(function (k) {
            var el = document.querySelector('canvas[data-chart="' + k + '"]');
            if (el) charts.push(new Chart(el, builders[k](t)));
        });
        document.querySelectorAll('[data-spark]').forEach(function (el) { spark(el, t); });
    }

    /* خطوط صغيرة (SVG) داخل كروت الأرقام */
    function spark(el, t) {
        var d = JSON.parse(el.getAttribute('data-spark') || '[]');
        if (d.length < 2) { el.innerHTML = ''; return; }
        var w = 120, h = 32, max = Math.max.apply(null, d.concat([1])), step = w / (d.length - 1);
        var pts = d.map(function (v, i) { return (rtl ? w - i * step : i * step).toFixed(1) + ',' + (h - 3 - (v / max) * (h - 8)).toFixed(1); });
        el.innerHTML = '<svg viewBox="0 0 ' + w + ' ' + h + '" width="100%" height="' + h + '" preserveAspectRatio="none" aria-hidden="true"><polyline fill="none" stroke="' + t.c1 + '" stroke-width="2" stroke-linejoin="round" stroke-linecap="round" vector-effect="non-scaling-stroke" points="' + pts.join(' ') + '"/></svg>';
    }

    render();
    new MutationObserver(render).observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
})();
