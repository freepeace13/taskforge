<header class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/80 backdrop-blur">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
      <a href="{{ route('site.home') }}" class="flex items-center gap-3">
        <img
          src="{{ asset('brand_64x64.png') }}"
          alt=""
          width="40"
          height="40"
          class="h-10 w-10 shrink-0 rounded-2xl object-contain shadow-soft"
          aria-hidden="true"
        />
        <div>
          <div class="text-lg font-bold tracking-tight">TaskForge</div>
          <div class="text-xs text-slate-500">Project & task workspace</div>
        </div>
      </a>

      <nav class="hidden items-center gap-8 md:flex">
        <a href="{{ route('site.features') }}" class="text-sm font-medium text-slate-600 transition hover:text-slate-900">Features</a>
        <a href="{{ route('site.preview') }}" class="text-sm font-medium text-slate-600 transition hover:text-slate-900">Preview</a>
        <a href="{{ route('site.pricing') }}" class="text-sm font-medium text-slate-600 transition hover:text-slate-900">Pricing</a>
        <a href="#faq" class="text-sm font-medium text-slate-600 transition hover:text-slate-900">FAQ</a>
      </nav>

      <div class="flex items-center gap-3">
        <a
          href="{{ route('login') }}"
          class="hidden rounded-2xl px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 sm:inline-flex"
        >
          Sign in
        </a>
        <a
          href="#"
          class="inline-flex items-center rounded-2xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-soft transition hover:bg-brand-700"
        >
          Start Free
        </a>
      </div>
    </div>
</header>
