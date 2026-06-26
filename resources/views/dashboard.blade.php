<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Dashboard
                </h2>
                <p class="mt-1 text-sm text-gray-500">Resumen operativo del contenido público.</p>
            </div>
            @can('posts.create')
                <a
                    href="{{ route('posts.create') }}"
                    class="inline-flex size-9 items-center justify-center rounded-md border border-gray-300 text-gray-900 transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2"
                    title="Crear post"
                    aria-label="Crear post"
                >
                    <x-heroicon-o-plus class="h-5 w-[18px]" aria-hidden="true" />
                    <span class="sr-only">Crear post</span>
                </a>
            @endcan
        </div>
    </x-slot>

    @php
        $toneClasses = [
            'amber' => 'bg-amber-50 text-amber-700 ring-amber-100',
            'blue' => 'bg-blue-50 text-blue-700 ring-blue-100',
            'gray' => 'bg-gray-50 text-gray-700 ring-gray-100',
            'green' => 'bg-green-50 text-green-700 ring-green-100',
            'indigo' => 'bg-indigo-50 text-indigo-700 ring-indigo-100',
            'pink' => 'bg-pink-50 text-pink-700 ring-pink-100',
            'red' => 'bg-red-50 text-red-700 ring-red-100',
            'yellow' => 'bg-yellow-50 text-yellow-700 ring-yellow-100',
        ];
    @endphp

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <div class="grid gap-4 px-4 sm:grid-cols-2 sm:px-0 lg:grid-cols-5">
                @foreach ($metrics as $metric)
                    <a
                        href="{{ $metric['href'] }}"
                        class="block rounded-lg border border-gray-200 bg-white p-4 shadow-sm transition hover:border-gray-300 hover:shadow focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-gray-500">{{ $metric['label'] }}</p>
                                <p class="mt-2 text-3xl font-semibold tracking-normal text-gray-900">
                                    {{ number_format($metric['value']) }}
                                </p>
                            </div>
                            <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-md ring-1 {{ $toneClasses[$metric['tone']] ?? $toneClasses['gray'] }}">
                                <x-dynamic-component :component="$metric['icon']" class="size-5" aria-hidden="true" />
                            </span>
                        </div>
                        <p class="mt-3 truncate text-xs font-medium text-gray-500">{{ $metric['detail'] }}</p>
                    </a>
                @endforeach
            </div>

            <div class="grid gap-6 px-4 sm:px-0 lg:grid-cols-2">
                <section class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-5 py-4">
                        <h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500">Top categorías</h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse ($topCategories as $category)
                            <a href="{{ route('categories.index') }}" class="flex items-center justify-between gap-4 px-5 py-4 transition hover:bg-gray-50">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-medium text-gray-900">{{ $category->name }}</p>
                                    <p class="mt-1 text-xs text-gray-500">{{ $category->slug }}</p>
                                </div>
                                <span class="shrink-0 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700">
                                    {{ $category->public_posts_count }} posts
                                </span>
                            </a>
                        @empty
                            <p class="px-5 py-8 text-sm text-gray-500">No hay categorías activas con actividad pública.</p>
                        @endforelse
                    </div>
                </section>

                <section class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-5 py-4">
                        <h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500">Top ubicaciones</h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse ($topLocations as $location)
                            <a href="{{ route('settings.edit') }}#locations" class="flex items-center justify-between gap-4 px-5 py-4 transition hover:bg-gray-50">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-medium text-gray-900">{{ $location->location }}</p>
                                    <p class="mt-1 text-xs text-gray-500">Ubicación con actividad pública</p>
                                </div>
                                <span class="shrink-0 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700">
                                    {{ $location->posts_count }} posts
                                </span>
                            </a>
                        @empty
                            <p class="px-5 py-8 text-sm text-gray-500">No hay ubicaciones con posts públicos.</p>
                        @endforelse
                    </div>
                </section>
            </div>

            <div class="grid gap-6 px-4 sm:px-0 lg:grid-cols-3">
                <section class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm lg:col-span-2">
                    <div class="flex items-center justify-between gap-4 border-b border-gray-200 px-5 py-4">
                        <h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500">Últimos posts publicados</h3>
                        @can('posts.view')
                            <a href="{{ route('posts.index') }}" class="text-sm font-medium text-gray-700 hover:text-gray-900">Ver todos</a>
                        @endcan
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Post</th>
                                    <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Categoría</th>
                                    <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Publicado</th>
                                    <th class="px-5 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @forelse ($latestPosts as $post)
                                    <tr>
                                        <td class="max-w-0 px-5 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="size-11 shrink-0 overflow-hidden rounded-md bg-gray-100">
                                                    @if ($post->cover_image_url)
                                                        <img src="{{ $post->cover_image_url }}" alt="{{ $post->title }}" class="h-full w-full object-cover">
                                                    @endif
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="truncate text-sm font-medium text-gray-900">{{ $post->title }}</p>
                                                    <p class="mt-1 truncate text-xs text-gray-500">{{ $post->location }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 text-sm text-gray-600">{{ $post->category?->name ?? 'General' }}</td>
                                        <td class="px-5 py-4 text-sm text-gray-600">{{ $post->published_at?->format('d/m/Y H:i') ?? $post->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="px-5 py-4 text-right">
                                            @if ($post->is_vip)
                                                <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800">VIP</span>
                                            @else
                                                <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-800">Público</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-5 py-8 text-sm text-gray-500">No hay posts públicos todavía.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-5 py-4">
                        <h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500">Alertas</h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @foreach ($alerts as $alert)
                            <a href="{{ $alert['href'] }}" class="flex items-center justify-between gap-4 px-5 py-4 transition hover:bg-gray-50">
                                <span class="min-w-0 truncate text-sm font-medium text-gray-700">{{ $alert['label'] }}</span>
                                <span class="shrink-0 rounded-full {{ $alert['value'] > 0 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }} px-2.5 py-1 text-xs font-semibold">
                                    {{ number_format($alert['value']) }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </section>
            </div>

            <div class="px-4 sm:px-0">
                <section class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-5 py-4">
                        <h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500">Próximos vencimientos</h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse ($upcomingExpirations as $post)
                            <a href="{{ route('posts.index') }}" class="flex items-center justify-between gap-4 px-5 py-4 transition hover:bg-gray-50">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-medium text-gray-900">{{ $post->title }}</p>
                                    <p class="mt-1 truncate text-xs text-gray-500">{{ $post->category?->name ?? 'General' }} · {{ $post->location }}</p>
                                </div>
                                <span class="shrink-0 rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-800">
                                    {{ $post->ends_at->format('d/m/Y H:i') }}
                                </span>
                            </a>
                        @empty
                            <p class="px-5 py-8 text-sm text-gray-500">No hay posts públicos por vencer en los próximos 7 días.</p>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
