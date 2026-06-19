@extends('layouts.public-classic')

@section('title', 'Buscar anuncios | '.$siteSettings->brandName())
@section('description', 'Resultados de búsqueda de publicaciones.')

@section('content')
    <main class="classic-shell classic-results-page">
        <div class="classic-page-heading compact">
            <p>Buscador</p>
            <h1>Resultados de búsqueda</h1>
            <span>Combina ubicación, categoría y palabra clave.</span>
        </div>

        <form action="{{ route('posts.search') }}" method="GET" class="classic-search-panel">
            <select name="location" aria-label="Ubicación">
                <option value="">Todas las ubicaciones</option>
                @foreach ($searchLocations as $location)
                    <option value="{{ $location['value'] }}" @selected($filters['location'] === $location['value'])>
                        {{ $location['label'] }}
                    </option>
                @endforeach
            </select>

            <select name="category" aria-label="Categoría">
                <option value="">Todas las categorías</option>
                @foreach ($searchCategories as $category)
                    <option value="{{ $category['value'] }}" @selected($filters['category'] === $category['value'])>
                        {{ $category['label'] }}
                    </option>
                @endforeach
            </select>

            <input type="search" name="query" value="{{ $filters['query'] }}" placeholder="Palabra clave">
            <button type="submit">
                <x-heroicon-o-magnifying-glass class="size-5" />
                Buscar
            </button>
        </form>

        <p class="classic-result-count">
            {{ trans_choice('{0} Sin resultados|{1} 1 resultado|[2,*] :count resultados', $posts->total(), ['count' => $posts->total()]) }}
        </p>

        <section class="classic-listing-grid">
            @forelse ($posts as $listing)
                <x-listing-card :listing="$listing" />
            @empty
                <p class="classic-empty-state">No encontramos posts con esa combinación de filtros.</p>
            @endforelse
        </section>

        @if ($posts->hasPages())
            <div class="classic-pagination">{{ $posts->links() }}</div>
        @endif
    </main>
@endsection
