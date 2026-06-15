@php
    $post = $post ?? null;
    $method = $method ?? 'POST';
    $submitLabel = $submitLabel ?? 'Guardar post';
    $integrations = collect($integrations ?? []);
    $galleryValue = old('gallery_image_urls', $post ? implode("\n", $post->gallery_image_urls ?? []) : '');
    $tagsValue = old('tags', $post ? implode(', ', $post->tags ?? []) : '');
    $publishMode = old('publish_mode', $post?->published_at?->isFuture() ? 'scheduled' : 'immediate');
    $publishedAtValue = old('published_at', $post?->published_at?->format('Y-m-d\TH:i'));
    $endsAtValue = old('ends_at', $post?->ends_at?->format('Y-m-d\TH:i'));
    $siteSettings = \App\Models\SiteSetting::current();
    $defaultDialCode = \App\Models\SiteSetting::SERVER_COUNTRIES[$siteSettings->server_country]['dial_code'] ?? '51';
    $postCardColorSuggestions = $postCardColorSuggestions ?? ['byTitle' => [], 'colors' => []];
    $postCardsValue = old('post_cards');
    $postCardsInitial = $postCardsValue !== null
        ? array_values($postCardsValue)
        : ($post?->cards?->map(fn ($card) => [
            'title' => $card->title,
            'color' => $card->color ?? '#E91E63',
            'is_active' => $card->is_active,
            'fields' => collect($card->fields ?? [])->map(fn ($field) => [
                'key' => $field['key'] ?? '',
                'value' => $field['value'] ?? '',
            ])->values()->all(),
        ])->values()->all() ?? []);
@endphp

<form
    method="POST"
    action="{{ $action }}"
    class="space-y-6"
    x-data="{
        publishMode: @js($publishMode),
        cards: @js($postCardsInitial),
        cardColorSuggestions: @js($postCardColorSuggestions['byTitle'] ?? []),
        reusableCardColors: @js($postCardColorSuggestions['colors'] ?? []),
        addCard() {
            this.cards.push({
                title: '',
                color: '#E91E63',
                colorTouched: false,
                is_active: true,
                fields: [{ key: '', value: '' }],
            });
        },
        normalizeTitle(value) {
            return (value || '').trim().toLowerCase();
        },
        suggestCardColor(card) {
            const suggested = this.cardColorSuggestions[this.normalizeTitle(card.title)];

            if (suggested && ! card.colorTouched) {
                card.color = suggested;
            }
        },
        setCardColor(card, color) {
            card.color = color;
            card.colorTouched = true;
        },
        removeCard(cardIndex) {
            this.cards.splice(cardIndex, 1);
        },
        addField(card) {
            card.fields.push({ key: '', value: '' });
        },
        removeField(card, fieldIndex) {
            card.fields.splice(fieldIndex, 1);
        },
    }"
