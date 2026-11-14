<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\ModerationLog;
use App\Models\User;
use App\Services\ImageOptimizationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ModerationController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status', 'pending_review');

        $articles = Article::with(['user', 'category'])
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.articles.index', compact('articles', 'status'));
    }

    public function show(Article $article): View
    {
        $article->load(['user', 'category', 'moderationLogs.admin']);
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();

        return view('admin.articles.show', compact('article', 'categories'));
    }

    public function updateCategory(Request $request, Article $article): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
        ]);

        $article->update([
            'category_id' => $validated['category_id'],
        ]);

        if ($article->status === 'published') {
            $this->clearSeoCaches();
        }

        return redirect()
            ->route('admin.articles.show', $article)
            ->with('success', 'Kategori artikel berhasil diperbarui.');
    }

    public function updateImage(Request $request, Article $article, ImageOptimizationService $imageService): RedirectResponse
    {
        $validated = $request->validate([
            'featured_image' => ['required', 'file', 'mimes:png,jpeg,jpg,webp,jfif,svg', 'max:500'],
            'image_alt' => ['nullable', 'string', 'min:3', 'max:160'],
            'image_caption' => ['nullable', 'string', 'max:220'],
        ], [
            'featured_image.required' => 'Pilih file foto baru terlebih dahulu.',
            'featured_image.mimes' => 'Format foto hanya boleh PNG, JPEG, JPG, WebP, JFIF, atau SVG.',
            'featured_image.max' => 'Ukuran foto maksimal 500 KB.',
        ]);

        $oldImage = (string) $article->featured_image;
        $newPath = $imageService->storeFeaturedImage($request->file('featured_image'), $article->slug);

        if (str_starts_with($oldImage, 'uploads/')) {
            $oldFullPath = $imageService->getUploadRoot() . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, substr($oldImage, strlen('uploads/')));
            if (is_file($oldFullPath)) {
                @unlink($oldFullPath);
            }
        } elseif (str_starts_with($oldImage, 'articles/') && Storage::disk('public')->exists($oldImage)) {
            Storage::disk('public')->delete($oldImage);
        }

        $article->update([
            'featured_image' => $newPath,
            'image_alt' => $validated['image_alt'] ?: $article->image_alt,
            'image_caption' => $validated['image_caption'] ?? $article->image_caption,
        ]);

        if ($article->status === 'published') {
            $this->clearSeoCaches();
        }

        return redirect()
            ->route('admin.articles.show', $article)
            ->with('success', 'Foto utama artikel berhasil diganti.');
    }

    public function approve(Request $request, Article $article): RedirectResponse
    {
        $updateData = [
            'status' => 'published',
            'rejection_reason' => null,
            'published_at' => $article->published_at ?? now(),
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ];

        if ($request->filled('category_id')) {
            $validated = $request->validate([
                'category_id' => ['exists:categories,id'],
            ]);
            $updateData['category_id'] = $validated['category_id'];
        }

        $article->update($updateData);

        ModerationLog::create([
            'article_id' => $article->id,
            'admin_id' => $request->user()->id,
            'action' => 'approved',
            'note' => 'Disetujui & diterbitkan melalui Panel Admin.',
            'source' => 'web',
        ]);

        $this->clearSeoCaches();

        return redirect()
            ->route('admin.articles.show', $article)
            ->with('success', 'Artikel berhasil disetujui dan langsung tayang di publik.');
    }

    public function reject(Request $request, Article $article): RedirectResponse
    {
        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'min:5', 'max:500'],
        ], [
            'rejection_reason.required' => 'Alasan penolakan wajib diisi agar penulis tahu kesalahannya.',
        ]);

        $article->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        ModerationLog::create([
            'article_id' => $article->id,
            'admin_id' => $request->user()->id,
            'action' => 'rejected',
            'note' => $validated['rejection_reason'],
            'source' => 'web',
        ]);

        $this->clearSeoCaches();

        return redirect()
            ->route('admin.articles.show', $article)
            ->with('success', 'Artikel telah ditolak.');
    }

    public function toggleBanUser(Request $request, User $user): RedirectResponse
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'Tidak dapat memblokir akun admin.');
        }

        $user->update([
            'is_banned' => !$user->is_banned,
            'ban_reason' => !$user->is_banned ? ($request->input('ban_reason') ?: 'Melanggar pedoman konten (Judol/Game Online/Pornografi/Spam).') : null,
        ]);

        return back()->with('success', $user->is_banned ? "User {$user->email} telah diblokir." : "Blokir user {$user->email} dicabut.");
    }

    public static function clearSeoCaches(): void
    {
        foreach ([
            'seo_sitemap_index',
            'seo_sitemap_pages',
            'seo_sitemap_articles',
            'seo_sitemap_news',
            'seo_llms_txt',
            'seo_llms_full_txt',
            'seo_rss_feed',
        ] as $key) {
            Cache::forget($key);
        }
    }
}
