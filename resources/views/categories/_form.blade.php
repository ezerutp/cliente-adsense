@php
    $category = $category ?? null;
    $method = $method ?? 'POST';
    $submitLabel = $submitLabel ?? 'Guardar';
@endphp

<form method="POST" action="{{ $action }}" class="space-y-6">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div>
        <x-input-label for="name" value="Nombre" />
        <x-text-input
            id="name"
            name="name"
            type="text"
            class="mt-1 block w-full"
            :value="old('name', $category?->name)"
            required
            autofocus
        />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>

    <div>
        <x-input-label for="slug" value="Slug" />
        <x-text-input
            id="slug"
            name="slug"
            type="text"
            class="mt-1 block w-full"
            :value="old('slug', $category?->slug)"
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
        >{{ old('description', $category?->description) }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('description')" />
    </div>

    <div>
        <x-input-label for="image_url" value="URL de imagen" />
        <x-text-input
            id="image_url"
            name="image_url"
            type="url"
            class="mt-1 block w-full"
            :value="old('image_url', $category?->image_url)"
            placeholder="https://..."
        />
        <x-input-error class="mt-2" :messages="$errors->get('image_url')" />
    </div>

    <div class="grid gap-6 sm:grid-cols-2">
        <div>
            <x-input-label for="sort_order" value="Orden" />
            <x-text-input
                id="sort_order"
                name="sort_order"
                type="number"
                min="0"
                class="mt-1 block w-full"
                :value="old('sort_order', $category?->sort_order ?? 0)"
                required
            />
            <x-input-error class="mt-2" :messages="$errors->get('sort_order')" />
        </div>

        <div class="flex items-end">
            <label for="is_active" class="inline-flex items-center gap-3 rounded-md border border-gray-200 px-4 py-3">
                <input
                    id="is_active"
                    name="is_active"
                    type="checkbox"
                    value="1"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                    @checked(old('is_active', $category?->is_active ?? true))
                >
                <span class="text-sm font-medium text-gray-700">Activa en portada</span>
            </label>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('categories.index') }}" class="admin-button-cancel">
            Cancelar
        </a>
        <x-primary-button>
            {{ $submitLabel }}
        </x-primary-button>
    </div>
</form>
