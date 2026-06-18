<?php

namespace Tests\Feature;

use App\Models\Integration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicAdvertisePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_advertise_page_explains_types_benefits_and_example(): void
    {
        $response = $this->get(route('advertise'));

        $response
            ->assertOk()
            ->assertSee('Tipos de anuncio')
            ->assertSee('Anuncio estándar')
            ->assertSee('Anuncio destacado')
            ->assertSee('Beneficios')
            ->assertSee('Tu mejor fotografía aquí');
    }

    public function test_advertise_page_only_shows_active_whatsapp_and_telegram_integrations(): void
    {
        Integration::query()->create([
            'name' => 'WhatsApp comercial',
            'provider' => 'whatsapp',
            'base_url' => 'https://wa.me/51999999999',
            'button_color' => '#25D366',
            'icon' => Integration::DEFAULT_ICONS['whatsapp'],
            'is_active' => true,
        ]);

        Integration::query()->create([
            'name' => 'Telegram oculto',
            'provider' => 'telegram',
            'base_url' => 'https://t.me/oculto',
            'button_color' => '#229ED9',
            'icon' => Integration::DEFAULT_ICONS['telegram'],
            'is_active' => false,
        ]);

        $response = $this->get(route('advertise'));

        $response
            ->assertOk()
            ->assertSee('Contactar por WhatsApp')
            ->assertSee('https://wa.me/51999999999')
            ->assertDontSee('Contactar por Telegram')
            ->assertDontSee('https://t.me/oculto');
    }
}
