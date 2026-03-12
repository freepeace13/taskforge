@extends('layouts.site')

@section('title', 'Pricing')

@section('content')
    <!-- Background -->
    <div class="absolute inset-x-0 top-0 -z-10 overflow-hidden">
        <div class="mx-auto h-[480px] max-w-7xl bg-gradient-to-b from-brand-50 via-white to-white"></div>
        <div class="absolute left-1/2 top-10 h-72 w-72 -translate-x-1/2 rounded-full bg-brand-200/40 blur-3xl"></div>
        <div class="absolute right-20 top-24 h-64 w-64 rounded-full bg-orange-100 blur-3xl"></div>
    </div>

    <x-header />

    <main>
        <section>
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                pricing
            </div>
        </section>
    </main>

    <x-footer />
@endsection
