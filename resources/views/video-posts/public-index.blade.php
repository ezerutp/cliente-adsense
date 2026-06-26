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
                <article
                    class="overflow-hidden rounded-md border border-[#d7d7d7] bg-white shadow-sm"
                    x-data
                >
                    <button
                        type="button"
                        class="group relative block aspect-video w-full overflow-hidden bg-[#111] text-left"
                        x-on:click="$dispatch('open-modal', 'watch-video-{{ $video->id }}')"
                        aria-label="Reproducir {{ $video->title }}"
                    >
                        @if ($video->thumbnail_url)
                            <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}" class="h-full w-full object-cover transition duration-200 group-hover:scale-105">
                        @else
                            <div class="grid h-full w-full place-items-center bg-[#222] text-4xl font-black tracking-widest text-white">XV</div>
                        @endif
                        <span class="absolute inset-0 bg-black/10 transition group-hover:bg-black/25"></span>
                        <span class="absolute left-1/2 top-1/2 grid size-14 -translate-x-1/2 -translate-y-1/2 place-items-center rounded-full bg-white/90 text-[#E91E63] shadow-lg transition group-hover:scale-105">
                            <x-heroicon-s-play class="ml-0.5 size-7" aria-hidden="true" />
                        </span>
                    </button>
                    <div class="p-4">
                        <h2 class="truncate text-base font-bold text-[#222222]">{{ $video->title }}</h2>
                        @if ($video->description)
                            <p class="mt-2 line-clamp-3 text-sm leading-6 text-[#6B7280]">{{ $video->description }}</p>
                        @endif
                    </div>
                </article>

                <x-modal name="watch-video-{{ $video->id }}" maxWidth="4xl">
                    <div class="bg-black">
                        <div class="flex items-center justify-between gap-4 border-b border-white/10 px-4 py-3 text-white">
                            <h2 class="truncate text-sm font-semibold">{{ $video->title }}</h2>
                            <button
                                type="button"
                                class="grid size-8 shrink-0 place-items-center rounded-md text-white/80 transition hover:bg-white/10 hover:text-white"
                                x-on:click="$dispatch('close')"
                                aria-label="Cerrar video"
                            >
                                <x-heroicon-o-x-mark class="size-5" aria-hidden="true" />
                            </button>
                        </div>
                        <div class="aspect-video">
                            <iframe
                                x-bind:src="show ? @js($video->iframe_src) : ''"
                                title="{{ $video->title }}"
                                class="h-full w-full"
                                loading="lazy"
                                allowfullscreen
                            ></iframe>
                        </div>
                    </div>
                </x-modal>
            @empty
                <p class="classic-empty-state md:col-span-2 lg:col-span-3">Todavía no hay videos publicados.</p>
            @endforelse
        </section>

        @if ($videos->hasPages())
            <div class="classic-pagination mt-8">{{ $videos->links() }}</div>
        @endif
    </main>
@endsection
