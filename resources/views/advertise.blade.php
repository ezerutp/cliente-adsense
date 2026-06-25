@php
    $contactButtons = collect($contactButtons ?? []);
@endphp

@extends('layouts.public-classic')

@section('title', 'Publicar anuncio | '.$siteSettings->brandName())
@section('description', 'Conoce los tipos de anuncio y contacta con '.$siteSettings->brandName().' para publicar.')

@section('content')
    <main class="classic-advertise-page">
        <section class="classic-advertise-banner">
            <div class="classic-shell">
                <span>www.{{ \Illuminate\Support\Str::slug($siteSettings->brandName(), '') }}.com</span>
            </div>
        </section>

        <section id="contacto" class="classic-shell classic-advertise-contact">
            <img src="{{ asset('logo.webp') }}" alt="{{ $siteSettings->brandName() }}" class="classic-advertise-logo">
            <h1>Solo para publicar<br>clic aquí</h1>

            @if ($contactButtons->isNotEmpty())
                <div class="classic-advertise-channels">
                    @foreach ($contactButtons as $contact)
                        <a
                            href="{{ $contact['href'] }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="classic-advertise-channel classic-advertise-channel-{{ $contact['provider'] }}"
                            style="--channel-color: {{ $contact['button_color'] }}"
                            aria-label="Contactar por {{ $contact['provider'] === 'whatsapp' ? 'WhatsApp' : 'Telegram' }}"
                        >
                            <span>
                                <x-dynamic-component :component="$contact['icon']" class="size-12" />
                            </span>
                            <strong>{{ $contact['provider'] === 'whatsapp' ? 'WhatsApp' : 'Telegram' }}</strong>
                            <small>Contactar ahora</small>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="classic-advertise-no-contact">Los canales de contacto estarán disponibles muy pronto.</p>
            @endif

            <p class="classic-advertise-warning">
                Es indispensable que confirmes ser<br>
                mayor de edad para poder publicar
            </p>
        </section>

        <section id="tipos" class="classic-shell classic-advertise-types">
            <h2 class="sr-only">Tipos de anuncio</h2>

            <article class="classic-advertise-type">
                <h3>Publicidad estándar</h3>
                <div class="classic-standard-example" aria-label="Ejemplo de anuncio estándar">
                    <span>Publicidad<br>Estándar</span>
                    <strong>Señoritas<br>Disponibles</strong>
                    <small>Tu mejor fotografía aquí</small>
                </div>
                <h4>Anuncio estándar</h4>
                <p>Una publicación completa con portada, galería, descripción, ubicación y botones de contacto.</p>
            </article>

            <article class="classic-advertise-type">
                <h3>Publicidad banner</h3>
                <div class="classic-banner-example" aria-label="Ejemplo de anuncio destacado">
                    <span>Tu publicidad</span>
                    <strong>Banner destacado</strong>
                    <small>Mayor visibilidad dentro de la plataforma</small>
                </div>
                <h4>Anuncio destacado</h4>
                <p>Incluye la publicación completa y una presencia preferente en las secciones destacadas.</p>
            </article>
        </section>

        <section class="classic-shell classic-advertise-benefits">
            <h2>Beneficios</h2>
            <h3>Te ofrecemos:</h3>
            <ul>
                <li>Edición en formatos exclusivos.</li>
                <li>Puedes pausar tu anuncio y mantener tus días restantes.</li>
                <li>Asesoramiento personalizado y discreto.</li>
                <li>Puedes cambiar fácilmente de ciudad o realizar modificaciones en tu aviso.</li>
                <li>Precios asequibles y económicos.</li>
            </ul>
            <p>¡Es muy fácil, anuncia ya<br><br>y empieza a ganar más!</p>
        </section>

        <section class="classic-shell classic-advertise-copy">
            <p>
                Publica tu anuncio en {{ $siteSettings->brandName() }} y presenta tu perfil de forma clara y directa.
                Tus fotografías, descripción, ubicación y canales de contacto quedan organizados para que las personas
                interesadas encuentren rápidamente toda la información necesaria.
            </p>
            <p>
                Los anuncios activos pueden aparecer en categorías, ubicaciones, etiquetas y resultados de búsqueda.
                También puedes solicitar una publicación destacada para obtener mayor presencia dentro de la plataforma.
            </p>
        </section>

    </main>
@endsection
