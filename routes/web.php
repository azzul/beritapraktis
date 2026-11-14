<?php

use App\Http\Controllers\Admin\AutoSadurController;
use App\Http\Controllers\Admin\BannedKeywordController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ModerationController;
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\TelegramWebhookController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Contributor\SubmitArticleController;
use App\Http\Controllers\Public\ArticleController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\LandingSeoController;
use App\Http\Controllers\Public\SeoFileController;
use Illuminate\Support\Facades\Route;

// Public Editorial Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/cari', [HomeController::class, 'search'])->name('search');
Route::get('/kategori/{slug}', [ArticleController::class, 'category'])->name('categories.show');
Route::get('/berita/{slug}', [ArticleController::class, 'show'])->name('articles.show');

// SEO Landing & Rules Pages
Route::get('/press-release-gratis', [LandingSeoController::class, 'pressReleaseGratis'])->name('seo.press-release');
Route::get('/backlink-berita-gratis', [LandingSeoController::class, 'backlinkBeritaGratis'])->name('seo.backlink-berita');
Route::get('/post-berita-gratis', [LandingSeoController::class, 'postBeritaGratis'])->name('seo.post-berita');
Route::get('/aturan-press-release', [LandingSeoController::class, 'rules'])->name('rules');

// Technical SEO & AI Crawler Endpoints
Route::get('/robots.txt', [SeoFileController::class, 'robots'])->name('seo.robots');
Route::get('/sitemap.xml', [SeoFileController::class, 'sitemapIndex'])->name('seo.sitemap');
Route::get('/sitemap-pages.xml', [SeoFileController::class, 'sitemapPages'])->name('seo.sitemap-pages');
Route::get('/sitemap-articles.xml', [SeoFileController::class, 'sitemapArticles'])->name('seo.sitemap-articles');
Route::get('/sitemap-news.xml', [SeoFileController::class, 'sitemapNews'])->name('seo.sitemap-news');
Route::get('/llms.txt', [SeoFileController::class, 'llmsTxt'])->name('seo.llms');
Route::get('/llms-full.txt', [SeoFileController::class, 'llmsFullTxt'])->name('seo.llms-full');
Route::get('/feed', [SeoFileController::class, 'feed'])->name('seo.feed');

// Google OAuth Authentication
Route::get('/login', [GoogleAuthController::class, 'showLogin'])->name('login');
Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');
Route::post('/logout', [GoogleAuthController::class, 'logout'])->middleware('auth')->name('logout');

// Contributor Dashboard
Route::middleware(['auth', 'not_banned'])
    ->prefix('dashboard')
    ->name('dashboard.')
    ->group(function () {
        Route::get('/artikel', [SubmitArticleController::class, 'index'])->name('articles.index');
        Route::get('/tulis-berita', [SubmitArticleController::class, 'create'])->name('articles.create');
        Route::post('/tulis-berita', [SubmitArticleController::class, 'store'])->name('articles.store');
    });

// Admin Redaksi Panel
Route::middleware(['auth', 'not_banned', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/artikel', [ModerationController::class, 'index'])->name('articles.index');
        Route::get('/artikel/{article}', [ModerationController::class, 'show'])->name('articles.show');
        Route::post('/artikel/{article}/kategori', [ModerationController::class, 'updateCategory'])->name('articles.update-category');
        Route::post('/artikel/{article}/gambar', [ModerationController::class, 'updateImage'])->name('articles.update-image');
        Route::post('/artikel/{article}/approve', [ModerationController::class, 'approve'])->name('articles.approve');
        Route::post('/artikel/{article}/reject', [ModerationController::class, 'reject'])->name('articles.reject');
        Route::post('/users/{user}/toggle-ban', [ModerationController::class, 'toggleBanUser'])->name('users.toggle-ban');

        Route::get('/kategori', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/kategori', [CategoryController::class, 'store'])->name('categories.store');
        Route::delete('/kategori/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        Route::get('/kata-terlarang', [BannedKeywordController::class, 'index'])->name('keywords.index');
        Route::post('/kata-terlarang', [BannedKeywordController::class, 'store'])->name('keywords.store');
        Route::delete('/kata-terlarang/{keyword}', [BannedKeywordController::class, 'destroy'])->name('keywords.destroy');

        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::post('/users/{user}/ban', [AdminUserController::class, 'toggleBan'])->name('users.ban');
        Route::post('/users/{user}/role', [AdminUserController::class, 'toggleRole'])->name('users.role');

        Route::get('/seo', [SeoController::class, 'index'])->name('seo.index');
        Route::post('/seo/regenerate', [SeoController::class, 'regenerate'])->name('seo.regenerate');

        Route::get('/auto-sadur', [AutoSadurController::class, 'index'])->name('sadur.index');
        Route::post('/auto-sadur', [AutoSadurController::class, 'import'])->name('sadur.import');
    });

// Telegram Bot Webhook (CSRF-exempt in bootstrap/app.php)
Route::post('/api/telegram/webhook', [TelegramWebhookController::class, 'handle'])->name('api.telegram.webhook');

// Fallback untuk file storage/app/public di shared hosting (cPanel) tanpa symlink
$servePublicStorage = function (string $path) {
    $safePath = ltrim(str_replace(['..', '\\'], ['', '/'], $path), '/');
    $fullPath = storage_path('app/public/' . $safePath);

    if (!is_file($fullPath)) {
        abort(404);
    }

    return response()->file($fullPath, [
        'Cache-Control' => 'public, max-age=31536000',
    ]);
};

Route::get('/media/{path}', $servePublicStorage)->where('path', '.*')->name('media.serve');
Route::get('/storage/{path}', $servePublicStorage)->where('path', '.*')->name('storage.fallback');
