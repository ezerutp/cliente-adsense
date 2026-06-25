<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicHomeListingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_latest_and_vip_lists_exclude_banner_posts(): void
    {
        $category = Category::query()->create([
            'name' => 'Categoría',
            'slug' => 'categoria',
            'is_active' => true,
        ]);

        Post::query()->create([
            'category_id' => $category->id,
            'card_type' => Post::CARD_TYPE_BANNER,
            'title' => 'Banner home',
            'slug' => 'banner-home',
            'body' => 'Contenido',
            'location' => 'Lima',
            'is_active' => true,
            'is_vip' => true,
            'published_at' => now(),
        ]);

        Post::query()->create([
            'category_id' => $category->id,
            'card_type' => Post::CARD_TYPE_POST,
            'title' => 'Normal home',
            'slug' => 'normal-home',
            'body' => 'Contenido',
            'location' => 'Lima',
            'is_active' => true,
            'is_vip' => true,
            'published_at' => now()->subMinute(),
        ]);

        $this->get('/')
            ->assertOk()
            ->assertViewHas('premiumListings', function ($premiumListings): bool {
                $titles = $premiumListings->pluck('title');

                return $titles->contains('Normal home')
                    && ! $titles->contains('Banner home');
            })
            ->assertViewHas('latestPublications', function ($latestPublications): bool {
                $titles = $latestPublications->pluck('title');

                return $titles->contains('Normal home')
                    && ! $titles->contains('Banner home');
            });
    }
}
