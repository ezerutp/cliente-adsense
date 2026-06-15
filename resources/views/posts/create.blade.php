<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Crear post
            </h2>
            <a href="{{ route('posts.index') }}" class="admin-button-cancel">
                Ver lista
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @include('posts._form', [
                        'action' => route('posts.store'),
                        'categories' => $categories,
                        'integrations' => $integrations,
                        'submitLabel' => 'Crear post',
                    ])
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
