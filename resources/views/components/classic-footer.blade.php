@props(['siteSettings'])

<footer class="classic-footer">
    <nav class="classic-footer-top-links" aria-label="Enlaces del footer">
        @foreach ($siteSettings->footerLegalLinks() as $link)
            <a href="{{ $link['href'] }}">{{ $link['label'] }}</a>
        @endforeach
        <a href="{{ route('advertise') }}">Anuncia en {{ $siteSettings->brandName() }}</a>
    </nav>

    <div class="classic-footer-main">
        <div class="classic-shell">
            <div class="classic-footer-seo">
                <p>
                    Entretenimiento para adultos con kinesiólogas y putas en ConejitasHot. Lindas putas, prostitutas, kinesiólogas y escorts en Perú.
                    Encuentra anuncios de chicas A1, sexo delivery y con departamento, kinesiólogas y putas independientes para servicios sexuales en Perú.
                    las mejores y más guapas escorts se anuncian en ConejtasHot que es la mejor manera de contactar con escorts, kinesiólogas, damas de compañía, putas y prostitutas.
                    Selecciona entre los cientos de anuncios que se publican diariamente en Perú. Date un capricho y disfruta del placer y la pasión de las escorts económicas que se anuncian en nuestro portal.
                    Lindas chicas baratas están esperando para hacerte disfrutar del sexo. ¿Qué esperas para empezar a buscar?
                    Encuentralas kinesiólogas y putas en Arequipa, Ayacucho, Juliaca, Huancayo, Cusco, Huánuco, Chiclayo, Trujillo, Piura, Huaraz, Chimbote, Iquitos, Pucallpa, Cajamarca, Tumbes, Tacna, Moquegua, Puno
                </p>
                <p>
                    Las mejores kinesiólogas y putas en ConejitasHot Peú. Encuentra las mejores putas, kinesiólogas, escorts, damas de compañía y prostitutas en ConejitasHot Perú.
                    Las chicas más lindas anuncian en ConejitasHot. Encuentra las mejores acompañantes y putas en ConejitasHot y Los mejores anuncios de sexo en ConejitasHot.
                    Contacta con cientos de Mujeres que se anuncian como putas vip, escorts complacientes y damas de compañía. Busca tu anuncio erótico.
                    Es muy fácil follar y tener sexo en ConejitasHot. Mujeres lindas y complaciente con fotos muy sexis suben sus anuncios a ConejitasHot y están disponibles para ti, puedes elegir a las que quieras probar.
                    Contáctalas por WhatsApp o Telegram o simplemente llámalas. Chicas disponibles en Lince, Los Olivos, Comas, San Isidro, Miraflores, Jesús María, Cercado, San Miguel, San Luis, Surco, San Juan de Miraflores, San Juan de Lurigancho, San Martín de Porres
                </p>
            </div>

            <x-classic-location-board />
        </div>
    </div>

    <div class="classic-shell">
        <div class="classic-footer-bottom">
            <span>Copyright© {{ $siteSettings->brandName() }} {{ now()->year }}</span>
            <a href="#" aria-label="Volver arriba">↟</a>
        </div>
    </div>
</footer>
