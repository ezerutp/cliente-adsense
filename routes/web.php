<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\IntegrationController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PostCardController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicPostBrowseController;
use App\Http\Controllers\PublicPostSearchController;
use App\Http\Controllers\SiteSettingController;
use App\Http\Controllers\VideoPostController;
use App\Http\Middleware\DiagnoseImageUploads;
use App\Models\AgeGateSetting;
use App\Models\Category;
use App\Models\Integration;
use App\Models\Location;
use App\Models\Post;
use App\Models\PostCard;
use App\Models\SiteSetting;
use App\Models\VideoPost;
use Illuminate\Support\Facades\DB;
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
            ->standardCard()
            ->where('is_vip', true)
            ->latest('published_at')
            ->latest('created_at')
            ->get()
            ->map(function (Post $post) use ($siteSettings): array {
                $tags = collect($post->tags ?? [])->map(fn (string $tag): string => mb_strtolower($tag));
                $category = $post->category;

                return [
                    'id' => 'post-'.$post->id,
                    'cardType' => $post->card_type ?? Post::CARD_TYPE_POST,
                    'title' => $post->title,
                    'subtitle' => $post->subtitle,
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
            ->standardCard()
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
    $publicPosts = fn () => Post::query()
        ->whereHas('category', fn ($query) => $query->where('is_active', true))
        ->publiclyVisible();

    $metrics = [
        [
            'label' => 'Posts públicos',
            'value' => $publicPosts()->count(),
            'detail' => 'Visibles ahora',
            'icon' => 'heroicon-o-eye',
            'href' => route('posts.index'),
            'tone' => 'green',
        ],
        [
            'label' => 'VIP activos',
            'value' => $publicPosts()->where('is_vip', true)->count(),
            'detail' => 'Destacados públicos',
            'icon' => 'heroicon-o-star',
            'href' => route('posts.index'),
            'tone' => 'amber',
        ],
        [
            'label' => 'Programados',
            'value' => Post::query()
                ->where('is_active', true)
                ->whereNotNull('published_at')
                ->where('published_at', '>', now())
                ->count(),
            'detail' => 'Pendientes de publicar',
            'icon' => 'heroicon-o-clock',
            'href' => route('posts.index'),
            'tone' => 'yellow',
        ],
        [
            'label' => 'Por vencer',
            'value' => $publicPosts()
                ->whereNotNull('ends_at')
                ->whereBetween('ends_at', [now(), now()->addDays(7)])
                ->count(),
            'detail' => 'Próximos 7 días',
            'icon' => 'heroicon-o-calendar-days',
            'href' => route('posts.index'),
            'tone' => 'red',
        ],
        [
            'label' => 'Sin contacto',
            'value' => $publicPosts()
                ->whereNull('whatsapp_url')
                ->whereNull('telegram_url')
                ->whereNull('sms_url')
                ->count(),
            'detail' => 'Revisar conversión',
            'icon' => 'heroicon-o-chat-bubble-left-right',
            'href' => route('posts.index'),
            'tone' => 'red',
        ],
        [
            'label' => 'Sin portada',
            'value' => $publicPosts()
                ->where(fn ($query) => $query->whereNull('cover_image_url')->orWhere('cover_image_url', ''))
                ->count(),
            'detail' => 'Calidad visual',
            'icon' => 'heroicon-o-photo',
            'href' => route('posts.index'),
            'tone' => 'gray',
        ],
        [
            'label' => 'Categorías activas',
            'value' => Category::query()->where('is_active', true)->count(),
            'detail' => 'Disponibles al público',
            'icon' => 'heroicon-o-squares-2x2',
            'href' => route('categories.index'),
            'tone' => 'indigo',
        ],
        [
            'label' => 'Categorías vacías',
            'value' => Category::query()
                ->where('is_active', true)
                ->whereDoesntHave('posts', fn ($query) => $query->publiclyVisible())
                ->count(),
            'detail' => 'Sin posts públicos',
            'icon' => 'heroicon-o-folder-open',
            'href' => route('categories.index'),
            'tone' => 'gray',
        ],
        [
            'label' => 'Ubicaciones activas',
            'value' => $publicPosts()
                ->whereNotNull('location')
                ->distinct('location')
                ->count('location'),
            'detail' => 'Con posts visibles',
            'icon' => 'heroicon-o-map-pin',
            'href' => route('settings.edit').'#locations',
            'tone' => 'blue',
        ],
        [
            'label' => 'Videos públicos',
            'value' => VideoPost::query()->publiclyVisible()->count(),
            'detail' => 'Galería activa',
            'icon' => 'heroicon-o-video-camera',
            'href' => route('video-posts.index'),
            'tone' => 'blue',
        ],
    ];

    $topCategories = Category::query()
        ->where('is_active', true)
        ->withCount([
            'posts as public_posts_count' => fn ($query) => $query->publiclyVisible(),
        ])
        ->orderByDesc('public_posts_count')
        ->orderBy('name')
        ->limit(5)
        ->get(['id', 'name', 'slug']);

    $topLocations = $publicPosts()
        ->select('location', DB::raw('count(*) as posts_count'))
        ->whereNotNull('location')
        ->groupBy('location')
        ->orderByDesc('posts_count')
        ->orderBy('location')
        ->limit(5)
        ->get();

    $latestPosts = $publicPosts()
        ->with('category')
        ->latest('published_at')
        ->latest('created_at')
        ->limit(6)
        ->get();

    $upcomingExpirations = $publicPosts()
        ->with('category')
        ->whereNotNull('ends_at')
        ->whereBetween('ends_at', [now(), now()->addDays(7)])
        ->orderBy('ends_at')
        ->limit(6)
        ->get();

    $alerts = [
        [
            'label' => 'Posts públicos sin contacto',
            'value' => $metrics[4]['value'],
            'href' => route('posts.index'),
        ],
        [
            'label' => 'Posts públicos sin portada',
            'value' => $metrics[5]['value'],
            'href' => route('posts.index'),
        ],
        [
            'label' => 'Categorías activas sin posts',
            'value' => $metrics[7]['value'],
            'href' => route('categories.index'),
        ],
        [
            'label' => 'Plantillas de cards activas',
            'value' => PostCard::query()->whereNull('post_id')->where('is_active', true)->count(),
            'href' => route('post-cards.index'),
        ],
        [
            'label' => 'Ubicaciones sin uso',
            'value' => Location::query()
                ->whereNotIn('name', $publicPosts()->select('location')->whereNotNull('location'))
                ->count(),
            'href' => route('settings.edit').'#locations',
        ],
    ];

    return view('dashboard', compact(
        'alerts',
        'latestPosts',
        'metrics',
        'topCategories',
        'topLocations',
        'upcomingExpirations',
    ));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::patch('dashboard/categories/{category}/toggle-visibility', [CategoryController::class, 'toggleVisibility'])
        ->middleware('permission:categories.publish')
        ->name('categories.toggle-visibility');
    Route::get('dashboard/categories', [CategoryController::class, 'index'])
        ->middleware('permission:categories.view')
        ->name('categories.index');
    Route::get('dashboard/categories/create', [CategoryController::class, 'create'])
        ->middleware('permission:categories.create')
        ->name('categories.create');
    Route::post('dashboard/categories', [CategoryController::class, 'store'])
        ->middleware(['permission:categories.create', DiagnoseImageUploads::class])
        ->name('categories.store');
    Route::get('dashboard/categories/{category}/edit', [CategoryController::class, 'edit'])
        ->middleware('permission:categories.edit')
        ->name('categories.edit');
    Route::match(['put', 'patch'], 'dashboard/categories/{category}', [CategoryController::class, 'update'])
        ->middleware(['permission:categories.edit', DiagnoseImageUploads::class])
        ->name('categories.update');
    Route::delete('dashboard/categories/{category}', [CategoryController::class, 'destroy'])
        ->middleware('permission:categories.delete')
        ->name('categories.destroy');

    Route::patch('dashboard/posts/{post}/toggle-visibility', [PostController::class, 'toggleVisibility'])
        ->middleware('permission:posts.publish')
        ->name('posts.toggle-visibility');
    Route::patch('dashboard/posts/{post}/toggle-vip', [PostController::class, 'toggleVip'])
        ->middleware('permission:posts.publish')
        ->name('posts.toggle-vip');
    Route::get('dashboard/posts', [PostController::class, 'index'])
        ->middleware('permission:posts.view')
        ->name('posts.index');
    Route::get('dashboard/posts/create', [PostController::class, 'create'])
        ->middleware('permission:posts.create')
        ->name('posts.create');
    Route::post('dashboard/posts', [PostController::class, 'store'])
        ->middleware(['permission:posts.create', DiagnoseImageUploads::class])
        ->name('posts.store');
    Route::get('dashboard/posts/{post}/edit', [PostController::class, 'edit'])
        ->middleware('permission:posts.edit')
        ->name('posts.edit');
    Route::match(['put', 'patch'], 'dashboard/posts/{post}', [PostController::class, 'update'])
        ->middleware(['permission:posts.edit', DiagnoseImageUploads::class])
        ->name('posts.update');
    Route::delete('dashboard/posts/{post}', [PostController::class, 'destroy'])
        ->middleware('permission:posts.delete')
        ->name('posts.destroy');

    Route::patch('dashboard/videos/{videoPost}/toggle-visibility', [VideoPostController::class, 'toggleVisibility'])
        ->middleware('permission:videos.publish')
        ->name('video-posts.toggle-visibility');
    Route::get('dashboard/videos', [VideoPostController::class, 'index'])
        ->middleware('permission:videos.view')
        ->name('video-posts.index');
    Route::get('dashboard/videos/create', [VideoPostController::class, 'create'])
        ->middleware('permission:videos.create')
        ->name('video-posts.create');
    Route::post('dashboard/videos', [VideoPostController::class, 'store'])
        ->middleware('permission:videos.create')
        ->name('video-posts.store');
    Route::get('dashboard/videos/{videoPost}/edit', [VideoPostController::class, 'edit'])
        ->middleware('permission:videos.edit')
        ->name('video-posts.edit');
    Route::match(['put', 'patch'], 'dashboard/videos/{videoPost}', [VideoPostController::class, 'update'])
        ->middleware('permission:videos.edit')
        ->name('video-posts.update');
    Route::delete('dashboard/videos/{videoPost}', [VideoPostController::class, 'destroy'])
        ->middleware('permission:videos.delete')
        ->name('video-posts.destroy');

    Route::patch('dashboard/post-cards/{postCard}/toggle-visibility', [PostCardController::class, 'toggleVisibility'])
        ->middleware('permission:cards.publish')
        ->name('post-cards.toggle-visibility');
    Route::get('dashboard/post-cards', [PostCardController::class, 'index'])
        ->middleware('permission:cards.view')
        ->name('post-cards.index');
    Route::get('dashboard/post-cards/create', [PostCardController::class, 'create'])
        ->middleware('permission:cards.create')
        ->name('post-cards.create');
    Route::post('dashboard/post-cards', [PostCardController::class, 'store'])
        ->middleware('permission:cards.create')
        ->name('post-cards.store');
    Route::get('dashboard/post-cards/{postCard}/edit', [PostCardController::class, 'edit'])
        ->middleware('permission:cards.edit')
        ->name('post-cards.edit');
    Route::match(['put', 'patch'], 'dashboard/post-cards/{postCard}', [PostCardController::class, 'update'])
        ->middleware('permission:cards.edit')
        ->name('post-cards.update');
    Route::delete('dashboard/post-cards/{postCard}', [PostCardController::class, 'destroy'])
        ->middleware('permission:cards.delete')
        ->name('post-cards.destroy');

    Route::patch('dashboard/integrations/{integration}/toggle-visibility', [IntegrationController::class, 'toggleVisibility'])
        ->middleware('permission:integrations.publish')
        ->name('integrations.toggle-visibility');
    Route::get('dashboard/integrations', [IntegrationController::class, 'index'])
        ->middleware('permission:integrations.view')
        ->name('integrations.index');
    Route::get('dashboard/integrations/create', [IntegrationController::class, 'create'])
        ->middleware('permission:integrations.create')
        ->name('integrations.create');
    Route::post('dashboard/integrations', [IntegrationController::class, 'store'])
        ->middleware('permission:integrations.create')
        ->name('integrations.store');
    Route::get('dashboard/integrations/{integration}/edit', [IntegrationController::class, 'edit'])
        ->middleware('permission:integrations.edit')
        ->name('integrations.edit');
    Route::match(['put', 'patch'], 'dashboard/integrations/{integration}', [IntegrationController::class, 'update'])
        ->middleware('permission:integrations.edit')
        ->name('integrations.update');
    Route::delete('dashboard/integrations/{integration}', [IntegrationController::class, 'destroy'])
        ->middleware('permission:integrations.delete')
        ->name('integrations.destroy');

    Route::get('dashboard/settings', [SiteSettingController::class, 'edit'])
        ->middleware('permission:site-settings.view')
        ->name('settings.edit');
    Route::put('dashboard/settings', [SiteSettingController::class, 'update'])
        ->middleware(['permission:site-settings.edit', DiagnoseImageUploads::class])
        ->name('settings.update');
    Route::post('dashboard/settings/locations', [LocationController::class, 'store'])
        ->middleware('permission:site-settings.edit')
        ->name('settings.locations.store');
    Route::put('dashboard/settings/locations/{location}', [LocationController::class, 'update'])
        ->middleware('permission:site-settings.edit')
        ->name('settings.locations.update');
    Route::delete('dashboard/settings/locations/{location}', [LocationController::class, 'destroy'])
        ->middleware('permission:site-settings.edit')
        ->name('settings.locations.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/buscar', PublicPostSearchController::class)
    ->name('posts.search');

Route::get('/publicar-anuncio', function () {
    $ageGate = AgeGateSetting::current()->toModalContent();
    $siteSettings = SiteSetting::current();
    $contactButtons = collect();

    if (Schema::hasTable('integrations')) {
        $contactButtons = Integration::query()
            ->where('is_active', true)
            ->whereIn('provider', ['whatsapp', 'telegram'])
            ->orderByRaw("CASE provider WHEN 'whatsapp' THEN 1 WHEN 'telegram' THEN 2 ELSE 3 END")
            ->get()
            ->map(function (Integration $integration) use ($siteSettings): array {
                $href = match ($integration->provider) {
                    'whatsapp' => $siteSettings->whatsappContactUrl($integration->base_url),
                    'telegram' => $siteSettings->telegramContactUrl($integration->base_url),
                    default => null,
                };

                return [
                    'provider' => $integration->provider,
                    'label' => $integration->name,
                    'href' => $href,
                    'icon' => $integration->icon ?: Integration::DEFAULT_ICONS[$integration->provider],
                    'button_color' => $integration->button_color ?: '#222222',
                ];
            })
            ->filter(fn (array $contact): bool => filled($contact['href']))
            ->values();
    }

    return view('advertise', compact('ageGate', 'contactButtons', 'siteSettings'));
})->name('advertise');

Route::get('/videos', [VideoPostController::class, 'publicIndex'])
    ->name('video-posts.public.index');

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
        ->standardCard()
        ->latest('published_at')
        ->latest('created_at')
        ->limit(4)
        ->get()
        ->map(function (Post $relatedPost) use ($category, $siteSettings): array {
            $relatedTags = collect($relatedPost->tags ?? [])->map(fn (string $tag): string => mb_strtolower($tag));

            return [
                'title' => $relatedPost->title,
                'subtitle' => $relatedPost->subtitle,
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
        ->orderByRaw("CASE WHEN card_type = 'banner' THEN 0 ELSE 1 END")
        ->latest('published_at')
        ->paginate(Post::PUBLIC_PER_PAGE)
        ->withQueryString()
        ->through(function (Post $post) use ($category, $siteSettings): array {
            $tags = collect($post->tags ?? [])->map(fn (string $tag): string => mb_strtolower($tag));

            return [
                'id' => 'post-'.$post->id,
                'cardType' => $post->card_type ?? Post::CARD_TYPE_POST,
                'title' => $post->title,
                'subtitle' => $post->subtitle,
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
