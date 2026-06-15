@props([
    'listing',
])

<article @isset($listing['id']) id="{{ $listing['id'] }}" @endisset class="overflow-hidden rounded-3xl border border-[#E5E7EB] bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-[#222222]/10">
    <div class="relative aspect-[4/5] overflow-hidden">
        <img src="{{ $listing['image'] }}" alt="{{ $listing['title'] }}" class="h-full w-full object-cover" loading="lazy">
        <div class="absolute left-3 top-3 flex flex-wrap gap-2">
            @if ($listing['verified'])
                <x-badge variant="verified">Verificado</x-badge>
            @endif
            @if ($listing['featured'])
                <x-badge variant="featured">Destacado</x-badge>
            @endif
        </div>
        @if ($listing['price'])
            <x-badge variant="neutral" class="absolute bottom-3 right-3">{{ $listing['price'] }}</x-badge>
        @endif
    </div>

    <div class="p-5">
        <div class="mb-3 flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.12em] text-[#E91E63]">
            <span>{{ $listing['city'] }}</span>
            <span class="size-1 rounded-full bg-[#E5E7EB]"></span>
            <span>{{ $listing['category'] }}</span>
        </div>
        <h3 class="line-clamp-2 min-h-14 text-lg font-bold leading-7 text-[#222222]">{{ $listing['title'] }}</h3>
        <p class="mt-3 text-sm text-[#6B7280]">{{ $listing['updated'] }}</p>

        <x-button :href="$listing['profileHref']" variant="secondary" class="mt-5 w-full">{{ $listing['profileLabel'] }}</x-button>
    </div>
</article>
