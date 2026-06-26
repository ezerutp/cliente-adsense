<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Location;
use App\Models\Post;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PostOnboardingTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_post_uses_the_six_step_onboarding(): void
    {
        $this->actingAs($this->admin())
            ->get(route('posts.create'))
            ->assertOk()
            ->assertSee('Datos básicos')
            ->assertSee('Contenido')
            ->assertSee('Imágenes')
            ->assertSee('Contacto')
            ->assertSee('Cards')
            ->assertSee('Publicación')
            ->assertSee('Paso 1 de 6');
    }

    public function test_immediate_post_end_date_uses_the_configured_local_timezone(): void
    {
        try {
            Carbon::setTestNow(Carbon::parse('2026-06-26 15:00:00', 'UTC'));
            SiteSetting::current()->update(['server_utc_offset' => '-05:00']);
            $category = Category::query()->create([
                'name' => 'Categoría',
                'slug' => 'categoria',
                'is_active' => true,
            ]);
            Location::query()->create([
                'name' => 'Miraflores',
                'department' => 'Lima',
                'sort_order' => 0,
            ]);

            $this->actingAs($this->admin())
                ->post(route('posts.store'), [
                    'category_id' => $category->id,
                    'title' => 'Post con fecha local',
                    'subtitle' => '',
                    'location' => 'Miraflores',
                    'body' => 'Contenido',
                    'cover_image_url' => '',
                    'gallery_image_urls' => '',
                    'tags' => '',
                    'publish_mode' => 'immediate',
                    'ends_at' => '2026-06-26T11:00',
                    'is_active' => '1',
                ])
                ->assertSessionHasNoErrors()
                ->assertRedirect(route('posts.index'));

            $post = Post::query()->where('title', 'Post con fecha local')->firstOrFail();

            $this->assertTrue($post->ends_at->equalTo(Carbon::parse('2026-06-26 16:00:00', 'UTC')));
        } finally {
            Carbon::setTestNow();
        }
    }

    private function admin(): User
    {
        $role = Role::query()->firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }
}
