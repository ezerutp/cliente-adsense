<?php

namespace App\Http\Controllers;

use App\Models\AgeGateSetting;
use App\Models\Location;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiteSettingController extends Controller
{
    public function edit(): View
    {
        return view('settings.edit', [
            'ageGateSettings' => AgeGateSetting::current(),
            'locations' => Location::query()
                ->orderBy('department')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->paginate(15, ['*'], 'locations_page')
                ->withQueryString()
                ->fragment('locations'),
            'serverCountries' => SiteSetting::SERVER_COUNTRIES,
            'settings' => SiteSetting::current(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $settings = SiteSetting::current();
        $ageGateSettings = AgeGateSetting::current();
        $data = $this->validatedData($request);

        $settings->update($data['site']);
        $ageGateSettings->update($data['age_gate']);

        return redirect()
            ->route('settings.edit')
            ->with('status', 'Configuración actualizada correctamente.');
    }

    /**
     * @return array{site: array<string, string|null>, age_gate: array<string, bool|string>}
     */
    private function validatedData(Request $request): array
    {
        $hex = ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'];

        $data = $request->validate([
            'site_title' => ['required', 'string', 'max:255'],
            'site_subtitle' => ['required', 'string', 'max:500'],
            'cover_image_url' => ['nullable', 'url', 'max:2048'],
            'primary_color' => $hex,
            'primary_hover_color' => $hex,
            'text_color' => $hex,
            'muted_color' => $hex,
            'background_color' => $hex,
            'admin_ink_color' => $hex,
            'admin_ink_hover_color' => $hex,
            'admin_muted_color' => $hex,
            'admin_danger_color' => $hex,
            'admin_focus_color' => $hex,
            'server_country' => ['required', 'string', 'max:255'],
            'server_country_code' => ['required', 'string', 'max:8'],
            'server_utc_offset' => ['required', 'regex:/^[+-](0\d|1[0-4]):[0-5]\d$/'],
            'age_gate_storage_key' => ['required', 'string', 'max:120', 'regex:/^[A-Za-z0-9_.:-]+$/'],
            'age_gate_badge' => ['required', 'string', 'max:24'],
            'age_gate_title' => ['required', 'string', 'max:255'],
            'age_gate_description' => ['required', 'string', 'max:1000'],
            'age_gate_confirm_label' => ['required', 'string', 'max:80'],
            'age_gate_exit_label' => ['required', 'string', 'max:80'],
            'age_gate_exit_href' => ['required', 'url', 'max:2048'],
            'age_gate_legal_text' => ['required', 'string', 'max:1000'],
        ]);

        return [
            'site' => collect($data)
                ->except([
                    'age_gate_storage_key',
                    'age_gate_badge',
                    'age_gate_title',
                    'age_gate_description',
                    'age_gate_confirm_label',
                    'age_gate_exit_label',
                    'age_gate_exit_href',
                    'age_gate_legal_text',
                ])
                ->all(),
            'age_gate' => [
                'is_enabled' => $request->boolean('age_gate_is_enabled'),
                'storage_key' => $data['age_gate_storage_key'],
                'badge' => $data['age_gate_badge'],
                'title' => $data['age_gate_title'],
                'description' => $data['age_gate_description'],
                'confirm_label' => $data['age_gate_confirm_label'],
                'exit_label' => $data['age_gate_exit_label'],
                'exit_href' => $data['age_gate_exit_href'],
                'legal_text' => $data['age_gate_legal_text'],
            ],
        ];
    }
}
