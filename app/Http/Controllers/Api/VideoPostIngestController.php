<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VideoPost;
use App\Support\VideoIframe;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class VideoPostIngestController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->validate([
            'admin_email' => ['required', 'email'],
            'admin_password' => ['required', 'string'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'iframe' => ['required', 'string', 'max:5000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999999'],
            'is_active' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        $admin = User::query()->where('email', $data['admin_email'])->first();

        if (! $admin || ! $admin->hasRole('admin') || ! Hash::check($data['admin_password'], $admin->password)) {
            throw ValidationException::withMessages([
                'admin_password' => 'Las credenciales de administrador no son válidas.',
            ]);
        }

        $iframeSrc = VideoIframe::srcFromInput($data['iframe']);

        if ($iframeSrc === null) {
            throw ValidationException::withMessages([
                'iframe' => 'Ingresa un iframe válido con src http o https.',
            ]);
        }

        $videoPost = VideoPost::create([
            'title' => trim($data['title']),
            'slug' => $this->uniqueSlug($data['slug'] ?? $data['title']),
            'description' => filled($data['description'] ?? null) ? trim($data['description']) : null,
            'iframe_src' => $iframeSrc,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_active' => array_key_exists('is_active', $data) ? $request->boolean('is_active') : true,
            'published_at' => filled($data['published_at'] ?? null) ? Carbon::parse($data['published_at']) : now(),
        ]);

        return response()->json([
            'data' => [
                'id' => $videoPost->id,
                'title' => $videoPost->title,
                'slug' => $videoPost->slug,
                'description' => $videoPost->description,
                'iframe_src' => $videoPost->iframe_src,
                'is_active' => $videoPost->is_active,
                'published_at' => $videoPost->published_at?->toJSON(),
            ],
        ], 201);
    }

    private function uniqueSlug(string $value): string
    {
        $slug = Str::slug($value) ?: Str::random(8);
        $baseSlug = $slug;
        $counter = 2;

        while (VideoPost::query()->where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
