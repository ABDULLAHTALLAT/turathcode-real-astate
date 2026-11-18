<footer class="bg-ink text-white/80">
    <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-10 py-20 grid gap-12 md:grid-cols-12">
        <div class="md:col-span-5">
            <img src="{{ asset('images/logo.png') }}" alt="Turath Code" class="h-16 w-auto mb-5">
            <p class="text-base leading-8 max-w-lg">{{ __('«بيتك في ايدك» منصة مصرية بتربط أصحاب العقارات المعطلة بمستأجرين مستعدين يكملوا التشطيب ويستأجروا لمدد طويلة بسعر أقل. كله بوضوح، وبعقد، وعن طريق المنصة.') }}</p>
            <div class="flex flex-wrap gap-x-7 gap-y-2 mt-7 text-sm">
                <a href="https://wa.me/{{ config('site.whatsapp') }}" target="_blank" rel="noopener" class="hover:text-brand underline-offset-4 hover:underline">{{ __('واتساب') }}</a>
                <a href="mailto:{{ config('site.email') }}" class="hover:text-brand underline-offset-4 hover:underline">{{ config('site.email') }}</a>
                <a href="tel:{{ preg_replace('/\s+/', '', config('site.phone')) }}" class="hover:text-brand underline-offset-4 hover:underline" dir="ltr">{{ config('site.phone') }}</a>
            </div>
        </div>
        <div class="md:col-span-2 md:col-start-7">
            <h4 class="text-white font-bold mb-5 text-lg">{{ __('للباحثين') }}</h4>
            <ul class="space-y-3 text-sm">
                <li><a class="hover:text-brand" href="{{ route('properties.index') }}">{{ __('كل العقارات') }}</a></li>
                <li><a class="hover:text-brand" href="{{ route('properties.index', ['stage' => 'plaster']) }}">{{ __('على المحارة') }}</a></li>
                <li><a class="hover:text-brand" href="{{ route('properties.index', ['stage' => 'finished']) }}">{{ __('تشطيب كامل') }}</a></li>
                <li><a class="hover:text-brand" href="{{ route('register', ['role' => 'tenant']) }}">{{ __('سجّل كمستأجر') }}</a></li>
            </ul>
        </div>
        <div class="md:col-span-2">
            <h4 class="text-white font-bold mb-5 text-lg">{{ __('للملّاك') }}</h4>
            <ul class="space-y-3 text-sm">
                <li><a class="hover:text-brand" href="{{ route('register', ['role' => 'owner']) }}">{{ __('اعرض عقارك') }}</a></li>
                <li><a class="hover:text-brand" href="{{ route('how') }}">{{ __('كيف تعمل المنصة') }}</a></li>
                <li><a class="hover:text-brand" href="{{ route('terms') }}">{{ __('نسبة المنصة') }}</a></li>
            </ul>
        </div>
        <div class="md:col-span-2">
            <h4 class="text-white font-bold mb-5 text-lg">{{ __('المنصة') }}</h4>
            <ul class="space-y-3 text-sm">
                <li><a class="hover:text-brand" href="{{ route('about') }}">{{ __('من نحن') }}</a></li>
                <li><a class="hover:text-brand" href="{{ route('contact') }}">{{ __('تواصل معنا') }}</a></li>
                <li><a class="hover:text-brand" href="{{ route('terms') }}">{{ __('الشروط') }}</a></li>
            </ul>
        </div>
    </div>
    <div class="border-t border-white/10 py-6 text-center text-xs text-white/50">
        © {{ date('Y') }} {{ __('بيتك في ايدك') }} — {{ __('تطوير') }} <span class="text-brand font-semibold">Turath Code</span>{{ __('. جميع الحقوق محفوظة.') }}
    </div>
</footer>
