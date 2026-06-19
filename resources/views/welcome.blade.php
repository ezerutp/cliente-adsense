@php
    $siteSettings = $siteSettings ?? \App\Models\SiteSetting::current();
    $categories = collect($categories ?? []);
    $premiumListings = collect($premiumListings ?? []);
    $searchOptions = \App\Support\PublicSearchOptions::all();
    $locationDirectory = \App\Support\PublicLocationDirectory::make();
@endphp

@extends('layouts.public-classic')

@section('title', $siteSettings->site_title.' | '.$siteSettings->brandName())
@section('description', $siteSettings->site_subtitle)

@section('content')
    <main>
        <section class="classic-home-hero">
            <div class="classic-shell">
                <div class="classic-hero-media">
                    <img
                        src="{{ $siteSettings->cover_image_url ?: \App\Models\SiteSetting::DEFAULTS['cover_image_url'] }}"
                        alt=""
                    >
                    <div class="classic-hero-shade"></div>
                    <div class="classic-hero-copy">
                        <h1>{{ $siteSettings->site_title }}</h1>
                        <p>{{ $siteSettings->site_subtitle }}</p>
                    </div>

                    <form action="{{ route('posts.search') }}" method="GET" class="classic-hero-search">
                        <label class="sr-only" for="home-location">Ubicación</label>
                        <select id="home-location" name="location">
                            <option value="">Ciudad</option>
                            @foreach ($searchOptions['locations'] as $location)
                                <option value="{{ $location['value'] }}">{{ $location['label'] }}</option>
                            @endforeach
                        </select>

                        <label class="sr-only" for="home-category">Categoría</label>
                        <select id="home-category" name="category">
                            <option value="">Categoría</option>
                            @foreach ($searchOptions['categories'] as $category)
                                <option value="{{ $category['value'] }}">{{ $category['label'] }}</option>
                            @endforeach
                        </select>

                        <label class="sr-only" for="home-query">Buscar</label>
                        <input id="home-query" name="query" type="search" placeholder="Buscar anuncio...">
                        <button type="submit" aria-label="Buscar">
                            <x-heroicon-o-magnifying-glass class="size-5" />
                        </button>
                    </form>
                </div>

                <div class="classic-home-heading">
                    <h2>Encuentros calientes en tu ciudad</h2>
                    <p>Anuncios clasificados para adultos</p>
                </div>
            </div>
        </section>

        <section class="classic-shell classic-category-section" aria-label="Categorías">
            <div class="classic-category-grid">
                @forelse ($categories as $category)
                    <x-classic-category-card :category="$category" />
                @empty
                    <p class="classic-empty-state">Todavía no hay categorías activas.</p>
                @endforelse
            </div>
        </section>

        <div class="classic-shell">
            <x-classic-location-board :directory="$locationDirectory" />
        </div>

        <section class="classic-shell classic-intro">
            <h2>
                Bienvenido a <span>{{ $siteSettings->brandName() }}</span>
            </h2>
            <p>{{ $siteSettings->site_subtitle }}</p>
        </section>

        @if ($premiumListings->isNotEmpty())
            <section class="classic-featured-strip">
                <div class="classic-shell">
                    <div class="classic-strip-title">Servicios destacados</div>
                    <div class="classic-featured-scroll">
                        @foreach ($premiumListings->take(8) as $listing)
                            <x-listing-card :listing="$listing" />
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </main>
@endsection
