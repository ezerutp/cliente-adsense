<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

#[Fillable([
    'site_title',
    'site_subtitle',
    'cover_image_url',
    'primary_color',
    'primary_hover_color',
    'text_color',
    'muted_color',
    'background_color',
    'admin_ink_color',
    'admin_ink_hover_color',
    'admin_muted_color',
    'admin_danger_color',
    'admin_focus_color',
    'server_country',
    'server_country_code',
    'server_utc_offset',
])]
class SiteSetting extends Model
{
    public const DEFAULTS = [
        'site_title' => 'Encuentra perfiles destacados cerca de ti',
        'site_subtitle' => 'Explora anuncios verificados con filtros rápidos, experiencia limpia y una navegación diseñada para inspirar confianza.',
        'cover_image_url' => 'https://www.skokka.com.pe/static/assets/ES_HERO_DESKTOP_2400x1220_1.9b9e620ff0b74030d571.jpg',
        'primary_color' => '#E91E63',
        'primary_hover_color' => '#D81B60',
        'text_color' => '#222222',
        'muted_color' => '#6B7280',
        'background_color' => '#F8F8F8',
        'admin_ink_color' => '#1f2937',
        'admin_ink_hover_color' => '#374151',
        'admin_muted_color' => '#4b5563',
        'admin_danger_color' => '#dc2626',
        'admin_focus_color' => '#6366f1',
        'server_country' => 'Perú',
        'server_country_code' => 'PE',
        'server_utc_offset' => '-05:00',
    ];

    public const SERVER_COUNTRIES = [
        'Argentina' => ['code' => 'AR', 'utc' => '-03:00', 'dial_code' => '54'],
        'Bolivia' => ['code' => 'BO', 'utc' => '-04:00', 'dial_code' => '591'],
        'Brasil' => ['code' => 'BR', 'utc' => '-03:00', 'dial_code' => '55'],
        'Canadá' => ['code' => 'CA', 'utc' => '-05:00', 'dial_code' => '1'],
        'Chile' => ['code' => 'CL', 'utc' => '-04:00', 'dial_code' => '56'],
        'Colombia' => ['code' => 'CO', 'utc' => '-05:00', 'dial_code' => '57'],
        'Costa Rica' => ['code' => 'CR', 'utc' => '-06:00', 'dial_code' => '506'],
        'Cuba' => ['code' => 'CU', 'utc' => '-05:00', 'dial_code' => '53'],
        'Ecuador' => ['code' => 'EC', 'utc' => '-05:00', 'dial_code' => '593'],
        'El Salvador' => ['code' => 'SV', 'utc' => '-06:00', 'dial_code' => '503'],
        'España' => ['code' => 'ES', 'utc' => '+01:00', 'dial_code' => '34'],
        'Estados Unidos' => ['code' => 'US', 'utc' => '-05:00', 'dial_code' => '1'],
        'Guatemala' => ['code' => 'GT', 'utc' => '-06:00', 'dial_code' => '502'],
        'Honduras' => ['code' => 'HN', 'utc' => '-06:00', 'dial_code' => '504'],
        'México' => ['code' => 'MX', 'utc' => '-06:00', 'dial_code' => '52'],
        'Nicaragua' => ['code' => 'NI', 'utc' => '-06:00', 'dial_code' => '505'],
        'Panamá' => ['code' => 'PA', 'utc' => '-05:00', 'dial_code' => '507'],
        'Paraguay' => ['code' => 'PY', 'utc' => '-04:00', 'dial_code' => '595'],
        'Perú' => ['code' => 'PE', 'utc' => '-05:00', 'dial_code' => '51'],
        'Portugal' => ['code' => 'PT', 'utc' => '+00:00', 'dial_code' => '351'],
        'Puerto Rico' => ['code' => 'PR', 'utc' => '-04:00', 'dial_code' => '1'],
        'República Dominicana' => ['code' => 'DO', 'utc' => '-04:00', 'dial_code' => '1'],
        'Uruguay' => ['code' => 'UY', 'utc' => '-03:00', 'dial_code' => '598'],
        'Venezuela' => ['code' => 'VE', 'utc' => '-04:00', 'dial_code' => '58'],
    ];

    public static function current(): self
    {
        if (! Schema::hasTable('site_settings')) {
            return new self(self::DEFAULTS);
        }

        return self::query()->firstOrCreate(['id' => 1], self::DEFAULTS);
    }

    /**
     * @return array<string, string>
     */
    public function cssVariables(): array
    {
        return [
            '--site-primary' => $this->primary_color,
            '--site-primary-hover' => $this->primary_hover_color,
            '--site-text' => $this->text_color,
            '--site-muted' => $this->muted_color,
            '--site-bg' => $this->background_color,
            '--admin-ink' => $this->admin_ink_color,
            '--admin-ink-hover' => $this->admin_ink_hover_color,
            '--admin-muted' => $this->admin_muted_color,
            '--admin-danger' => $this->admin_danger_color,
            '--admin-focus' => $this->admin_focus_color,
        ];
    }

    public function inlineCssVariables(): string
    {
        return collect($this->cssVariables())
            ->filter(fn (?string $value): bool => filled($value))
            ->map(fn (string $value, string $name): string => "{$name}: {$value};")
            ->implode(' ');
    }

    public function inlineCssVariableBlock(): string
    {
        $variables = $this->inlineCssVariables();

        return $variables === '' ? '' : ":root { {$variables} }";
    }
}
