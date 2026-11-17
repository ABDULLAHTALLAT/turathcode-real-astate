<footer class="bg-ink text-white/80 mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 grid gap-10 md:grid-cols-4">
        <div class="md:col-span-2">
            <img src="{{ asset('images/logo.png') }}" alt="Turath Code" class="h-16 w-auto mb-4">
            <p class="text-sm leading-7 max-w-md">«بيتك في ايدك» منصة مصرية بتربط أصحاب العقارات المعطلة بمستأجرين مستعدين يكملوا التشطيب ويستأجروا لمدد طويلة بسعر أقل. كله بوضوح، وبعقد، وعن طريق المنصة.</p>
            <div class="flex gap-3 mt-5">
                <a href="https://wa.me/{{ config('site.whatsapp') }}" target="_blank" rel="noopener" class="w-10 h-10 rounded-full bg-white/10 hover:bg-brand hover:text-ink flex items-center justify-center transition" aria-label="واتساب"><i class="bi bi-whatsapp"></i></a>
                <a href="mailto:{{ config('site.email') }}" class="w-10 h-10 rounded-full bg-white/10 hover:bg-brand hover:text-ink flex items-center justify-center transition" aria-label="إيميل"><i class="bi bi-envelope"></i></a>
                <a href="tel:{{ preg_replace('/\s+/', '', config('site.phone')) }}" class="w-10 h-10 rounded-full bg-white/10 hover:bg-brand hover:text-ink flex items-center justify-center transition" aria-label="اتصال"><i class="bi bi-telephone"></i></a>
            </div>
        </div>
        <div>
            <h4 class="text-white font-bold mb-4">روابط سريعة</h4>
            <ul class="space-y-2.5 text-sm">
                <li><a class="hover:text-brand" href="{{ route('properties.index') }}">كل العقارات</a></li>
                <li><a class="hover:text-brand" href="{{ route('register', ['role' => 'owner']) }}">أنا مالك عقار</a></li>
                <li><a class="hover:text-brand" href="{{ route('register', ['role' => 'tenant']) }}">أنا باحث عن عقار</a></li>
                <li><a class="hover:text-brand" href="{{ route('how') }}">كيف تعمل المنصة</a></li>
            </ul>
        </div>
        <div>
            <h4 class="text-white font-bold mb-4">المنصة</h4>
            <ul class="space-y-2.5 text-sm">
                <li><a class="hover:text-brand" href="{{ route('about') }}">من نحن</a></li>
                <li><a class="hover:text-brand" href="{{ route('terms') }}">الشروط ونسبة المنصة</a></li>
                <li><a class="hover:text-brand" href="{{ route('contact') }}">تواصل معنا</a></li>
            </ul>
        </div>
    </div>
    <div class="border-t border-white/10 py-5 text-center text-xs text-white/50">
        © {{ date('Y') }} بيتك في ايدك — تطوير <span class="text-brand font-semibold">Turath Code</span>. جميع الحقوق محفوظة.
    </div>
</footer>
