<?php

namespace App\Support;

use App\Models\Location;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PublicLocationDirectory
{
    /**
     * @return array{
     *     eyebrow: string,
     *     title: string,
     *     description: string,
     *     groups: array<int, array{
     *         title: string,
     *         description: string,
     *         links: array<int, array{label: string, href: string}>
     *     }>
     * }
     */
    public static function make(): array
    {
        $locations = Schema::hasTable('locations')
            ? Location::query()
                ->get(['name', 'department', 'sort_order'])
                ->sort(function (Location $first, Location $second): int {
                    return Str::lower(Str::ascii($first->department)) <=> Str::lower(Str::ascii($second->department))
                        ?: $first->sort_order <=> $second->sort_order
                        ?: Str::lower(Str::ascii($first->name)) <=> Str::lower(Str::ascii($second->name));
                })
                ->map(fn (Location $location): array => [
                    'label' => $location->name,
                    'department' => $location->department,
                    'href' => route('posts.locations.show', ['location' => Str::slug($location->name)]),
                ])
                ->filter(fn (array $location): bool => Str::slug($location['label']) !== '')
                ->values()
            : collect();

        $groupDefinitions = [
            [
                'title' => 'Ciudades y distritos del Perú',
                'description' => 'Directorio nacional',
                'departments' => fn (string $department): bool => ! in_array($department, ['Lima', 'Callao'], true),
            ],
            [
                'title' => 'Lima Metropolitana',
                'description' => 'Distritos de Lima',
                'departments' => fn (string $department): bool => $department === 'Lima',
            ],
            [
                'title' => 'Callao',
                'description' => 'Distritos del Callao',
                'departments' => fn (string $department): bool => $department === 'Callao',
            ],
        ];

        return [
            'eyebrow' => 'Directorio',
            'title' => 'Explora anuncios por zona',
            'description' => 'Accesos rápidos a todas las ciudades y ubicaciones disponibles en nuestro directorio.',
            'groups' => collect($groupDefinitions)
                ->map(function (array $definition) use ($locations): array {
                    $links = $locations
                        ->filter(fn (array $location): bool => $definition['departments']($location['department']))
                        ->map(fn (array $location): array => [
                            'label' => $location['label'],
                            'href' => $location['href'],
                        ])
                        ->values()
                        ->all();

                    return [
                        'title' => $definition['title'],
                        'description' => $definition['description'].' · '.trans_choice(
                            '{0} 0 ubicaciones|{1} 1 ubicación|[2,*] :count ubicaciones',
                            count($links),
                            ['count' => count($links)],
                        ),
                        'links' => $links,
                    ];
                })
                ->all(),
        ];
    }
}
