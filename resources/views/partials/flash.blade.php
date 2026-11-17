@if (session('success') || session('error') || $errors->any())
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 relative z-30">
        @if (session('success'))
            <div data-flash role="status" aria-live="polite" class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm flex items-center gap-2 dark:bg-emerald-950/40 dark:border-emerald-900 dark:text-emerald-200">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div data-flash role="alert" class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm flex items-center gap-2 dark:bg-rose-950/40 dark:border-rose-900 dark:text-rose-200">
                <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
            </div>
        @endif
        @if ($errors->any() && ! session('success'))
            <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm dark:bg-rose-950/40 dark:border-rose-900 dark:text-rose-200">
                <p class="font-semibold mb-1"><i class="bi bi-exclamation-triangle-fill"></i> في مشكلة في البيانات:</p>
                <ul class="list-disc ps-5 space-y-0.5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif
    </div>
@endif
