<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'بيتك في ايدك') — {{ config('site.name') }}</title>
    <meta name="description" content="@yield('description', 'منصة بيتك في ايدك: اعرض أو استأجر المباني والشقق المعطلة في مصر بإيجار مخفض لمدة طويلة، مع تفاصيل التشطيب كاملة.')">
    <meta name="theme-color" content="#0b2447">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script>
        (function () { try { if (localStorage.getItem('bf-theme') === 'dark') document.documentElement.classList.add('dark'); } catch (e) {} })();
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: { extend: {
                colors: {
                    ink: '#0b2447',
                    brand: { DEFAULT: '#6bbce9', light: '#a8d9f3', dark: '#1f7fb8', 50: '#eef7fd', 100: '#d9eefa' },
                    navy: { DEFAULT: '#0b2447', 600: '#1a3f6b', 700: '#122c4f', 900: '#0b2447' },
                },
                fontFamily: { sans: ['Alexandria', 'ui-sans-serif', 'system-ui'] },
            } },
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @include('partials.styles')
    @stack('head')
</head>
<body class="bg-white text-slate-800 antialiased font-sans">
    <a href="#main" class="skip-link">تخطّي إلى المحتوى</a>

    @include('partials.header')

    <main>
        @include('partials.flash')
        @yield('content')
    </main>

    @include('partials.footer')
    @include('partials.dock')

    <script src="{{ asset('js/site.js') }}"></script>
    @stack('scripts')
</body>
</html>
