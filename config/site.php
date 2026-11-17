<?php

return [
    'name' => 'بيتك في ايدك',
    'brand' => 'Turath Code',
    'phone' => env('SITE_PHONE', '+20 100 000 0000'),
    'whatsapp' => env('SITE_WHATSAPP', '201000000000'),
    'email' => env('SITE_EMAIL', 'info@turathcode.com'),

    // نسبة المنصة من كل طرف (تتغير من لوحة الأدمن > الإعدادات)
    'commission_owner_percent' => (float) env('COMMISSION_OWNER_PERCENT', 5),
    'commission_tenant_percent' => (float) env('COMMISSION_TENANT_PERCENT', 5),
];
