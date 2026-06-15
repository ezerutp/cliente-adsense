<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Configuración del sitio
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 rounded-md bg-green-50 p-4 text-sm font-medium text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('settings.update') }}" class="space-y-8 p-6">
                    @csrf
                    @method('PUT')

                    <section class="space-y-6">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">Portada</h3>
                        </div>

                        <div>
                            <x-input-label for="site_title" value="Título" />
                            <x-text-input
                                id="site_title"
                                name="site_title"
                                type="text"
                                class="mt-1 block w-full"
                                :value="old('site_title', $settings->site_title)"
                                required
                            />
                            <x-input-error class="mt-2" :messages="$errors->get('site_title')" />
                        </div>

                        <div>
                            <x-input-label for="site_subtitle" value="Subtítulo" />
                            <textarea
                                id="site_subtitle"
                                name="site_subtitle"
                                rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required
                            >{{ old('site_subtitle', $settings->site_subtitle) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('site_subtitle')" />
                        </div>

                        <div>
                            <x-input-label for="cover_image_url" value="Foto de portada" />
                            <x-text-input
                                id="cover_image_url"
                                name="cover_image_url"
                                type="url"
                                class="mt-1 block w-full"
                                :value="old('cover_image_url', $settings->cover_image_url)"
                                placeholder="https://..."
                            />
                            <x-input-error class="mt-2" :messages="$errors->get('cover_image_url')" />
                        </div>
                    </section>

                    <section class="space-y-6 border-t border-gray-200 pt-8">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">Colores globales</h3>
                        </div>

                        <div class="grid gap-6 sm:grid-cols-3">
                            @foreach ([
                                'primary_color' => 'Primario',
                                'primary_hover_color' => 'Primario hover',
                                'text_color' => 'Texto',
                                'muted_color' => 'Texto secundario',
                                'background_color' => 'Fondo',
                                'admin_ink_color' => 'Admin principal',
                                'admin_ink_hover_color' => 'Admin hover',
                                'admin_muted_color' => 'Admin secundario',
                                'admin_danger_color' => 'Admin peligro',
                                'admin_focus_color' => 'Admin foco',
                            ] as $field => $label)
                                <div>
                                    <x-input-label :for="$field" :value="$label" />
                                    <div class="mt-1 flex gap-2">
                                        <input
                                            id="{{ $field }}"
                                            type="color"
                                            class="h-10 w-12 rounded-md border border-gray-300 bg-white p-1"
                                            value="{{ old($field, $settings->{$field}) }}"
                                            onchange="this.nextElementSibling.value = this.value"
                                        >
                                        <x-text-input
                                            name="{{ $field }}"
                                            type="text"
                                            class="block w-full"
                                            :value="old($field, $settings->{$field})"
                                            required
                                        />
                                    </div>
                                    <x-input-error class="mt-2" :messages="$errors->get($field)" />
                                </div>
                            @endforeach
                        </div>
                    </section>

                    <section
                        class="space-y-6 border-t border-gray-200 pt-8"
                        x-data="{
                            countries: @js($serverCountries),
                            country: @js(old('server_country', $settings->server_country)),
                            code: @js(old('server_country_code', $settings->server_country_code)),
                            utc: @js(old('server_utc_offset', $settings->server_utc_offset)),
                            syncCountry() {
                                const selected = this.countries[this.country];

                                if (! selected) {
                                    return;
                                }

                                this.code = selected.code;
                                this.utc = selected.utc;
                            },
                        }"
                    >
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">Servidor</h3>
                        </div>

                        <div class="grid gap-6 sm:grid-cols-3">
                            <div>
                                <x-input-label for="server_country" value="País" />
                                <select
                                    id="server_country"
                                    name="server_country"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    x-model="country"
                                    x-on:change="syncCountry"
                                    required
                                >
                                    <option value="">Selecciona un país</option>
                                    @foreach ($serverCountries as $country => $metadata)
                                        <option value="{{ $country }}" @selected(old('server_country', $settings->server_country) === $country)>
                                            {{ $country }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('server_country')" />
                            </div>

                            <div>
                                <x-input-label for="server_country_code" value="Código de país" />
                                <x-text-input
                                    id="server_country_code"
                                    name="server_country_code"
                                    type="text"
                                    class="mt-1 block w-full uppercase"
                                    x-model="code"
                                    placeholder="PE"
                                    required
                                />
                                <x-input-error class="mt-2" :messages="$errors->get('server_country_code')" />
                            </div>

                            <div>
                                <x-input-label for="server_utc_offset" value="UTC" />
                                <x-text-input
                                    id="server_utc_offset"
                                    name="server_utc_offset"
                                    type="text"
                                    class="mt-1 block w-full"
                                    x-model="utc"
                                    placeholder="-05:00"
                                    required
                                />
                                <x-input-error class="mt-2" :messages="$errors->get('server_utc_offset')" />
                            </div>
                        </div>
                    </section>

                    <section class="space-y-6 border-t border-gray-200 pt-8">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">Modal de confirmación de edad</h3>
                        </div>

                        <label class="flex items-center gap-3 rounded-md border border-gray-200 p-4">
                            <input
                                type="checkbox"
                                name="age_gate_is_enabled"
                                value="1"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                @checked(old('age_gate_is_enabled', $ageGateSettings->is_enabled))
                            >
                            <span class="text-sm font-medium text-gray-700">Mostrar modal de confirmación</span>
                        </label>

                        <div class="grid gap-6 sm:grid-cols-2">
                            <div>
                                <x-input-label for="age_gate_storage_key" value="Storage key" />
                                <x-text-input
                                    id="age_gate_storage_key"
                                    name="age_gate_storage_key"
                                    type="text"
                                    class="mt-1 block w-full"
                                    :value="old('age_gate_storage_key', $ageGateSettings->storage_key)"
                                    required
                                />
                                <x-input-error class="mt-2" :messages="$errors->get('age_gate_storage_key')" />
                            </div>

                            <div>
                                <x-input-label for="age_gate_badge" value="Badge" />
                                <x-text-input
                                    id="age_gate_badge"
                                    name="age_gate_badge"
                                    type="text"
                                    class="mt-1 block w-full"
                                    :value="old('age_gate_badge', $ageGateSettings->badge)"
                                    required
                                />
                                <x-input-error class="mt-2" :messages="$errors->get('age_gate_badge')" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="age_gate_title" value="Título" />
                            <x-text-input
                                id="age_gate_title"
                                name="age_gate_title"
                                type="text"
                                class="mt-1 block w-full"
                                :value="old('age_gate_title', $ageGateSettings->title)"
                                required
                            />
                            <x-input-error class="mt-2" :messages="$errors->get('age_gate_title')" />
                        </div>

                        <div>
                            <x-input-label for="age_gate_description" value="Descripción" />
                            <textarea
                                id="age_gate_description"
                                name="age_gate_description"
                                rows="4"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required
                            >{{ old('age_gate_description', $ageGateSettings->description) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('age_gate_description')" />
                        </div>

                        <div class="grid gap-6 sm:grid-cols-2">
                            <div>
                                <x-input-label for="age_gate_confirm_label" value="Texto botón confirmar" />
                                <x-text-input
                                    id="age_gate_confirm_label"
                                    name="age_gate_confirm_label"
                                    type="text"
                                    class="mt-1 block w-full"
                                    :value="old('age_gate_confirm_label', $ageGateSettings->confirm_label)"
                                    required
                                />
                                <x-input-error class="mt-2" :messages="$errors->get('age_gate_confirm_label')" />
                            </div>

                            <div>
                                <x-input-label for="age_gate_exit_label" value="Texto botón salir" />
                                <x-text-input
                                    id="age_gate_exit_label"
                                    name="age_gate_exit_label"
                                    type="text"
                                    class="mt-1 block w-full"
                                    :value="old('age_gate_exit_label', $ageGateSettings->exit_label)"
                                    required
                                />
                                <x-input-error class="mt-2" :messages="$errors->get('age_gate_exit_label')" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="age_gate_exit_href" value="URL de salida" />
                            <x-text-input
                                id="age_gate_exit_href"
                                name="age_gate_exit_href"
                                type="url"
                                class="mt-1 block w-full"
                                :value="old('age_gate_exit_href', $ageGateSettings->exit_href)"
                                required
                            />
                            <x-input-error class="mt-2" :messages="$errors->get('age_gate_exit_href')" />
                        </div>

                        <div>
                            <x-input-label for="age_gate_legal_text" value="Texto legal" />
                            <textarea
                                id="age_gate_legal_text"
                                name="age_gate_legal_text"
                                rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required
                            >{{ old('age_gate_legal_text', $ageGateSettings->legal_text) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('age_gate_legal_text')" />
                        </div>
                    </section>

                    <div class="flex justify-end border-t border-gray-200 pt-6">
                        <x-primary-button>
                            Guardar configuración
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
