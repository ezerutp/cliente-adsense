@props([
    'group',
    'dense' => false,
])

<article class="overflow-hidden rounded-2xl border border-[#E5E7EB] bg-white shadow-sm">
    <div class="bg-[#222222] px-4 py-3 text-center">
        <h3 class="text-sm font-black uppercase tracking-[0.08em] text-white sm:text-base">{{ $group['title'] }}</h3>
        <p class="mt-1 text-xs font-medium text-white/65">{{ $group['description'] }}</p>
    </div>

    <div @class([
        'grid gap-2 p-3',
        'grid-cols-2 sm:grid-cols-3 xl:grid-cols-6' => $dense,
        'grid-cols-2 sm:grid-cols-3 xl:grid-cols-5' => ! $dense,
    ])>
        @forelse ($group['links'] as $link)
            <x-location-link :link="$link" />
        @empty
            <p class="col-span-full py-5 text-center text-sm text-[#9CA3AF]">Aún no hay ubicaciones disponibles.</p>
        @endforelse
    </div>
</article>
