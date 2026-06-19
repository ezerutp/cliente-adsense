@extends('layouts.public-classic')

@section('title', $label.' | '.$siteSettings->brandName())
@section('description', 'Anuncios publicados en '.$label)

@section('content')
    <main class="classic-shell classic-results-page">
        <a href="{{ $directoryHref }}" class="classic-back-link">&lt; Volver al directorio</a>

        <div class="classic-page-heading compact">
            <p>{{ $eyebrow }}</p>
            <h1>{{ $label }}</h1>
            <span>{{ trans_choice('{1} :count publicación activa|[2,*] :count publicaciones activas', $posts->total(), ['count' => $posts->total()]) }}</span>
        </div>

        <section class="classic-listing-grid">
            @forelse ($posts as $listing)
                <x-listing-card :listing="$listing" />
            @empty
                <p class="classic-empty-state">{{ $emptyMessage }}</p>
            @endforelse
        </section>

        @if ($posts->hasPages())
            <div class="classic-pagination">{{ $posts->links() }}</div>
        @endif
    </main>
@endsection
