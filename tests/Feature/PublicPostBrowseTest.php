<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPostBrowseTest extends TestCase
{
    use RefreshDatabase;

    public function test_posts_can_be_browsed_by_location_and_tag(): void
    {
        $category = Category::query()->create([
            'name' => 'Categoría',
            'slug' => 'categoria',
            'is_active' => true,
        ]);

        Post::query()->create([
            'category_id' => $category->id,
            'title' => 'Post visible',
            'slug' => 'post-visible',
            'body' => 'Contenido',
            'location' => 'José Leonardo Ortiz',
            'tags' => ['Verificado', 'Lima Norte'],
            'is_active' => true,
            'published_at' => now()->subDay(),
        ]);

        Post::query()->create([
            'category_id' => $category->id,
            'title' => 'Post inactivo',
            'slug' => 'post-inactivo',
            'body' => 'Contenido',
            'location' => 'José Leonardo Ortiz',
            'tags' => ['Lima Norte'],
            'is_active' => false,
            'published_at' => now()->subDay(),
        ]);

        $this->get('/u')
            ->assertOk()
            ->assertSee('/u/jose-leonardo-ortiz', false);

        $this->get('/u/jose-leonardo-ortiz')
            ->assertOk()
            ->assertSee('Post visible')
            ->assertDontSee('Post inactivo');

        $this->get('/t')
            ->assertOk()
            ->assertSee('/t/lima-norte', false);

        $this->get('/t/lima-norte')
            ->assertOk()
            ->assertSee('Post visible')
            ->assertDontSee('Post inactivo');
    }
}
