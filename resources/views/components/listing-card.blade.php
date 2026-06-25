@props(['listing'])

@if (($listing['cardType'] ?? 'post') === 'banner')
    <article @isset($listing['id']) id="{{ $listing['id'] }}" @endisset class="classic-listing-card classic-listing-banner-card">
        <a href="{{ $listing['profileHref'] }}" class="classic-listing-banner-image" aria-label="Ver post: {{ $listing['title'] }}">
            <img src="{{ $listing['image'] }}" alt="{{ $listing['title'] }}" loading="lazy">
            @if ($listing['featured'] ?? false)
                <span class="classic-listing-vip">VIP</span>
            @endif
        </a>

        <div class="classic-listing-banner-copy">
            <span>{{ $listing['category'] }}</span>
            <h3 title="{{ $listing['title'] }}">
                <a href="{{ $listing['profileHref'] }}">{{ $listing['title'] }}</a>
            </h3>
            <p>{{ $listing['subtitle'] ?? '' }}</p>
            <dl>
                <div><dt>Ubicación</dt><dd>{{ $listing['city'] }}</dd></div>
                <div><dt>Categoría</dt><dd>{{ $listing['category'] }}</dd></div>
            </dl>
            <a href="{{ $listing['profileHref'] }}">
                <x-heroicon-o-eye class="size-4" />
                Ver anuncio
            </a>
        </div>
    </article>
@else
<article @isset($listing['id']) id="{{ $listing['id'] }}" @endisset class="classic-listing-card">
    <a href="{{ $listing['profileHref'] }}" class="classic-listing-image" aria-label="Ver post: {{ $listing['title'] }}">
        <img src="{{ $listing['image'] }}" alt="{{ $listing['title'] }}" loading="lazy">
        @if ($listing['featured'] ?? false)
            <span class="classic-listing-vip">VIP</span>
        @endif
    </a>

    <div class="classic-listing-copy">
        <h3 class="truncate text-lg" title="{{ $listing['title'] }}">
            <a href="{{ $listing['profileHref'] }}">{{ $listing['title'] }}</a>
        </h3>
        <p
            class="mt-1 h-5 truncate"
            @if (filled($listing['subtitle'] ?? null)) title="{{ $listing['subtitle'] }}" @endif
        >
            {{ $listing['subtitle'] ?? '' }}
        </p>
        <dl>
            <div><dt>Ubicación</dt><dd>{{ $listing['city'] }}</dd></div>
            <div><dt>Categoría</dt><dd>{{ $listing['category'] }}</dd></div>
        </dl>
    </div>

    <div class="classic-listing-actions">
        <a href="{{ $listing['profileHref'] }}">
            <x-heroicon-o-eye class="size-4" />
            Ver anuncio
        </a>
    </div>
</article>
@endif
