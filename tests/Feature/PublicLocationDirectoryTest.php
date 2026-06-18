<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Support\PublicLocationDirectory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicLocationDirectoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_three_ordered_groups_using_the_complete_location_catalog(): void
    {
        foreach ([
            ['Chachapoyas', 'Amazonas', 0],
            ['Huaraz', 'Áncash', 0],
            ['Arequipa', 'Arequipa', 0],
            ['Cayma', 'Arequipa', 1],
            ['Miraflores', 'Lima', 0],
            ['San Isidro', 'Lima', 1],
            ['Tacna', 'Tacna', 0],
        ] as [$name, $department, $sortOrder]) {
            Location::query()->create([
                'name' => $name,
                'department' => $department,
                'sort_order' => $sortOrder,
            ]);
        }

        $directory = PublicLocationDirectory::make();
        $links = collect($directory['groups'])->pluck('links')->flatten(1);

        $this->assertCount(3, $directory['groups']);
        $this->assertSame(
            ['Chachapoyas', 'Huaraz', 'Arequipa', 'Cayma', 'Tacna', 'Miraflores', 'San Isidro'],
            $links->pluck('label')->all(),
        );
        $this->assertSame(
            ['Ciudades y distritos del Perú', 'Lima Metropolitana', 'Callao'],
            collect($directory['groups'])->pluck('title')->all(),
        );
        $this->assertTrue($links->every(fn (array $link): bool => str_contains($link['href'], '/u/')));
    }

    public function test_catalog_location_without_posts_opens_an_empty_listing(): void
    {
        Location::query()->create([
            'name' => 'Chachapoyas',
            'department' => 'Amazonas',
            'sort_order' => 0,
        ]);

        $this->get(route('posts.locations.show', ['location' => 'chachapoyas']))
            ->assertOk()
            ->assertSee('Chachapoyas')
            ->assertSee('No hay posts activos en esta ubicación.');
    }
}
