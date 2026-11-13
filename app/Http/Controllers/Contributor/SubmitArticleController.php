<?php

namespace App\Http\Controllers\Contributor;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\BannedKeyword;
use App\Models\Category;
use App\Models\ModerationLog;
use App\Services\ArticleValidationService;
use App\Services\ImageOptimizationService;
use App\Services\TelegramNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SubmitArticleController extends Controller
{
    public function index(Request $request): View
    {
        $articles = Article::with('category')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return view('dashboard.articles.index', compact('articles'));
    }

    public function create(Request $request): View
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        $bannedKeywords = BannedKeyword::where('is_active', true)
            ->get(['keyword', 'category'])
            ->toArray();

        $user = $request->user();
        $monthlyLimit = (int) config('services.beritapraktis.max_articles_per_month', 20);
        $monthlyUsed = Article::where('user_id', $user->id)
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();

        return view('dashboard.articles.create', compact('categories', 'bannedKeywords', 'monthlyUsed', 'monthlyLimit'));
    }

    public function store(
        Request $request,
        ArticleValidationService $validator,
        ImageOptimizationService $imageService,
        TelegramNotificationService $telegram
    ): RedirectResponse {
        $user = $request->user();
        $monthlyLimit = (int) config('services.beritapraktis.max_articles_per_month', 20);

        if (!$user->isAdmin()) {
            $monthlyUsed = Article::where('user_id', $user->id)
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->count();

            if ($monthlyUsed >= $monthlyLimit) {
                throw ValidationException::withMessages([
                    'title' => "Batas pengiriman artikel bulan ini sudah tercapai ({$monthlyUsed}/{$monthlyLimit} artikel). Silakan coba lagi bulan depan.",
                ]);
            }
        }
        $validated = $request->validate([
            'title' => ['required', 'string', 'min:15', 'max:180'],
            'category_id' => ['required', 'exists:categories,id'],
            'featured_image' => ['required', 'file', 'mimes:png,jpeg,jpg,webp,jfif,svg', 'max:500'],
            'image_alt' => ['required', 'string', 'min:5', 'max:160'],
            'image_caption' => ['nullable', 'string', 'max:220'],
            'excerpt' => ['nullable', 'string', 'max:320'],
            'content' => ['required', 'string'],
            'agree_rules' => ['accepted'],
        ], [
            'title.required' => 'Judul artikel wajib diisi.',
            'title.min' => 'Judul artikel minimal 15 karakter.',
            'category_id.required' => 'Silakan pilih kategori berita.',
            'featured_image.required' => 'Foto utama artikel wajib diunggah.',
            'featured_image.file' => 'File foto utama tidak valid.',
            'featured_image.mimes' => 'Format foto hanya boleh PNG, JPEG, JPG, WebP, JFIF, atau SVG.',
            'featured_image.max' => 'Ukuran foto maksimal 500 KB.',
            'image_alt.required' => 'Deskripsi alt text foto wajib diisi untuk SEO.',
            'content.required' => 'Isi artikel tidak boleh kosong.',
            'agree_rules.accepted' => 'Anda wajib menyetujui aturan penulisan press release.',
        ]);

        $minWords = (int) config('services.beritapraktis.min_word_count', 900);
        $maxLinks = (int) config('services.beritapraktis.max_outbound_links', 2);

        $wordCount = $validator->countWords($validated['content']);
        if ($wordCount < $minWords) {
            throw ValidationException::withMessages([
                'content' => "Artikel wajib minimal {$minWords} kata. Saat ini terdeteksi {$wordCount} kata (kurang " . ($minWords - $wordCount) . " kata lagi).",
            ]);
        }

        $outboundLinks = $validator->extractOutboundLinks($validated['content']);
        if (!$user->isAdmin() && count($outboundLinks) > $maxLinks) {
            throw ValidationException::withMessages([
                'content' => "Maksimal hanya {$maxLinks} link keluar yang diperbolehkan. Saat ini terdeteksi " . count($outboundLinks) . ' link.',
            ]);
        }

        $bannedMatches = $validator->findBannedKeywords($validated['title'], $validated['content'], $outboundLinks);
        if (!empty($bannedMatches)) {
            $sample = implode(', ', array_unique(array_column(array_slice($bannedMatches, 0, 3), 'keyword')));
            throw ValidationException::withMessages([
                'content' => "Artikel ditolak otomatis karena mengandung konten terlarang (Judi Online / Game Online / Pornografi). Kata terdeteksi: {$sample}.",
            ]);
        }

        $sanitizedContent = $validator->sanitizeContent($validated['content']);
        $slugBase = Str::slug($validated['title']);
        $slug = $slugBase;
        $counter = 2;
        while (Article::withTrashed()->where('slug', $slug)->exists()) {
            $slug = "{$slugBase}-{$counter}";
            $counter++;
        }

        $imagePath = $imageService->storeFeaturedImage($request->file('featured_image'), $slug);

        $plainText = trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($sanitizedContent), ENT_QUOTES | ENT_HTML5, 'UTF-8')) ?? '');
        $excerpt = !empty($validated['excerpt'])
            ? $validated['excerpt']
            : Str::limit($plainText, 160);

        $isDirectPublish = $user->isAdmin() && $request->boolean('publish_now');

        $article = Article::create([
            'user_id' => $user->id,
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'slug' => $slug,
            'excerpt' => $excerpt,
            'content' => $sanitizedContent,
            'featured_image' => $imagePath,
            'image_alt' => $validated['image_alt'],
            'image_caption' => $validated['image_caption'] ?? null,
            'word_count' => $wordCount,
            'outbound_links_count' => count($outboundLinks),
            'outbound_links' => $outboundLinks,
            'status' => $isDirectPublish ? 'published' : 'pending_review',
            'published_at' => $isDirectPublish ? now() : null,
            'reviewed_by' => $isDirectPublish ? $user->id : null,
            'reviewed_at' => $isDirectPublish ? now() : null,
        ]);

        ModerationLog::create([
            'article_id' => $article->id,
            'admin_id' => $isDirectPublish ? $user->id : null,
            'action' => $isDirectPublish ? 'approved' : 'submitted',
            'note' => $isDirectPublish
                ? "Diterbitkan langsung oleh Admin ({$wordCount} kata)."
                : "Dikirim oleh kontributor ({$wordCount} kata, " . count($outboundLinks) . ' link).',
            'source' => 'web',
        ]);

        if ($isDirectPublish) {
            \App\Http\Controllers\Admin\ModerationController::clearSeoCaches();

            return redirect()
                ->route('articles.show', $article->slug)
                ->with('success', 'Artikel berhasil diterbitkan langsung oleh Admin dan sitemap telah diperbarui.');
        }

        $telegram->notifyNewSubmission($article);

        return redirect()
            ->route('dashboard.articles.index')
            ->with('success', 'Artikel berhasil dikirim dan sedang menunggu persetujuan Admin. Notifikasi telah diteruskan ke redaksi.');
    }
}
