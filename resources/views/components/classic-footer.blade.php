@props(['siteSettings'])

<footer class="classic-footer">
    <div class="classic-shell">
        <div class="classic-footer-brand">
            <a href="{{ url('/') }}">
                <img src="{{ asset('logo.webp') }}" alt="{{ $siteSettings->brandName() }}">
                <span class="sr-only">{{ $siteSettings->brandName() }} {{ $siteSettings->brandInitials() }}</span>
            </a>
        </div>

        <div class="classic-footer-columns">
            @foreach ($siteSettings->footerGroups() as $title => $items)
                <section>
                    <h2>{{ $title }}</h2>
                    <ul>
                        @foreach ($items as $item)
                            <li><a href="{{ $item['href'] }}">{{ $item['label'] }}</a></li>
                        @endforeach
                    </ul>
                </section>
            @endforeach
        </div>

        <a href="{{ route('advertise') }}" class="classic-footer-cta">Publica tu anuncio</a>

        <div class="classic-footer-legal">
            <span>&copy; {{ now()->year }} {{ $siteSettings->brandName() }}</span>
            @foreach ($siteSettings->footerLegalLinks() as $link)
                <a href="{{ $link['href'] }}">{{ $link['label'] }}</a>
            @endforeach
        </div>
    </div>
</footer>
