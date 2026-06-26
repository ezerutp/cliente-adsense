<?php

namespace App\Http\Controllers;

use App\Models\AgeGateSetting;
use App\Models\SiteSetting;
use App\Models\VideoPost;
use App\Support\VideoIframe;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class VideoPostController extends Controller
{
    public function publicIndex(): View
    {
        $ageGate = AgeGateSetting::current()->toModalContent();
        $siteSettings = SiteSetting::current();
        $videos = VideoPost::query()
            ->publiclyVisible()
            ->orderBy('sort_order')
            ->latest('published_at')
            ->latest()
            ->paginate(12);

        return view('video-posts.public-index', compact('ageGate', 'siteSettings', 'videos'));
    }

    public function index(): View
    {
        $videoPosts = VideoPost::query()
            ->orderBy('sort_order')
            ->latest()
            ->paginate(12);

        return view('video-posts.index', compact('videoPosts'));
    }

    public function create(): View
    {
        return view('video-posts.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? $data['title']);

        VideoPost::create($data);

        return redirect()
            ->route('video-posts.index')
            ->with('status', 'Video creado correctamente.');
    }

    public function edit(VideoPost $videoPost): View
    {
        return view('video-posts.edit', compact('videoPost'));
    }

    public function update(Request $request, VideoPost $videoPost): RedirectResponse
    {
        $data = $this->validatedData($request, $videoPost);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? $data['title'], $videoPost);

        $videoPost->update($data);

        return redirect()
            ->route('video-posts.index')
            ->with('status', 'Video actualizado correctamente.');
    }

    public function destroy(VideoPost $videoPost): RedirectResponse
    {
        $videoPost->delete();

        return redirect()
            ->route('video-posts.index')
            ->with('status', 'Video eliminado correctamente.');
    }

    public function toggleVisibility(VideoPost $videoPost): RedirectResponse
    {
        $videoPost->update([
            'is_active' => ! $videoPost->is_active,
        ]);

        $status = $videoPost->is_active
            ? 'Video publicado correctamente.'
            : 'Video ocultado correctamente.';

        return redirect()
            ->route('video-posts.index')
            ->with('status', $status);
    }

    /**
     * @return array{title: string, slug?: string|null, description?: string|null, iframe_src: string, thumbnail_url?: string|null, sort_order: int, is_active: bool, published_at: Carbon|null}
     */
    private function validatedData(Request $request, ?VideoPost $videoPost = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('video_posts', 'slug')->ignore($videoPost),
            ],
            'description' => ['nullable', 'string', 'max:2000'],
            'iframe' => ['required', 'string', 'max:5000'],
            'thumbnail_url' => ['nullable', 'url:http,https', 'max:2048'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:999999'],
            'published_at' => ['nullable', 'date'],
        ]);

        $iframeSrc = VideoIframe::srcFromInput($data['iframe']);

        if ($iframeSrc === null) {
            throw ValidationException::withMessages([
                'iframe' => 'Ingresa un iframe válido con src http o https.',
            ]);
        }

        return [
            'title' => trim($data['title']),
            'slug' => filled($data['slug'] ?? null) ? trim($data['slug']) : null,
            'description' => filled($data['description'] ?? null) ? trim($data['description']) : null,
            'iframe_src' => $iframeSrc,
            'thumbnail_url' => filled($data['thumbnail_url'] ?? null) ? trim($data['thumbnail_url']) : null,
            'sort_order' => (int) $data['sort_order'],
            'is_active' => $request->boolean('is_active'),
            'published_at' => filled($data['published_at'] ?? null) ? Carbon::parse($data['published_at']) : now(),
        ];
    }

    private function uniqueSlug(string $value, ?VideoPost $videoPost = null): string
    {
        $slug = Str::slug($value) ?: Str::random(8);
        $baseSlug = $slug;
        $counter = 2;

        while (
            VideoPost::query()
                ->where('slug', $slug)
                ->when($videoPost, fn ($query) => $query->whereKeyNot($videoPost->getKey()))
                ->exists()
        ) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
