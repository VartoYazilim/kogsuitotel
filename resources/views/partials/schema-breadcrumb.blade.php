{{--
    Schema.org BreadcrumbList — yerel SEO için iç sayfa hiyerarşisi.
    Kullanım:
    @push('head')
        @include('partials.schema-breadcrumb', ['items' => [
            ['name' => 'Ana Sayfa', 'url' => route('home')],
            ['name' => 'Odalar', 'url' => route('rooms.index')],
        ]])
    @endpush
--}}
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => collect($items)->values()->map(fn ($item, $i) => [
        '@type' => 'ListItem',
        'position' => $i + 1,
        'name' => $item['name'],
        'item' => $item['url'],
    ])->all(),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
