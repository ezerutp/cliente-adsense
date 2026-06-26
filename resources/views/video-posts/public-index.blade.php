@extends('layouts.public-classic')

@section('title', 'Videos | '.$siteSettings->brandName())
@section('description', 'Galería de videos publicados en '.$siteSettings->brandName())

@section('content')
    <main class="classic-shell py-10">
        <div class="classic-page-heading compact">
            <p>Galería</p>
            <h1>Videos</h1>
            <span>{{ trans_choice('{0} 0 videos publicados|{1} 1 video publicado|[2,*] :count videos publicados', $videos->total(), ['count' => $videos->total()]) }}</span>
        </div>

        <section class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse ($videos as $video)
                <article class="overflow-hidden rounded-md border border-[#d7d7d7] bg-white shadow-sm">
                    <div class="aspect-video bg-[#111]">
                        <iframe
                            src="{{ $video->iframe_src }}"
                            title="{{ $video->title }}"
                            class="h-full w-full"
                            loading="lazy"
                            allowfullscreen
                        ></iframe>
                    </div>
                    <div class="p-4">
                        <h2 class="truncate text-base font-bold text-[#222222]">{{ $video->title }}</h2>
                        @if ($video->description)
                            <p class="mt-2 line-clamp-3 text-sm leading-6 text-[#6B7280]">{{ $video->description }}</p>
                        @endif
                    </div>
                </article>
            @empty
                <p class="classic-empty-state md:col-span-2 lg:col-span-3">Todavía no hay videos publicados.</p>
            @endforelse
        </section>

        @if ($videos->hasPages())
            <div class="classic-pagination mt-8">{{ $videos->links() }}</div>
        @endif
    </main>
@endsection
