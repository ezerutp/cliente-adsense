@php
    $videoPost = $videoPost ?? null;
    $method = $method ?? 'POST';
    $submitLabel = $submitLabel ?? 'Guardar video';
    $publishedAtValue = old('published_at', $videoPost?->published_at?->format('Y-m-d\TH:i'));
@endphp

<form method="POST" action="{{ $action }}" class="space-y-6">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div>
        <x-input-label for="title" value="Título" />
        <x-text-input
            id="title"
            name="title"
            type="text"
            class="mt-1 block w-full"
            :value="old('title', $videoPost?->title)"
            required
            autofocus
        />
        <x-input-error class="mt-2" :messages="$errors->get('title')" />
    </div>

    <div>
        <x-input-label for="slug" value="Slug" />
        <x-text-input
            id="slug"
            name="slug"
            type="text"
            class="mt-1 block w-full"
            :value="old('slug', $videoPost?->slug)"
            placeholder="Se genera automáticamente si lo dejas vacío"
        />
        <x-input-error class="mt-2" :messages="$errors->get('slug')" />
    </div>

    <div>
        <x-input-label for="description" value="Descripción" />
        <textarea
            id="description"
            name="description"
            rows="4"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        >{{ old('description', $videoPost?->description) }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('description')" />
    </div>

    <div>
        <x-input-label for="iframe" value="Iframe del video" />
        <textarea
            id="iframe"
            name="iframe"
            rows="5"
            class="mt-1 block w-full rounded-md border-gray-300 font-mono text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            placeholder='<iframe src="https://www.youtube.com/embed/..." allowfullscreen></iframe>'
            required
        >{{ old('iframe', $videoPost?->iframe_src) }}</textarea>
        <p class="mt-2 text-xs text-gray-500">Puedes pegar el iframe completo o solo la URL del src. Se guardará únicamente la URL validada.</p>
        <x-input-error class="mt-2" :messages="$errors->get('iframe')" />
    </div>

    <div class="grid gap-6 sm:grid-cols-3">
        <div>
            <x-input-label for="sort_order" value="Orden" />
            <x-text-input
                id="sort_order"
                name="sort_order"
                type="number"
                min="0"
                class="mt-1 block w-full"
                :value="old('sort_order', $videoPost?->sort_order ?? 0)"
                required
            />
            <x-input-error class="mt-2" :messages="$errors->get('sort_order')" />
        </div>

        <div>
            <x-input-label for="published_at" value="Fecha de publicación" />
            <x-text-input
                id="published_at"
                name="published_at"
                type="datetime-local"
                class="mt-1 block w-full"
                :value="$publishedAtValue"
            />
            <x-input-error class="mt-2" :messages="$errors->get('published_at')" />
        </div>

        <div class="flex items-end">
            <label for="is_active" class="inline-flex items-center gap-3 rounded-md border border-gray-200 px-4 py-3">
                <input
                    id="is_active"
                    name="is_active"
                    type="checkbox"
                    value="1"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                    @checked(old('is_active', $videoPost?->is_active ?? true))
                >
                <span class="text-sm font-medium text-gray-700">Publicado</span>
            </label>
        </div>
    </div>

    @if ($videoPost?->iframe_src)
        <div class="rounded-lg border border-gray-200 p-4">
            <h3 class="text-sm font-semibold text-gray-900">Vista previa</h3>
            <div class="mt-4 aspect-video overflow-hidden rounded-md bg-gray-100">
                <iframe
                    src="{{ $videoPost->iframe_src }}"
                    title="{{ $videoPost->title }}"
                    class="h-full w-full"
                    loading="lazy"
                    allowfullscreen
                ></iframe>
            </div>
        </div>
    @endif

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('video-posts.index') }}" class="admin-button-cancel">
            Cancelar
        </a>
        <x-primary-button>
            {{ $submitLabel }}
        </x-primary-button>
    </div>
</form>
