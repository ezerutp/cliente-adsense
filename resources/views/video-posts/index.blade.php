<x-app-layout>
    @php
        $tampermonkeyScript = file_get_contents(base_path('scripts/tampermonkey/xvideos-video-ingest.user.js'));
        $defaultScriptEndpoint = route('api.video-posts.store');
        $defaultAdminEmail = auth()->user()?->email ?? '';
    @endphp

    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Videos
            </h2>
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    class="inline-flex h-9 items-center gap-2 rounded-full border border-gray-300 bg-white px-3 text-xs font-semibold uppercase tracking-wider text-gray-800 transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2"
                    x-data
                    x-on:click="$dispatch('open-modal', 'copy-xvideos-script')"
                >
                    <x-heroicon-o-clipboard-document class="size-4" aria-hidden="true" />
                    Copiar script
                </button>

                @can('videos.create')
                    <a
                        href="{{ route('video-posts.create') }}"
                        class="inline-flex size-9 items-center justify-center rounded-md border border-gray-300 text-gray-900 transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2"
                        title="Crear video"
                        aria-label="Crear video"
                    >
                        <x-heroicon-o-plus class="h-5 w-[18px]" aria-hidden="true" />
                        <span class="sr-only">Crear video</span>
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 rounded-md bg-green-50 p-4 text-sm font-medium text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Video</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Slug</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Publicación</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Estado</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($videoPosts as $videoPost)
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-4">
                                            <div class="aspect-video w-28 shrink-0 overflow-hidden rounded-md bg-gray-100">
                                                <iframe
                                                    src="{{ $videoPost->iframe_src }}"
                                                    title="{{ $videoPost->title }}"
                                                    class="h-full w-full"
                                                    loading="lazy"
                                                ></iframe>
                                            </div>
                                            <div class="min-w-0">
                                                <div class="truncate font-medium text-gray-900">{{ $videoPost->title }}</div>
                                                @if ($videoPost->description)
                                                    <div class="mt-1 max-w-md truncate text-sm text-gray-500">{{ $videoPost->description }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $videoPost->slug }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $videoPost->published_at?->format('d/m/Y H:i') ?? 'Sin fecha' }}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $videoPost->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">
                                            {{ $videoPost->is_active ? 'Publicado' : 'Oculto' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-medium">
                                        <div class="flex justify-end gap-2">
                                            @can('videos.publish')
                                                <button
                                                    type="button"
                                                    class="inline-flex size-9 items-center justify-center rounded-md border border-gray-300 text-gray-900 transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2"
                                                    title="{{ $videoPost->is_active ? 'Ocultar' : 'Publicar' }}"
                                                    aria-label="{{ $videoPost->is_active ? 'Ocultar video' : 'Publicar video' }}"
                                                    x-data
                                                    x-on:click="$dispatch('open-modal', 'toggle-video-{{ $videoPost->id }}')"
                                                >
                                                    @if ($videoPost->is_active)
                                                        <x-heroicon-o-eye-slash class="h-5 w-[18px]" aria-hidden="true" />
                                                    @else
                                                        <x-heroicon-o-eye class="h-5 w-[18px]" aria-hidden="true" />
                                                    @endif
                                                    <span class="sr-only">{{ $videoPost->is_active ? 'Ocultar video' : 'Publicar video' }}</span>
                                                </button>
                                            @endcan

                                            @can('videos.edit')
                                                <a
                                                    href="{{ route('video-posts.edit', $videoPost) }}"
                                                    class="inline-flex size-9 items-center justify-center rounded-md border border-gray-300 text-gray-900 transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2"
                                                    title="Editar"
                                                    aria-label="Editar video"
                                                >
                                                    <x-heroicon-o-pencil-square class="h-5 w-[18px]" aria-hidden="true" />
                                                    <span class="sr-only">Editar video</span>
                                                </a>
                                            @endcan

                                            @can('videos.delete')
                                                <button
                                                    type="button"
                                                    class="inline-flex size-9 items-center justify-center rounded-md border border-gray-300 text-gray-900 transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2"
                                                    title="Eliminar"
                                                    aria-label="Eliminar video"
                                                    x-data
                                                    x-on:click="$dispatch('open-modal', 'delete-video-{{ $videoPost->id }}')"
                                                >
                                                    <x-heroicon-o-trash class="h-5 w-[18px]" aria-hidden="true" />
                                                    <span class="sr-only">Eliminar video</span>
                                                </button>
                                            @endcan
                                        </div>

                                        @can('videos.publish')
                                            <x-category-action-modal
                                                name="toggle-video-{{ $videoPost->id }}"
                                                :title="$videoPost->is_active ? 'Ocultar video' : 'Publicar video'"
                                                :description="$videoPost->is_active ? 'Este video dejará de mostrarse en la galería pública.' : 'Este video volverá a mostrarse en la galería pública.'"
                                                :action="route('video-posts.toggle-visibility', $videoPost)"
                                                method="PATCH"
                                                :confirm-label="$videoPost->is_active ? 'Ocultar' : 'Publicar'"
                                                :variant="$videoPost->is_active ? 'primary' : 'success'"
                                            />
                                        @endcan

                                        @can('videos.delete')
                                            <x-category-action-modal
                                                name="delete-video-{{ $videoPost->id }}"
                                                title="Eliminar video"
                                                description="Esta acción eliminará el video permanentemente."
                                                :action="route('video-posts.destroy', $videoPost)"
                                                method="DELETE"
                                                confirm-label="Eliminar"
                                                variant="danger"
                                            />
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center gap-3">
                                            <x-heroicon-o-video-camera class="h-12 w-12 text-gray-400" />
                                            <p class="text-sm font-medium text-gray-500">No hay videos creados</p>
                                            @can('videos.create')
                                                <a
                                                    href="{{ route('video-posts.create') }}"
                                                    class="mt-2 inline-flex items-center gap-2 rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                                >
                                                    <x-heroicon-o-plus class="h-5 w-5" />
                                                    Crear primer video
                                                </a>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6">
                {{ $videoPosts->links() }}
            </div>
        </div>
    </div>

    <x-modal name="copy-xvideos-script" focusable>
        <div
            class="p-6"
            x-data="{
                endpoint: @js($defaultScriptEndpoint),
                email: @js($defaultAdminEmail),
                password: '',
                copied: false,
                error: '',
                scriptTemplate: @js($tampermonkeyScript),
                configuredScript() {
                    return this.scriptTemplate
                        .replace(/const DEFAULT_ENDPOINT = '[^']*';/, `const DEFAULT_ENDPOINT = ${JSON.stringify(this.endpoint)};`)
                        .replace(/const DEFAULT_ADMIN_EMAIL = '[^']*';/, `const DEFAULT_ADMIN_EMAIL = ${JSON.stringify(this.email)};`)
                        .replace(/const DEFAULT_ADMIN_PASSWORD = '[^']*';/, `const DEFAULT_ADMIN_PASSWORD = ${JSON.stringify(this.password)};`);
                },
                async copyScript() {
                    this.error = '';
                    this.copied = false;

                    if (! this.endpoint || ! this.email || ! this.password) {
                        this.error = 'Completa endpoint, correo y contraseña antes de copiar.';
                        return;
                    }

                    try {
                        const script = this.configuredScript();

                        if (navigator.clipboard?.writeText) {
                            await navigator.clipboard.writeText(script);
                        } else {
                            this.fallbackCopy(script);
                        }

                        this.copied = true;
                    } catch (error) {
                        this.error = 'No se pudo copiar automáticamente. Revisa permisos del navegador.';
                    }
                },
                fallbackCopy(text) {
                    const textarea = document.createElement('textarea');
                    textarea.value = text;
                    textarea.style.position = 'fixed';
                    textarea.style.left = '-9999px';
                    document.body.appendChild(textarea);
                    textarea.focus();
                    textarea.select();
                    document.execCommand('copy');
                    textarea.remove();
                }
            }"
        >
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-medium text-gray-900">Copiar script de Xvideos</h2>
                    <p class="mt-1 text-sm text-gray-500">Configura los datos del endpoint y copia el userscript listo para pegar en Tampermonkey.</p>
                </div>
                <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700">
                    Tampermonkey
                </span>
            </div>

            <div class="mt-6 space-y-4">
                <div>
                    <x-input-label for="script_endpoint" value="Endpoint" />
                    <x-text-input
                        id="script_endpoint"
                        type="url"
                        class="mt-1 block w-full"
                        x-model="endpoint"
                        required
                    />
                </div>

                <div>
                    <x-input-label for="script_email" value="Correo admin" />
                    <x-text-input
                        id="script_email"
                        type="email"
                        class="mt-1 block w-full"
                        x-model="email"
                        required
                    />
                </div>

                <div>
                    <x-input-label for="script_password" value="Contraseña admin" />
                    <x-text-input
                        id="script_password"
                        type="password"
                        class="mt-1 block w-full"
                        x-model="password"
                        required
                    />
                    <p class="mt-2 text-xs text-gray-500">POC: la contraseña quedará dentro del script copiado y en el storage local de Tampermonkey cuando se use.</p>
                </div>

                <template x-if="copied">
                    <div class="rounded-md bg-green-50 p-3 text-sm font-medium text-green-800">
                        Script copiado al portapapeles.
                    </div>
                </template>

                <template x-if="error">
                    <div class="rounded-md bg-red-50 p-3 text-sm font-medium text-red-800" x-text="error"></div>
                </template>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button
                    type="button"
                    class="admin-button-cancel"
                    x-on:click="$dispatch('close')"
                >
                    Cancelar
                </button>
                <button
                    type="button"
                    class="admin-button-primary"
                    x-on:click="copyScript()"
                >
                    Copiar
                </button>
            </div>
        </div>
    </x-modal>
</x-app-layout>