>
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div>
        <x-input-label for="category_id" value="Categoría" />
        <select
            id="category_id"
            name="category_id"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            required
        >
            <option value="">Selecciona una categoría</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((int) old('category_id', $post?->category_id) === $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
    </div>

    <div class="grid gap-6 sm:grid-cols-2">
        <div>
            <x-input-label for="title" value="Título" />
            <x-text-input
                id="title"
                name="title"
                type="text"
                class="mt-1 block w-full"
                :value="old('title', $post?->title)"
                required
                autofocus
            />
            <x-input-error class="mt-2" :messages="$errors->get('title')" />
        </div>

        <div>
            <x-input-label for="subtitle" value="Subtítulo" />
            <x-text-input
                id="subtitle"
                name="subtitle"
                type="text"
                class="mt-1 block w-full"
                :value="old('subtitle', $post?->subtitle)"
            />
            <x-input-error class="mt-2" :messages="$errors->get('subtitle')" />
        </div>
    </div>

    <div>
        <x-input-label for="body" value="Texto" />
        <textarea
            id="body"
            name="body"
            rows="7"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            required
        >{{ old('body', $post?->body) }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('body')" />
    </div>

    <div>
        <x-input-label for="cover_image_url" value="Imagen de portada" />
        <x-text-input
            id="cover_image_url"
            name="cover_image_url"
            type="url"
            class="mt-1 block w-full"
            :value="old('cover_image_url', $post?->cover_image_url)"
            placeholder="https://..."
        />
        <x-input-error class="mt-2" :messages="$errors->get('cover_image_url')" />
    </div>

    <div>
        <x-input-label for="gallery_image_urls" value="Galería de imágenes" />
        <textarea
            id="gallery_image_urls"
            name="gallery_image_urls"
            rows="4"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            placeholder="https://imagen-1.jpg&#10;https://imagen-2.jpg"
        >{{ $galleryValue }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('gallery_image_urls')" />
    </div>

    <div class="rounded-md border border-gray-200 p-4">
        <h3 class="text-sm font-semibold text-gray-900">Botones de contacto</h3>

        @if ($integrations->isEmpty())
            <p class="mt-3 text-sm text-gray-500">
                Activa una integración para habilitar botones de contacto.
            </p>
        @else
            <div class="mt-4 grid gap-6">
                @if ($integrations->has('whatsapp'))
                    <div class="grid gap-6 sm:grid-cols-[120px_1fr]">
                        <div>
                            <x-input-label for="whatsapp_country_code" value="País WhatsApp" />
                            <x-text-input
                                id="whatsapp_country_code"
                                name="whatsapp_country_code"
                                type="text"
                                class="mt-1 block w-full"
                                :value="old('whatsapp_country_code', $post?->whatsapp_country_code ?: $defaultDialCode)"
                                placeholder="51"
                            />
                            <x-input-error class="mt-2" :messages="$errors->get('whatsapp_country_code')" />
                        </div>

                        <div>
                            <x-input-label for="whatsapp_number" value="Número WhatsApp" />
                            <x-text-input
                                id="whatsapp_number"
                                name="whatsapp_number"
                                type="text"
                                class="mt-1 block w-full"
                                :value="old('whatsapp_number', $post?->whatsapp_number)"
                                placeholder="999999999"
                            />
                            <x-input-error class="mt-2" :messages="$errors->get('whatsapp_number')" />
                        </div>
                    </div>
                @endif

                @if ($integrations->has('telegram'))
                    <div>
                        <x-input-label for="telegram_username" value="Usuario Telegram" />
                        <x-text-input
                            id="telegram_username"
                            name="telegram_username"
                            type="text"
                            class="mt-1 block w-full"
                            :value="old('telegram_username', $post?->telegram_username)"
                            placeholder="@usuario"
                        />
                        <x-input-error class="mt-2" :messages="$errors->get('telegram_username')" />
                    </div>
                @endif

                @if ($integrations->has('sms'))
                    <div class="grid gap-6 sm:grid-cols-[120px_1fr]">
                        <div>
                            <x-input-label for="sms_country_code" value="País SMS" />
                            <x-text-input
                                id="sms_country_code"
                                name="sms_country_code"
                                type="text"
                                class="mt-1 block w-full"
                                :value="old('sms_country_code', $post?->sms_country_code ?: $defaultDialCode)"
                                placeholder="51"
                            />
                            <x-input-error class="mt-2" :messages="$errors->get('sms_country_code')" />
                        </div>

                        <div>
                            <x-input-label for="sms_number" value="Número SMS" />
                            <x-text-input
                                id="sms_number"
                                name="sms_number"
                                type="text"
                                class="mt-1 block w-full"
                                :value="old('sms_number', $post?->sms_number)"
                                placeholder="999999999"
                            />
                            <x-input-error class="mt-2" :messages="$errors->get('sms_number')" />
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <div>
        <x-input-label for="tags" value="Tags" />
        <x-text-input
            id="tags"
            name="tags"
            type="text"
            class="mt-1 block w-full"
            :value="$tagsValue"
            placeholder="premium, verificado, lima"
        />
        <x-input-error class="mt-2" :messages="$errors->get('tags')" />
    </div>

    <section class="rounded-md border border-gray-200 p-4">
        <div class="flex items-center justify-between gap-4">
            <h3 class="text-sm font-semibold text-gray-900">Cards informativas</h3>
            <button
                type="button"
                class="inline-flex size-9 items-center justify-center rounded-md border border-gray-300 text-gray-900 transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2"
                title="Agregar card"
                aria-label="Agregar card"
                x-on:click="addCard()"
            >
                <x-heroicon-o-plus class="h-5 w-[18px]" aria-hidden="true" />
            </button>
        </div>

        <template x-if="cards.length === 0">
            <p class="mt-3 text-sm text-gray-500">Agrega cards como Perfil, Atención o cualquier dato extra del post.</p>
        </template>

        <div class="mt-4 space-y-4">
            <template x-for="(card, cardIndex) in cards" :key="cardIndex">
                <div class="rounded-md border border-gray-200 bg-gray-50 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="w-full">
                            <x-input-label value="Título de card" />
                            <x-text-input
                                type="text"
                                class="mt-1 block w-full"
                                x-model="card.title"
                                x-on:input="suggestCardColor(card)"
                                x-bind:name="`post_cards[${cardIndex}][title]`"
                                placeholder="Perfil"
                            />
                        </div>

                        <button
                            type="button"
                            class="mt-6 inline-flex size-9 shrink-0 items-center justify-center rounded-md border border-gray-300 text-gray-900 transition hover:bg-white focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2"
                            title="Eliminar card"
                            aria-label="Eliminar card"
                            x-on:click="removeCard(cardIndex)"
                        >
                            <x-heroicon-o-trash class="h-5 w-[18px]" aria-hidden="true" />
                        </button>
                    </div>

                    <label class="mt-3 inline-flex items-center gap-3">
                        <input type="hidden" value="0" x-bind:name="`post_cards[${cardIndex}][is_active]`">
                        <input
                            type="checkbox"
                            value="1"
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                            x-model="card.is_active"
                            x-bind:name="`post_cards[${cardIndex}][is_active]`"
                        >
                        <span class="text-sm font-medium text-gray-700">Visible</span>
                    </label>

                    <div class="mt-4">
                        <x-input-label value="Color de card" />
                        <div class="mt-1 flex gap-2">
                            <input
                                type="color"
                                class="h-10 w-12 rounded-md border border-gray-300 bg-white p-1"
                                x-bind:value="card.color || '#E91E63'"
                                x-on:input="setCardColor(card, $event.target.value)"
                            >
                            <x-text-input
                                type="text"
                                class="block w-full"
                                x-model="card.color"
                                x-on:input="card.colorTouched = true"
                                x-bind:name="`post_cards[${cardIndex}][color]`"
                                placeholder="#E91E63"
                            />
                        </div>

                        <template x-if="reusableCardColors.length > 0">
                            <div class="mt-3 flex flex-wrap gap-2">
                                <template x-for="color in reusableCardColors" :key="color">
                                    <button
                                        type="button"
                                        class="size-7 rounded-full border border-gray-300 shadow-sm transition hover:scale-105 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2"
                                        x-bind:style="`background-color: ${color}`"
                                        x-bind:title="color"
                                        x-on:click="setCardColor(card, color)"
                                    >
                                        <span class="sr-only" x-text="`Usar ${color}`"></span>
                                    </button>
                                </template>
                            </div>
                        </template>
                    </div>

                    <div class="mt-4 space-y-3">
                        <template x-for="(field, fieldIndex) in card.fields" :key="fieldIndex">
                            <div class="grid gap-3 sm:grid-cols-[1fr_1fr_auto]">
                                <x-text-input
                                    type="text"
                                    class="block w-full"
                                    x-model="field.key"
                                    x-bind:name="`post_cards[${cardIndex}][fields][${fieldIndex}][key]`"
                                    placeholder="Clave"
                                />
                                <x-text-input
                                    type="text"
                                    class="block w-full"
                                    x-model="field.value"
                                    x-bind:name="`post_cards[${cardIndex}][fields][${fieldIndex}][value]`"
                                    placeholder="Valor"
                                />
                                <button
                                    type="button"
                                    class="inline-flex size-10 items-center justify-center rounded-md border border-gray-300 text-gray-900 transition hover:bg-white focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2"
                                    title="Eliminar dato"
                                    aria-label="Eliminar dato"
                                    x-on:click="removeField(card, fieldIndex)"
                                >
                                    <x-heroicon-o-x-mark class="h-5 w-[18px]" aria-hidden="true" />
                                </button>
                            </div>
                        </template>
                    </div>

                    <button
                        type="button"
                        class="mt-4 inline-flex items-center gap-2 rounded-md border border-gray-300 px-3 py-2 text-sm font-medium text-gray-900 transition hover:bg-white focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2"
                        x-on:click="addField(card)"
                    >
                        <x-heroicon-o-plus class="h-5 w-[18px]" aria-hidden="true" />
                        Agregar dato
                    </button>
                </div>
            </template>
        </div>

        <x-input-error class="mt-2" :messages="$errors->get('post_cards')" />
    </section>

    <div class="flex items-end">
        <label for="is_active" class="inline-flex items-center gap-3 rounded-md border border-gray-200 px-4 py-3">
            <input
                id="is_active"
                name="is_active"
                type="checkbox"
                value="1"
                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                @checked(old('is_active', $post?->is_active ?? true))
            >
            <span class="text-sm font-medium text-gray-700">Publicado</span>
        </label>
    </div>

    <div class="grid gap-6 sm:grid-cols-3">
        <div>
            <x-input-label for="publish_mode" value="Tipo de publicación" />
            <select
                id="publish_mode"
                name="publish_mode"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                x-model="publishMode"
                required
            >
                <option value="immediate">Inmediata</option>
                <option value="scheduled">Programada</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('publish_mode')" />
        </div>

        <div x-show="publishMode === 'scheduled'" x-cloak>
            <x-input-label for="published_at" value="Fecha y hora de publicación" />
            <x-text-input
                id="published_at"
                name="published_at"
                type="datetime-local"
                class="mt-1 block w-full"
                :value="$publishedAtValue"
                x-bind:required="publishMode === 'scheduled'"
            />
            <x-input-error class="mt-2" :messages="$errors->get('published_at')" />
        </div>

        <div>
            <x-input-label for="ends_at" value="Fecha y hora de finalización" />
            <x-text-input
                id="ends_at"
                name="ends_at"
                type="datetime-local"
                class="mt-1 block w-full"
                :value="$endsAtValue"
            />
            <x-input-error class="mt-2" :messages="$errors->get('ends_at')" />
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('posts.index') }}" class="admin-button-cancel">
            Cancelar
        </a>
        <x-primary-button>
            {{ $submitLabel }}
        </x-primary-button>
    </div>
</form>
