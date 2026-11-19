<div class="no-print fixed bottom-5 end-4 sm:end-6 z-[60] flex flex-col items-end gap-3 pointer-events-none">
    <button type="button" data-theme-toggle aria-label="{{ __('تبديل الوضع الليلي') }}" title="{{ __('تبديل الوضع الليلي') }}"
        class="dock-btn pointer-events-auto w-12 h-12 rounded-2xl bg-white/80 text-ink backdrop-blur-md ring-1 ring-slate-200 shadow-xl shadow-black/10 hover:-translate-y-0.5 hover:ring-brand transition flex items-center justify-center dark:bg-slate-800/80 dark:text-amber-200 dark:ring-slate-600">
        <span class="dock-sun"><x-icon name="sun" class="w-[22px] h-[22px]" /></span>
        <span class="dock-moon"><x-icon name="moon" class="w-[22px] h-[22px]" /></span>
    </button>
    <button type="button" data-to-top aria-label="{{ __('للأعلى') }}" title="{{ __('للأعلى') }}"
        class="dock-btn hidden pointer-events-auto w-12 h-12 rounded-2xl bg-ink text-white shadow-xl shadow-ink/30 items-center justify-center hover:-translate-y-0.5 transition dark:bg-brand dark:text-ink">
        <x-icon name="arrow-up" class="w-[22px] h-[22px]" stroke="2.2" />
    </button>
</div>
