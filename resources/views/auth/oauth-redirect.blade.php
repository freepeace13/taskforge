@extends('layouts.site')

@section('title', 'Sign in')

@push('head')
  <meta http-equiv="refresh" content="2;url={{ $authUrl }}" />
@endpush

@section('content')
  <div class="flex min-h-screen flex-col items-center justify-center bg-slate-50 px-4 py-12">
    <div class="w-full max-w-md rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-soft">
      <h1 class="text-lg font-semibold text-slate-900">Signing you in</h1>
      <p class="mt-3 text-sm leading-relaxed text-slate-600">
        You will be redirected to the Techysavvy authentication server to complete sign in.
      </p>
      <p class="mt-6 text-sm text-slate-500">If you are not redirected automatically, use the button below.</p>
      <a
        href="{{ $authUrl }}"
        class="mt-4 inline-flex w-full items-center justify-center rounded-xl bg-brand-600 px-4 py-3 text-sm font-semibold text-white shadow-soft transition hover:bg-brand-700"
      >
        Continue to sign in
      </a>
    </div>
  </div>

  <script>
    window.setTimeout(function () {
      window.location.replace(@json($authUrl));
    }, 2000);
  </script>
@endsection
