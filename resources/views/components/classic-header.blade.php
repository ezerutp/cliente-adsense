@props(['siteSettings'])

<header x-data="{ open: false }" class="classic-header">
    <div class="classic-shell flex h-[74px] items-center justify-between gap-4">
        <a href="{{ url('/') }}" class="classic-logo" aria-label="{{ $siteSettings->brandName() }}">
            <img src="{{ asset('logo.webp') }}" alt="{{ $siteSettings->brandName() }}">
            <span class="sr-only">{{ $siteSettings->brand_primary_text }} {{ $siteSettings->brand_accent_text }} {{ $siteSettings->brandInitials() }}</span>
        </a>

        <nav class="classic-desktop-nav" aria-label="Navegación principal">
            <a href="{{ url('/') }}">Inicio</a>
            <a href="{{ route('video-posts.public.index') }}">Videos</a>
            <a href="{{ route('posts.locations.index') }}">Ubicaciones</a>
            <a href="{{ route('posts.tags.index') }}">Etiquetas</a>
            @auth
                <a href="{{ route('dashboard') }}">Panel</a>
            @else
                <a href="{{ route('login') }}">Ingresar</a>
            @endauth
        </nav>

        <div class="ml-auto flex items-center gap-2">
            <a href="{{ route('posts.search') }}" class="classic-header-icon" aria-label="Buscar anuncios">
                <x-heroicon-o-magnifying-glass class="size-4" />
            </a>

            <a href="{{ route('advertise') }}" class="classic-publish-button">
                Publica tu anuncio
            </a>

            <button
                type="button"
                class="classic-menu-button"
                x-on:click="open = ! open"
                x-bind:aria-expanded="open.toString()"
                aria-label="Abrir menú"
            >
                <x-heroicon-o-bars-3 class="size-6" />
            </button>
        </div>
    </div>

    <div x-show="open" x-cloak x-transition class="classic-mobile-menu">
        <div class="classic-shell grid gap-1 py-3">
            <a href="{{ url('/') }}">Inicio</a>
            <a href="{{ route('video-posts.public.index') }}">Videos</a>
            <a href="{{ route('posts.locations.index') }}">Ubicaciones</a>
            <a href="{{ route('posts.tags.index') }}">Etiquetas</a>
            @auth
                <a href="{{ route('dashboard') }}">Panel</a>
            @else
                <a href="{{ route('login') }}">Ingresar</a>
            @endauth
        </div>
    </div>
</header>
