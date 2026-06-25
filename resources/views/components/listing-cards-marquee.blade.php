@props([
    'listings' => [],
])

@php
    $items = array_merge($listings, $listings);
@endphp

<div class="relative overflow-hidden">
    <div class="pointer-events-none absolute inset-y-0 left-0 z-10 w-12 bg-gradient-to-r from-[#F8F8F8] to-transparent"></div>
    <div class="pointer-events-none absolute inset-y-0 right-0 z-10 w-12 bg-gradient-to-l from-[#F8F8F8] to-transparent"></div>

    <div class="marquee-track classic-listing-marquee-track flex w-max gap-2">
        @foreach ($items as $listing)
            @php
                $marqueeListing = $listing;
                unset($marqueeListing['id']);
            @endphp

            <div class="classic-listing-marquee-item shrink-0">
                <x-listing-card :listing="$marqueeListing" />
            </div>
        @endforeach
    </div>
</div>
