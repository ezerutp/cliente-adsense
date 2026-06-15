@props([
    'logo',
    'logoHref',
    'links' => [],
    'loginHref' => '#',
    'loginLabel',
    'publishHref' => '#',
    'publishLabel',
])

@php
    $logoWords = explode(' ', $logo, 2);
    $logoFirstWord = $logoWords[0] ?? $logo;
    $logoRest = $logoWords[1] ?? '';
@endphp

<header class="sticky top-0 z-50 border-b border-[#E5E7EB]/80 bg-white/95 shadow-sm backdrop-blur">
    <nav class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8" aria-label="Navegación principal">
        <a href="{{ $logoHref }}" class="flex items-center gap-2 text-lg font-black tracking-normal text-[#222222]" aria-label="{{ $logo }}">
            <span class="grid size-9 place-items-center rounded-2xl bg-[#E91E63]" aria-hidden="true">
                <img class="size-7" src="{{ asset('icons/bunny-ears.svg') }}" alt="" width="28" height="28">
            </span>
            <span class="inline-flex items-baseline gap-1 text-3xl leading-none" style="font-family: 'Dancing Script', cursive !important; font-weight: 700;">
                <span style="color: #111111;">{{ $logoFirstWord }}</span>
                @if ($logoRest !== '')
                    <span style="color: #E10600;">{{ $logoRest }}</span>
                @endif
            </span>
        </a>

        <div class="hidden items-center gap-8 lg:flex">
            @foreach ($links as $link)
                <a href="{{ $link['href'] }}" class="text-sm font-medium text-[#6B7280] transition hover:text-[#E91E63]">
                    {{ $link['label'] }}
                </a>
            @endforeach
        </div>

        <div class="flex items-center gap-2">
            <x-button :href="$loginHref" variant="ghost" size="sm" class="hidden sm:inline-flex">{{ $loginLabel }}</x-button>
            <x-button :href="$publishHref" size="sm">{{ $publishLabel }}</x-button>
        </div>
    </nav>
</header>
