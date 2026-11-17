<?php

if (! function_exists('catalog')) {
    /** قراءة قيمة من config/catalog.php */
    function catalog(string $key, mixed $default = []): mixed
    {
        return config("catalog.$key", $default);
    }
}

if (! function_exists('egp')) {
    /** تنسيق مبلغ بالجنيه المصري */
    function egp(int|float|null $amount): string
    {
        return number_format((float) $amount, 0, '.', ',').' ج.م';
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
