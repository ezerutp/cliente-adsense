@props([
    'link',
])

<a href="{{ $link['href'] }}" class="block truncate rounded-md border border-[#E91E63]/25 bg-[#E91E63]/10 px-2 py-1.5 text-center text-xs font-semibold text-[#C2185B] transition duration-200 hover:border-[#E91E63] hover:bg-[#E91E63] hover:text-white focus:outline-none focus:ring-2 focus:ring-[#E91E63] focus:ring-offset-2" title="{{ $link['label'] }}">
    {{ $link['label'] }}
</a>
