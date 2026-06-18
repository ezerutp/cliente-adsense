<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPostSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_filter_combinations_are_optional_and_combined_with_and_logic(): void
    {
        $firstCategory = $this->category('Primera', 'primera');
        $secondCategory = $this->category('Segunda', 'segunda');

        $this->post($firstCategory, 'Primera Lima Alpha', 'primera-lima-alpha', 'Lima');
        $this->post($firstCategory, 'Primera Barranco Beta', 'primera-barranco-beta', 'Barranco');
        $this->post($secondCategory, 'Segunda Lima Beta', 'segunda-lima-beta', 'Lima');
        $this->post($secondCategory, 'Segunda Barranco Alpha', 'segunda-barranco-alpha', 'Barranco');

        $cases = [
            'sin filtros' => [
                [],
                ['Primera Lima Alpha', 'Primera Barranco Beta', 'Segunda Lima Beta', 'Segunda Barranco Alpha'],
            ],
            'solo ubicación' => [
                ['location' => 'lima'],
                ['Primera Lima Alpha', 'Segunda Lima Beta'],
            ],
            'solo categoría' => [
                ['category' => 'primera'],
                ['Primera Lima Alpha', 'Primera Barranco Beta'],
            ],
            'solo palabra clave' => [
                ['query' => 'Alpha'],
                ['Primera Lima Alpha', 'Segunda Barranco Alpha'],
            ],
            'ubicación y categoría' => [
                ['location' => 'lima', 'category' => 'primera'],
                ['Primera Lima Alpha'],
            ],
            'ubicación y palabra clave' => [
                ['location' => 'lima', 'query' => 'Beta'],
                ['Segunda Lima Beta'],
            ],
            'categoría y palabra clave' => [
                ['category' => 'primera', 'query' => 'Beta'],
                ['Primera Barranco Beta'],
            ],
            'los tres filtros' => [
                ['location' => 'lima', 'category' => 'segunda', 'query' => 'Beta'],
                ['Segunda Lima Beta'],
            ],
        ];

        foreach ($cases as $case => [$filters, $expectedTitles]) {
            $response = $this->get(route('posts.search', $filters));

            $response
                ->assertOk()
                ->assertViewHas('posts', function ($posts) use ($expectedTitles, $case): bool {
                    $actualTitles = collect($posts->items())->pluck('title')->sort()->values()->all();
                    $expectedTitles = collect($expectedTitles)->sort()->values()->all();

                    $this->assertSame($expectedTitles, $actualTitles, "Falló el caso: {$case}");

                    return true;
                });
        }
    }

    public function test_search_filters_by_category_and_location_without_keyword(): void
    {
        $firstCategory = $this->category('Primera', 'primera');
        $secondCategory = $this->category('Segunda', 'segunda');

        $this->post($firstCategory, 'Coincide', 'coincide', 'Miraflores');
        $this->post($firstCategory, 'Otra ubicación', 'otra-ubicacion', 'Barranco');
        $this->post($secondCategory, 'Otra categoría', 'otra-categoria', 'Miraflores');

        $this->get(route('posts.search', [
            'category' => 'primera',
            'location' => 'miraflores',
            'query' => '',
        ]))
            ->assertOk()
            ->assertSee('Coincide')
            ->assertDontSee('Otra ubicación')
            ->assertDontSee('Otra categoría');
    }

    public function test_search_options_and_results_only_use_public_posts(): void
    {
        $category = $this->category('Activa', 'activa');
        $this->post($category, 'Post público', 'post-publico', 'Lima', true);
        $this->post($category, 'Post oculto', 'post-oculto', 'Comas', false);

        $this->get('/')
            ->assertOk()
            ->assertSee('value="lima"', false)
            ->assertDontSee('value="comas"', false);

        $this->get(route('posts.search', ['query' => 'público']))
            ->assertOk()
            ->assertSee('Post público')
            ->assertDontSee('Post oculto');
    }

    private function category(string $name, string $slug): Category
    {
        return Category::query()->create([
            'name' => $name,
            'slug' => $slug,
            'is_active' => true,
        ]);
    }

    private function post(Category $category, string $title, string $slug, string $location, bool $active = true): Post
    {
        return Post::query()->create([
            'category_id' => $category->id,
            'title' => $title,
            'slug' => $slug,
            'body' => 'Contenido',
            'location' => $location,
            'is_active' => $active,
            'published_at' => now()->subDay(),
        ]);
    }
}
