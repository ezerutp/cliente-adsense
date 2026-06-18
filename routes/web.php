<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\IntegrationController;
use App\Http\Controllers\PostCardController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicPostBrowseController;
use App\Http\Controllers\SiteSettingController;
use App\Models\AgeGateSetting;
use App\Models\Category;
use App\Models\Integration;
use App\Models\Post;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

Route::get('/', function () {
    $categories = [];
    $latestPublications = collect();
    $premiumListings = collect();
    $ageGate = AgeGateSetting::current()->toModalContent();
    $siteSettings = SiteSetting::current();

    if (Schema::hasTable('categories')) {
        $categories = Category::query()
            ->where('is_active', true)
            ->withCount([
                'posts' => fn ($query) => $query->publiclyVisible(),
            ])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(fn (Category $category): array => [
                'name' => $category->name,
                'count' => trans_choice('{0} 0 anuncios|{1} 1 anuncio|[2,*] :count anuncios', $category->posts_count),
                'href' => route('categories.public.show', ['category' => $category->slug]),
                'image' => $category->image_url ?: 'https://www.skokka.com.pe/static/assets/womenseekmen_repr.09bc5c7b4ed7201892f6.jpg',
            ])
            ->all();
    }

    if (Schema::hasTable('posts')) {
        $premiumListings = Post::query()
            ->with('category')
            ->whereHas('category', fn ($query) => $query->where('is_active', true))
            ->publiclyVisible()
            ->where('is_vip', true)
            ->latest('published_at')
            ->latest('created_at')
            ->get()
            ->map(function (Post $post) use ($siteSettings): array {
                $tags = collect($post->tags ?? [])->map(fn (string $tag): string => mb_strtolower($tag));
                $category = $post->category;

                return [
                    'id' => 'post-'.$post->id,
                    'title' => $post->title,
                    'city' => $siteSettings->server_country,
                    'category' => $category?->name ?? 'General',
                    'updated' => $post->published_at ? 'Publicado '.$post->published_at->diffForHumans() : 'Publicado recientemente',
                    'price' => null,
                    'image' => $post->cover_image_url ?: 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=900&q=80',
                    'profileHref' => $category && $post->slug
                        ? route('posts.public.show', ['category' => $category->slug, 'post' => $post->slug])
                        : '#',
                    'profileLabel' => 'Ver post',
                    'verified' => $tags->contains('verificado'),
                    'featured' => true,
                ];
            });

        $latestPublications = Post::query()
            ->with('category')
            ->whereHas('category', fn ($query) => $query->where('is_active', true))
            ->publiclyVisible()
            ->latest('published_at')
            ->latest('created_at')
            ->get()
            ->map(function (Post $post) use ($siteSettings): array {
                $tags = collect($post->tags ?? [])->map(fn (string $tag): string => mb_strtolower($tag));
                $category = $post->category;

                return [
                    'title' => $post->title,
                    'city' => $siteSettings->server_country,
                    'category' => $category?->name ?? 'General',
                    'time' => $post->published_at ? $post->published_at->diffForHumans() : 'Reciente',
                    'href' => $category && $post->slug
                        ? route('posts.public.show', ['category' => $category->slug, 'post' => $post->slug])
                        : '#',
                    'verified' => $tags->contains('verificado'),
                    'verifiedLabel' => 'Verificado',
                ];
            });
    }

    return view('welcome', compact('ageGate', 'categories', 'latestPublications', 'premiumListings', 'siteSettings'));
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::patch('dashboard/categories/{category}/toggle-visibility', [CategoryController::class, 'toggleVisibility'])
        ->name('categories.toggle-visibility');

    Route::resource('dashboard/categories', CategoryController::class)
        ->except('show')
        ->names('categories');

    Route::patch('dashboard/posts/{post}/toggle-visibility', [PostController::class, 'toggleVisibility'])
        ->name('posts.toggle-visibility');
    Route::patch('dashboard/posts/{post}/toggle-vip', [PostController::class, 'toggleVip'])
        ->name('posts.toggle-vip');

    Route::resource('dashboard/posts', PostController::class)
        ->except('show')
        ->names('posts');

    Route::patch('dashboard/post-cards/{postCard}/toggle-visibility', [PostCardController::class, 'toggleVisibility'])
        ->name('post-cards.toggle-visibility');

    Route::resource('dashboard/post-cards', PostCardController::class)
        ->except('show')
        ->names('post-cards');

    Route::patch('dashboard/integrations/{integration}/toggle-visibility', [IntegrationController::class, 'toggleVisibility'])
        ->name('integrations.toggle-visibility');

    Route::resource('dashboard/integrations', IntegrationController::class)
        ->except('show')
        ->names('integrations');

    Route::get('dashboard/settings', [SiteSettingController::class, 'edit'])
        ->name('settings.edit');
    Route::put('dashboard/settings', [SiteSettingController::class, 'update'])
        ->name('settings.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/u', [PublicPostBrowseController::class, 'locations'])
    ->name('posts.locations.index');
Route::get('/u/{location}', [PublicPostBrowseController::class, 'location'])
    ->name('posts.locations.show');
Route::get('/t', [PublicPostBrowseController::class, 'tags'])
    ->name('posts.tags.index');
Route::get('/t/{tag}', [PublicPostBrowseController::class, 'tag'])
    ->name('posts.tags.show');

Route::get('/{category:slug}/{post:slug}', function (Category $category, Post $post) {
    abort_unless($category->is_active, 404);
    abort_unless($post->category_id === $category->id && $post->isPubliclyVisible(), 404);

    $ageGate = AgeGateSetting::current()->toModalContent();
    $siteSettings = SiteSetting::current();
    $tags = collect($post->tags ?? [])->map(fn (string $tag): string => mb_strtolower($tag));
    $postCards = $post->cards()->active()->get();
    $postContactUrls = [
        'whatsapp' => $post->whatsapp_url,
        'telegram' => $post->telegram_url,
        'sms' => $post->sms_url,
    ];
    $contactButtons = Integration::query()
        ->where('is_active', true)
        ->orderBy('name')
        ->get()
        ->map(function (Integration $integration) use ($postContactUrls): array {
            $href = $integration->provider === 'custom'
                ? $integration->base_url
                : ($postContactUrls[$integration->provider] ?? null);

            return [
                'label' => $integration->name,
                'href' => $href,
                'icon' => $integration->icon ?: (Integration::DEFAULT_ICONS[$integration->provider] ?? Integration::DEFAULT_ICONS['custom']),
                'button_color' => $integration->button_color ?: '#222222',
                'external' => $href && ! str_starts_with($href, 'sms:'),
            ];
        })
        ->filter(fn (array $contact): bool => filled($contact['href']))
        ->values();
    $relatedPosts = Post::query()
        ->whereBelongsTo($category)
        ->whereKeyNot($post->getKey())
        ->publiclyVisible()
        ->latest('published_at')
        ->latest('created_at')
        ->limit(3)
        ->get()
        ->map(function (Post $relatedPost) use ($category, $siteSettings): array {
            $relatedTags = collect($relatedPost->tags ?? [])->map(fn (string $tag): string => mb_strtolower($tag));

            return [
                'title' => $relatedPost->title,
                'city' => $siteSettings->server_country,
                'category' => $category->name,
                'updated' => $relatedPost->published_at ? 'Publicado '.$relatedPost->published_at->diffForHumans() : 'Publicado recientemente',
                'price' => null,
                'image' => $relatedPost->cover_image_url ?: 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=900&q=80',
                'profileHref' => $relatedPost->slug ? route('posts.public.show', ['category' => $category->slug, 'post' => $relatedPost->slug]) : '#',
                'profileLabel' => 'Ver post',
                'verified' => $relatedTags->contains('verificado'),
                'featured' => $relatedPost->is_vip || $relatedTags->contains('destacado'),
            ];
        });

    return view('posts.show-public', compact('ageGate', 'category', 'contactButtons', 'post', 'postCards', 'relatedPosts', 'siteSettings', 'tags'));
})
    ->name('posts.public.show');

Route::get('/{category:slug}', function (Category $category) {
    abort_unless($category->is_active, 404);

    $ageGate = AgeGateSetting::current()->toModalContent();
    $siteSettings = SiteSetting::current();
    $posts = Post::query()
        ->whereBelongsTo($category)
        ->publiclyVisible()
        ->latest('published_at')
        ->get()
        ->map(function (Post $post) use ($category, $siteSettings): array {
            $tags = collect($post->tags ?? [])->map(fn (string $tag): string => mb_strtolower($tag));

            return [
                'id' => 'post-'.$post->id,
                'title' => $post->title,
                'city' => $siteSettings->server_country,
                'category' => $category->name,
                'updated' => $post->published_at ? 'Publicado '.$post->published_at->diffForHumans() : 'Publicado recientemente',
                'price' => null,
                'image' => $post->cover_image_url ?: 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=900&q=80',
                'profileHref' => $post->slug
                    ? route('posts.public.show', ['category' => $category->slug, 'post' => $post->slug])
                    : '#post-'.$post->id,
                'profileLabel' => 'Ver post',
                'verified' => $tags->contains('verificado'),
                'featured' => $tags->contains('destacado'),
            ];
        });

    return view('categories.show-public', compact('ageGate', 'category', 'posts', 'siteSettings'));
})
    ->where('category', '^(?!register$|login$|logout$|dashboard$|forgot-password$|reset-password$|confirm-password$|verify-email$).+')
    ->name('categories.public.show');
