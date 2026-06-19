@extends('layouts.public-classic')

@section('title', $category->name.' | '.$siteSettings->brandName())
@section('description', $category->description ?: 'Anuncios de '.$category->name)

@section('content')
    <main class="classic-shell classic-results-page">
        <div class="classic-category-banner">
            <img
                src="{{ $category->image_url ?: ($siteSettings->cover_image_url ?: \App\Models\SiteSetting::DEFAULTS['cover_image_url']) }}"
                alt=""
            >
            <div>
                <p>Categoría</p>
                <h1>{{ $category->name }}</h1>
                @if ($category->description)
                    <span>{{ $category->description }}</span>
                @endif
            </div>
        </div>

        <section class="classic-listing-grid">
            @forelse ($posts as $listing)
                <x-listing-card :listing="$listing" />
            @empty
                <p class="classic-empty-state">Todavía no hay posts publicados en esta categoría.</p>
            @endforelse
        </section>

        @if ($posts->hasPages())
            <div class="classic-pagination">{{ $posts->links() }}</div>
        @endif
    </main>
@endsection
