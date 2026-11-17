<?php

/**
 * كل القوائم الثابتة في المنصة (أنواع العقار، مراحل التشطيب، بنود التشطيب، المحافظات...)
 */
return [

    'property_types' => [
        'apartment' => 'شقة',
        'villa' => 'فيلا',
        'duplex' => 'دوبلكس',
        'building' => 'مبنى كامل',
        'floor' => 'دور كامل',
        'shop' => 'محل تجاري',
        'office' => 'مكتب / إداري',
        'warehouse' => 'مخزن / مصنع',
        'land_building' => 'أرض بها مبنى',
    ],

    // مرحلة التشطيب الحالية (الترتيب = من الأقل للأعلى)
    'finishing_stages' => [
        'concrete' => ['label' => 'هيكل خرساني', 'hint' => 'أعمدة وأسقف فقط', 'level' => 1],
        'red_brick' => ['label' => 'على الطوب', 'hint' => 'الطوب مبني والمحارة لسه', 'level' => 2],
        'plaster' => ['label' => 'على المحارة', 'hint' => 'محارة تامة ومحتاجة باقي التشطيب', 'level' => 3],
        'half_finished' => ['label' => 'نصف تشطيب', 'hint' => 'جزء من التشطيبات ناقص', 'level' => 4],
        'finished' => ['label' => 'تشطيب كامل', 'hint' => 'جاهزة للسكن / للعمل', 'level' => 5],
        'furnished' => ['label' => 'مفروشة', 'hint' => 'جاهزة بالأثاث', 'level' => 6],
    ],

    // بنود التشطيب التفصيلية (تتعبى بحالة كل بند)
    'finishing_items' => [
        'structure' => ['label' => 'الهيكل الخرساني', 'group' => 'الإنشاءات'],
        'bricks' => ['label' => 'مباني الطوب', 'group' => 'الإنشاءات'],
        'plaster' => ['label' => 'المحارة', 'group' => 'الإنشاءات'],
        'electricity' => ['label' => 'التأسيس الكهربائي', 'group' => 'المرافق'],
        'plumbing' => ['label' => 'السباكة والصرف', 'group' => 'المرافق'],
        'gas' => ['label' => 'مواسير الغاز', 'group' => 'المرافق'],
        'meters' => ['label' => 'عدادات (كهرباء/مياه/غاز)', 'group' => 'المرافق'],
        'ceramic' => ['label' => 'السيراميك / البورسلين', 'group' => 'التشطيب'],
        'marble' => ['label' => 'الرخام والعتبات', 'group' => 'التشطيب'],
        'paint' => ['label' => 'النقاشة', 'group' => 'التشطيب'],
        'ceiling' => ['label' => 'الأسقف والجبس', 'group' => 'التشطيب'],
        'doors' => ['label' => 'الأبواب', 'group' => 'النجارة والألوميتال'],
        'windows' => ['label' => 'الشبابيك', 'group' => 'النجارة والألوميتال'],
        'kitchen' => ['label' => 'المطبخ', 'group' => 'النجارة والألوميتال'],
        'bathrooms' => ['label' => 'أطقم الحمامات', 'group' => 'النجارة والألوميتال'],
        'facade' => ['label' => 'الواجهة', 'group' => 'أخرى'],
        'elevator' => ['label' => 'المصعد', 'group' => 'أخرى'],
        'ac' => ['label' => 'تأسيس التكييف', 'group' => 'أخرى'],
    ],

    'item_statuses' => [
        'done' => ['label' => 'متوفر / تم', 'color' => 'emerald'],
        'partial' => ['label' => 'جزئي', 'color' => 'amber'],
        'missing' => ['label' => 'ناقص', 'color' => 'rose'],
        'na' => ['label' => 'غير موجود', 'color' => 'slate'],
    ],

    'utilities' => [
        'electricity' => 'كهرباء',
        'water' => 'مياه',
        'gas' => 'غاز طبيعي',
        'sewage' => 'صرف صحي',
        'elevator' => 'مصعد',
        'parking' => 'جراج / ركن سيارات',
        'roof' => 'سطح',
        'garden' => 'حديقة',
        'security' => 'حارس / أمن',
        'internet' => 'خط إنترنت',
    ],

    // الغرض من الإيجار
    'purposes' => [
        'family' => 'سكن عائلي',
        'singles' => 'سكن أفراد / طلبة',
        'office' => 'مكتب / شركة',
        'clinic' => 'عيادة / مركز طبي',
        'shop' => 'نشاط تجاري',
        'storage' => 'مخزن',
        'factory' => 'ورشة / مصنع',
        'school' => 'حضانة / مدرسة / سنتر',
    ],

    'marital_statuses' => [
        'single' => 'أعزب',
        'married' => 'متزوج',
    ],

    'finishing_by' => [
        'tenant' => 'المستأجر يكمل التشطيب على حسابه',
        'owner' => 'المالك يكمل التشطيب',
        'shared' => 'مشاركة بين الطرفين',
    ],

    'property_statuses' => [
        'pending' => ['label' => 'قيد المراجعة', 'color' => 'amber'],
        'approved' => ['label' => 'منشور', 'color' => 'emerald'],
        'rejected' => ['label' => 'مرفوض', 'color' => 'rose'],
        'rented' => ['label' => 'تم التأجير', 'color' => 'sky'],
        'hidden' => ['label' => 'مخفي', 'color' => 'slate'],
    ],

    'request_statuses' => [
        'pending' => ['label' => 'في انتظار رد المالك', 'color' => 'amber'],
        'negotiating' => ['label' => 'تفاوض', 'color' => 'violet'],
        'accepted' => ['label' => 'تم الاتفاق - في انتظار سداد النسبة', 'color' => 'sky'],
        'completed' => ['label' => 'مكتمل', 'color' => 'emerald'],
        'rejected' => ['label' => 'مرفوض', 'color' => 'rose'],
        'cancelled' => ['label' => 'ملغي', 'color' => 'slate'],
    ],

    'roles' => [
        'admin' => 'أدمن المنصة',
        'owner' => 'مالك عقار',
        'tenant' => 'مستأجر',
    ],

    'governorates' => [
        'القاهرة', 'الجيزة', 'الإسكندرية', 'القليوبية', 'الشرقية', 'الدقهلية', 'الغربية', 'المنوفية',
        'البحيرة', 'كفر الشيخ', 'دمياط', 'بورسعيد', 'الإسماعيلية', 'السويس', 'شمال سيناء', 'جنوب سيناء',
        'الفيوم', 'بني سويف', 'المنيا', 'أسيوط', 'سوهاج', 'قنا', 'الأقصر', 'أسوان', 'الوادي الجديد',
        'مطروح', 'البحر الأحمر',
    ],
];
