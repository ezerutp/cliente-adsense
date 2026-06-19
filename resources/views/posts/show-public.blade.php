@php
    $fallbackCoverImage = 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=1200&q=80';
    $splitImageUrls = static fn ($values) => collect(is_array($values) ? $values : [$values])
        ->flatMap(fn ($value) => preg_split('/\R/u', (string) $value) ?: [])
        ->map(fn (string $url): string => trim($url))
        ->filter()
        ->values();
    $coverImages = $splitImageUrls($post->cover_image_url);
    $coverImage = $coverImages->first() ?: $fallbackCoverImage;
    $gallery = $coverImages->slice(1)->merge($splitImageUrls($post->gallery_image_urls ?? []))->values();
    $galleryImages = collect([$coverImage])->merge($gallery)->values();
    $contactButtons = collect($contactButtons ?? []);
    $postCards = collect($postCards ?? []);
@endphp

@extends('layouts.public-classic')

@section('title', $post->title.' | '.$siteSettings->brandName())
@section('description', $post->subtitle ?: $post->title)

@section('content')
    <main class="classic-shell classic-post-page">
        <a href="{{ route('categories.public.show', ['category' => $category->slug]) }}" class="classic-back-link">
            &lt; Inicio de búsqueda
        </a>

        <div class="classic-post-layout">
            <article class="classic-post-content">
                <div class="classic-post-meta">
                    <a href="{{ route('categories.public.show', ['category' => $category->slug]) }}">{{ $category->name }}</a>
                    @if ($post->location)
                        <span>/</span>
                        <a href="{{ route('posts.locations.show', ['location' => \Illuminate\Support\Str::slug($post->location)]) }}">
                            {{ $post->location }}
                        </a>
                    @endif
                </div>

                <h1>{{ $post->title }}</h1>

                @if ($post->subtitle)
                    <p class="classic-post-subtitle">{{ $post->subtitle }}</p>
                @endif

                <div class="classic-post-body">
                    {!! \App\Support\PostBodyRenderer::render($post->body) !!}
                </div>

                @if ($post->tags)
                    <div class="classic-post-tags">
                        @foreach ($post->tags as $tag)
                            <a href="{{ route('posts.tags.show', ['tag' => \Illuminate\Support\Str::slug($tag)]) }}">#{{ $tag }}</a>
                        @endforeach
                    </div>
                @endif

                <section class="classic-gallery">
                    <h2><x-heroicon-o-photo class="size-5" /> {{ $galleryImages->count() }} fotos</h2>
                    <div class="classic-gallery-grid">
                        @foreach ($galleryImages as $image)
                            <button
                                type="button"
                                x-data
                                x-on:click="$dispatch('open-post-gallery', { index: {{ $loop->index }} })"
                                aria-label="Ampliar imagen {{ $loop->iteration }} de {{ $post->title }}"
                            >
                                <img src="{{ $image }}" alt="{{ $post->title }}" loading="{{ $loop->first ? 'eager' : 'lazy' }}">
                            </button>
                        @endforeach
                    </div>
                    @if ($gallery->isNotEmpty())
                        <span class="sr-only">Galería index: 1</span>
                    @endif
                </section>
            </article>

            <aside class="classic-post-sidebar">
                <section class="classic-profile-card">
                    <strong>{{ $post->title }}</strong>
                    @if ($post->location)
                        <span>{{ $post->location }}</span>
                    @endif
                </section>

                @foreach ($postCards as $card)
                    <section
                        class="classic-info-card"
                        style="--card-color: {{ $card->color ?: '#f39c12' }}; {{ $card->fill_background ? 'background: var(--card-color); color: white;' : '' }}"
                    >
                        <h2>{{ $card->title }}</h2>
                        <dl>
                            @foreach ($card->fields ?? [] as $field)
                                <div>
                                    <dt>{{ $field['key'] ?? '' }}</dt>
                                    <dd>{{ $field['value'] ?? '' }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </section>
                @endforeach

                @if ($contactButtons->isNotEmpty())
                    <div class="classic-contact-stack">
                        @foreach ($contactButtons as $contact)
                            <a
                                href="{{ $contact['href'] }}"
                                style="background-color: {{ $contact['button_color'] }}"
                                @if ($contact['external']) target="_blank" rel="noopener noreferrer" @endif
                            >
                                <x-dynamic-component :component="$contact['icon']" class="size-6" />
                                {{ $contact['label'] }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </aside>
        </div>

        @if ($contactButtons->isNotEmpty())
            <div class="classic-floating-contacts">
                @foreach ($contactButtons as $contact)
                    <a
                        href="{{ $contact['href'] }}"
                        style="background-color: {{ $contact['button_color'] }}"
                        @if ($contact['external']) target="_blank" rel="noopener noreferrer" @endif
                    >
                        <x-dynamic-component :component="$contact['icon']" class="size-4" />
                        {{ $contact['label'] }}
                    </a>
                @endforeach
            </div>
        @endif

        @if ($relatedPosts->isNotEmpty())
            <section class="classic-related">
                <h2>Más anuncios de {{ $category->name }}</h2>
                <div class="classic-listing-grid">
                    @foreach ($relatedPosts as $relatedPost)
                        <x-listing-card :listing="$relatedPost" />
                    @endforeach
                </div>
            </section>
        @endif
    </main>
@endsection

@push('overlays')
    <x-post-image-gallery :images="$galleryImages" :title="$post->title" />
@endpush
