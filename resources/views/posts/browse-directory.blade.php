@extends('layouts.public-classic')

@section('title', $title.' | '.$siteSettings->brandName())
@section('description', $description)

@section('content')
    <main class="classic-shell classic-directory-page">
        <div class="classic-page-heading">
            <p>{{ $eyebrow }}</p>
            <h1>{{ $title }}</h1>
            <span>{{ $description }}</span>
        </div>

        <section class="classic-directory-links">
            @forelse ($groups as $group)
                <a href="{{ $group['href'] }}">
                    <strong>{{ $group['label'] }}</strong>
                    <small>{{ trans_choice('{1} :count anuncio|[2,*] :count anuncios', $group['count'], ['count' => $group['count']]) }}</small>
                </a>
            @empty
                <p class="classic-empty-state">Todavía no hay publicaciones activas para construir este directorio.</p>
            @endforelse
        </section>
    </main>
@endsection
