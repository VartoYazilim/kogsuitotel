@extends('layouts.public')

@section('title', $page->title . ' — Koğ Suit Otel')
@section('description', $page->title . ' — Koğ Suit Otel hukuki bilgilendirme metni.')

@push('head')
@include('partials.schema-breadcrumb', ['items' => [
    ['name' => 'Ana Sayfa', 'url' => route('home')],
    ['name' => $page->title, 'url' => url()->current()],
]])
@endpush

@section('content')

<section class="py-lg md:py-xl">
    <div class="max-w-[800px] mx-auto px-md">
        <p class="font-display text-xs tracking-[0.2em] uppercase text-accent-dark mb-sm">Yasal</p>
        <h1 class="font-display font-bold text-4xl md:text-5xl tracking-tight text-ink mb-md leading-tight">
            {{ $page->title }}
        </h1>

        @if ($page->last_reviewed_at)
            <p class="text-sm text-ink-mute mb-lg">
                Son güncelleme: {{ $page->last_reviewed_at->translatedFormat('d F Y') }}
            </p>
        @endif

        <div class="prose prose-ink max-w-none text-ink-soft leading-relaxed space-y-md">
            {!! $content !!}
        </div>
    </div>
</section>

@endsection
