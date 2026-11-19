@props(['name', 'class' => 'w-5 h-5', 'stroke' => 1.8])
{{-- أيقونات خطية عصرية (نفس لغة Lucide): خط رفيع ونهايات مدوّرة --}}
@php
$paths = [
    'search' => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.6-3.6"/>',
    'pin' => '<path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
    'building' => '<rect x="4" y="3" width="16" height="18" rx="2"/><path d="M9 7h.01M15 7h.01M9 11h.01M15 11h.01M9 15h.01M15 15h.01"/>',
    'layers' => '<path d="m12 2 9 5-9 5-9-5 9-5Z"/><path d="m3 12 9 5 9-5M3 17l9 5 9-5"/>',
    'target' => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1" fill="currentColor"/>',
    'calendar' => '<rect x="3" y="4.5" width="18" height="16.5" rx="3"/><path d="M8 2.5v4M16 2.5v4M3 10h18"/>',
    'sliders' => '<path d="M4 6h10M18 6h2M4 12h4M12 12h8M4 18h12M20 18h0"/><circle cx="16" cy="6" r="2"/><circle cx="10" cy="12" r="2"/><circle cx="18" cy="18" r="2"/>',
    'arrow-right' => '<path d="M5 12h14M13 6l6 6-6 6"/>',
    'arrow-up' => '<path d="M12 19V5M6 11l6-6 6 6"/>',
    'sparkles' => '<path d="m12 3 1.9 5.1L19 10l-5.1 1.9L12 17l-1.9-5.1L5 10l5.1-1.9L12 3Z"/><path d="M19 15v4M17 17h4"/>',
    'x' => '<path d="M18 6 6 18M6 6l12 12"/>',
    'check' => '<path d="m5 12.5 4.5 4.5L19 7.5"/>',
    'sun' => '<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/>',
    'moon' => '<path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8Z"/>',
    'home' => '<path d="m3 11 9-8 9 8"/><path d="M5 10v10h14V10"/><path d="M10 20v-6h4v6"/>',
    'heart' => '<path d="M12 20.5s-7.5-4.6-9.2-9.6C1.7 7.600 3.800 4.500 7 4.500c2 0 3.600 1 5 3 1.400-2 3-3 5-3 3.200 0 5.300 3.100 4.200 6.400C19.500 15.900 12 20.500 12 20.500Z"/>',
    'chevron-down' => '<path d="m6 9 6 6 6-6"/>',
];
@endphp
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ $stroke }}" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}" aria-hidden="true">{!! $paths[$name] ?? '' !!}</svg>
