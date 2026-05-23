@extends('layouts.public')

@section('title', 'Hakkımızda — Varto\'nun Butik Konağı')
@section('description', 'Koğ Suit Otel: Muş Varto\'da modern minimalizm ve geleneksel Anadolu sıcaklığını bir araya getiren beş özenli süitlik butik otel. Vizyonumuz ve hikayemiz.')

@push('head')
@include('partials.schema-breadcrumb', ['items' => [
    ['name' => 'Ana Sayfa', 'url' => route('home')],
    ['name' => 'Hakkımızda', 'url' => route('about')],
]])
@endpush

@section('content')

<section class="py-lg md:py-xl">
    <div class="max-w-[900px] mx-auto px-md">
        <p class="font-display text-xs tracking-[0.2em] uppercase text-accent-dark mb-sm">Hakkımızda</p>
        <h1 class="font-display font-bold text-4xl md:text-6xl tracking-tight text-ink mb-md leading-tight">
            Varto'nun küçük konağı,<br />
            <span class="text-primary">samimi bir kaçış.</span>
        </h1>
        <p class="text-lg text-ink-soft leading-relaxed mb-md">
            Koğ Suit Otel, Muş Varto'nun doğayla iç içe bir köşesinde, beş özenle tasarlanmış süitle
            misafirlerine ev konforu ve butik bir konaklama deneyimi sunmak amacıyla kuruldu.
        </p>
        <p class="text-ink-soft leading-relaxed mb-md">
            Modern minimalizmi Anadolu'nun sıcaklığıyla harmanlayan tasarımımız; doğal tonlar, kaliteli
            tekstiller ve ferah alanlarla her detayda kendini gösterir. Misafirlerimize sade ama özenli
            bir konaklama deneyimi yaşatmak en büyük önceliğimizdir.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-md mt-lg">
            <div class="bg-surface-card rounded-card p-md shadow-soft">
                <p class="font-display font-bold text-3xl text-primary mb-xs">5</p>
                <p class="text-sm text-ink-soft">Özenli Süit</p>
            </div>
            <div class="bg-surface-card rounded-card p-md shadow-soft">
                <p class="font-display font-bold text-3xl text-primary mb-xs">24/7</p>
                <p class="text-sm text-ink-soft">WhatsApp Destek</p>
            </div>
            <div class="bg-surface-card rounded-card p-md shadow-soft">
                <p class="font-display font-bold text-3xl text-primary mb-xs">100%</p>
                <p class="text-sm text-ink-soft">Misafir Memnuniyeti Odağı</p>
            </div>
        </div>

        <div class="mt-xl pt-lg border-t border-border-soft">
            <h2 class="font-display font-bold text-2xl md:text-3xl tracking-tight text-ink mb-md">Vizyonumuz</h2>
            <p class="text-ink-soft leading-relaxed">
                Muş Varto'yu ziyaret eden her misafirimize, bölgenin doğal güzelliklerini deneyimleyebileceği
                konforlu ve huzurlu bir konak hizmeti sunmak. Sıcak bir yatak ve güzel bir manzaranın ötesinde —
                bir dostun evine gelmiş hissini yaşatmak.
            </p>
        </div>

        {{-- Varto Bölge Bilgisi — yerel SEO için bölgesel ilgi sinyali --}}
        <div class="mt-xl pt-lg border-t border-border-soft">
            <h2 class="font-display font-bold text-2xl md:text-3xl tracking-tight text-ink mb-md">Varto Hakkında</h2>
            <p class="text-ink-soft leading-relaxed mb-md">
                Muş iline bağlı bir ilçe olan <strong class="text-ink">Varto</strong>, Doğu Anadolu'nun yüksek
                rakımlı yaylaları ve berrak hava kalitesiyle tanınır. Akdoğan Dağları'nın eteklerinde, Şerafettin
                Dağları'na komşu konumuyla doğa tutkunları ve sakin bir kaçış arayanlar için ideal bir varış noktasıdır.
            </p>
            <p class="text-ink-soft leading-relaxed mb-md">
                Bölgenin temiz yayla havası, geleneksel Anadolu mutfağı ve sıcakkanlı insanlarıyla Varto, küçük
                ama unutulmaz bir keşif deneyimi sunar. <strong class="text-ink">Koğ Suit Otel</strong> olarak
                hem iş seyahatleri hem hafta sonu kaçamakları hem de yayla turları için merkezi ve sakin bir
                konaklama noktasıyız.
            </p>
            <p class="text-ink-soft leading-relaxed">
                Varto, Muş merkezine yaklaşık 60 km mesafededir. Bingöl, Erzurum ve Elazığ gibi çevre illere
                karayolu ile ulaşım kolaydır. Otelimizden bölgenin en güzel manzaralarına ve geleneksel köy
                yaşamına kısa bir sürede ulaşabilirsiniz.
            </p>
        </div>

        <div class="mt-xl pt-lg border-t border-border-soft text-center">
            <p class="font-display text-xs tracking-[0.2em] uppercase text-accent-dark mb-sm">Şimdi Rezervasyon</p>
            <p class="text-ink-soft mb-md max-w-xl mx-auto">
                Varto'da huzurlu bir konaklama için Koğ Suit Otel'i tercih edin. Hızlı talep formuyla bir kaç
                dakikada rezervasyonunuzu oluşturun.
            </p>
            <a href="{{ route('reservations.create') }}"
               class="inline-flex items-center gap-xs bg-primary hover:bg-primary-dark text-white font-display font-semibold tracking-wide px-md py-sm rounded-btn transition-colors">
                Rezervasyon Yap
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>
    </div>
</section>

@endsection
