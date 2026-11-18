<?php

if (! function_exists('catalog')) {
    /** قراءة قيمة من config/catalog.php */
    function catalog(string $key, mixed $default = []): mixed
    {
        $value = config("catalog.$key", $default);

        // أسماء المحافظات بتتخزن في قاعدة البيانات بالعربي، فبنسيبها زي ما هي وبنترجم العرض بس (__($g))
        if (app()->getLocale() === 'ar' || $key === 'governorates') {
            return $value;
        }

        return catalog_translate($value);
    }
}

if (! function_exists('catalog_translate')) {
    /** يترجم كل النصوص جوه القائمة (القيم بس، المفاتيح تفضل زي ما هي) */
    function catalog_translate(mixed $value): mixed
    {
        if (is_string($value)) {
            return __($value);
        }

        return is_array($value) ? array_map('catalog_translate', $value) : $value;
    }
}

if (! function_exists('egp')) {
    /** تنسيق مبلغ بالجنيه المصري */
    function egp(int|float|null $amount): string
    {
        return number_format((float) $amount, 0, '.', ',').' '.__('ج.م');
    }
}

if (! function_exists('mask_contacts')) {
    /**
     * يخفي أرقام التليفون / الإيميلات / روابط واتساب داخل النصوص الحرة
     * عشان التواصل يفضل جوه المنصة لحد ما الاتفاق يكتمل وتتسدد نسبة المنصة.
     */
    function mask_contacts(?string $text): ?string
    {
        if ($text === null || $text === '') {
            return $text;
        }
        $text = preg_replace('/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}/iu', '[بيانات محجوبة]', $text);
        $text = preg_replace('/(?:wa\.me|whatsapp\.com|t\.me)\S*/iu', '[بيانات محجوبة]', $text);
        $text = preg_replace('/(?:\+?[\d\x{0660}-\x{0669}][\s\-.]?){9,}/u', '[بيانات محجوبة] ', $text);

        return $text;
    }
}
