<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locationsByDepartment = [
            'Perú' => [
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
            'Lima Metropolitana' => [
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
            'Lima Provincias' => [
                'Ancón', 'Arahuay', 'Asia', 'Aucallama', 'Barranca', 'Cajatambo',
                'Calango', 'Cañete', 'Canta', 'Cerro Azul', 'Chancay', 'Chicla',
                'Chilca', 'Churín', 'Cochamarca', 'Huacho', 'Huamantanga', 'Huaral',
                'Huaura', 'Imperial', 'Lunahuaná', 'Mala', 'Manás', 'Matucana',
                'Naván', 'Nuevo Imperial', 'Oyón', 'Paramonga', 'Pativilca', 'Quilmaná',
                'San Mateo', 'Santa Eulalia', 'Santa Rosa de Quives', 'Sayán', 'Supe', 'Végueta',
            ],
        ];

        Location::query()->delete();

        $sortOrder = 0;
        $seen = [];

        foreach ($locationsByDepartment as $department => $locations) {
            foreach ($locations as $location) {
                $name = trim($location);
                $key = mb_strtolower($name);

                if ($name === '' || isset($seen[$key])) {
                    continue;
                }

                $seen[$key] = true;

                Location::query()->create([
                    'name' => $name,
                    'department' => $department,
                    'sort_order' => $sortOrder++,
                ]);
            }
        }
    }
}
