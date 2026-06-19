@php
    $groups = [
        [
            'title' => 'Kinesiólogas en Perú',
            'class' => 'classic-location-group-peru',
            'locations' => [
                'Abancay', 'Acobamba', 'Acomayo', 'Aguaytía', 'Ambo', 'Andahuaylas',
                'Anta', 'Aplao', 'Arequipa', 'Ascope', 'Atalaya', 'Ayabaca',
                'Ayacucho', 'Ayaviri', 'Azángaro', 'Bagua', 'Bagua Grande', 'Bambamarca',
                'Bellavista SM', 'Cajabamba', 'Cajamarca', 'Calca', 'Camaná', 'Cangallo',
                'Caravelí', 'Caraz', 'Carhuaz', 'Casma', 'Catacaos', 'Celendín',
                'Cerro de Pasco', 'Chachapoyas', 'Chalhuanca', 'Chepén', 'Chiclayo', 'Chimbote',
                'Chincha', 'Chincheros', 'Chiquián', 'Chivay', 'Chota', 'Chulucanas',
                'Chupaca', 'Chuquibamba', 'Churcampa', 'Concepción', 'Contamana', 'Contumazá',
                'Coracora', 'Cotahuasi', 'Cusco', 'Cutervo', 'Ferreñafe', 'Huamachuco',
                'Huancabamba', 'Huancapi', 'Huancavelica', 'Huancayo', 'Huanchaco', 'Huanta',
                'Huánuco', 'Huaraz', 'Huari', 'Huaral', 'Ica', 'Ilave',
                'Ilo', 'Iquitos', 'Jaén', 'Jauja', 'Juanjuí', 'Juli',
                'Juliaca', 'Jumbilla', 'Junín', 'La Merced', 'La Oroya', 'Lamas',
                'Lambayeque', 'Lámud', 'Lircay', 'Mendoza', 'Mollendo', 'Moquegua',
                'Moyobamba', 'Nauta', 'Nazca', 'Otuzco', 'Oxapampa', 'Pacasmayo',
                'Paita', 'Pallasca', 'Pampas', 'Paruro', 'Paucartambo', 'Picota',
                'Pichari', 'Pisco', 'Piscobamba', 'Piura', 'Pomabamba', 'Pucallpa',
                'Puerto Maldonado', 'Puno', 'Puquio', 'Quillabamba', 'Recuay', 'Requena',
                'Rioja', 'San Ignacio', 'San Marcos', 'San Miguel Ay', 'San P. de Lloc', 'San Pablo',
                'San Ramón', 'Santa Cruz', 'Santa María N', 'Santiago de Chuco', 'Santo Tomás', 'Satipo',
                'Sechura', 'Sicuani', 'Sihuas', 'Sullana', 'Tacna', 'Talara',
                'Tarapoto', 'Tarma', 'Tayabamba', 'Tingo María', 'Tocache', 'Trujillo',
                'Tumbes', 'Urcos', 'Urubamba', 'Vilcashuamán', 'Virú', 'Yanaoca',
                'Yauri', 'Yauyos', 'Yungay', 'Yurimaguas', 'Zarumilla', 'Zorritos',
            ],
        ],
        [
            'title' => 'Kinesiólogas en Lima Metropolitana',
            'class' => 'classic-location-group-lima',
            'locations' => [
                'Ate', 'Av. Izaguirre', 'Av. Arequipa', 'Barranco', 'Bellavista', 'Breña',
                'Callao', 'Carabayllo', 'Carmen de la Legua', 'Cercado', 'Chaclacayo', 'Chorrillos',
                'Chosica', 'Cieneguilla', 'Ciudad de Dios', 'Colombianas', 'Comas', 'El Agustino',
                'Fiori', 'Huachipa', 'Independencia', 'Jesús María', 'La Molina', 'La Perla',
                'La Punta', 'La Victoria', 'Lince', 'Los Olivos', 'Lurín', 'Magdalena',
                'Mall del Sur', 'Mega Plaza', 'Mi Perú', 'Miraflores', 'Pachacámac', 'Plaza Norte',
                'Pucusana', 'Pueblo Libre', 'Puente Piedra', 'Punta Hermosa', 'Punta Negra', 'Rímac',
                'San Bartolo', 'San Borja', 'San Isidro', 'San Juan de Lurigancho', 'San Juan de Miraflores', 'San Luis',
                'San Martín', 'San Miguel', 'Santa Anita', 'Santa María', 'Santa Rosa', 'Surco',
                'Surquillo', 'Venezolanas', 'Ventanilla', 'Villa El Salvador', 'Villa María del Triunfo', 'Zárate',
            ],
        ],
        [
            'title' => 'Kinesiólogas en Lima Provincias',
            'class' => 'classic-location-group-provinces',
            'locations' => [
                'Ancón', 'Arahuay', 'Asia', 'Aucallama', 'Barranca', 'Cajatambo',
                'Calango', 'Cañete', 'Canta', 'Cerro Azul', 'Chancay', 'Chicla',
                'Chilca', 'Churín', 'Cochamarca', 'Huacho', 'Huamantanga', 'Huaral',
                'Huaura', 'Imperial', 'Lunahuaná', 'Mala', 'Manás', 'Matucana',
                'Naván', 'Nuevo Imperial', 'Oyón', 'Paramonga', 'Pativilca', 'Quilmaná',
                'San Mateo', 'Santa Eulalia', 'Santa Rosa de Quives', 'Sayán', 'Supe', 'Végueta',
            ],
        ],
    ];
@endphp

<section class="classic-location-board" aria-label="Directorio de ubicaciones">
    <div class="classic-location-grid classic-location-grid-complete">
        @foreach ($groups as $group)
            <article class="classic-location-group {{ $group['class'] }}">
                <h3>{{ $group['title'] }}</h3>
                <div>
                    @foreach ($group['locations'] as $location)
                        <a href="{{ route('posts.search', ['location' => \Illuminate\Support\Str::slug($location)]) }}">
                            {{ $location }}
                        </a>
                    @endforeach
                </div>
            </article>
        @endforeach
    </div>
</section>
