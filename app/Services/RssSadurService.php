<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class RssSadurService
{
    public function __construct(
        private ArticleValidationService $validator
    ) {}

    /**
     * Preset official / reputable Indonesian RSS feeds for ethical auto-sadur.
     *
     * @return list<array{name: string, url: string, category_slug: string}>
     */
    public static function presets(): array
    {
        return [
            ['name' => 'ANTARA News - Terkini', 'url' => 'https://www.antaranews.com/rss/terkini.xml', 'category_slug' => 'politik'],
            ['name' => 'ANTARA News - Ekonomi', 'url' => 'https://www.antaranews.com/rss/ekonomi.xml', 'category_slug' => 'bisnis-ekonomi'],
            ['name' => 'ANTARA News - Tekno', 'url' => 'https://www.antaranews.com/rss/tekno.xml', 'category_slug' => 'teknologi'],
            ['name' => 'ANTARA News - Hukum', 'url' => 'https://www.antaranews.com/rss/hukum.xml', 'category_slug' => 'hukum-kriminal'],
            ['name' => 'ANTARA News - Gaya Hidup', 'url' => 'https://www.antaranews.com/rss/lifestyle.xml', 'category_slug' => 'gaya-hidup'],
        ];
    }

    /**
     * Fetch RSS feed, filter banned keywords, and create ethical saduran articles.
     *
     * @return array{imported: int, skipped: int, errors: list<string>}
     */
    public function importFromFeed(
        string $feedUrl,
        string $sourceName,
        Category $category,
        User $author,
        int $limit = 5,
        bool $publishImmediately = false
    ): array {
        $imported = 0;
        $skipped = 0;
        $errors = [];

        try {
            $response = Http::timeout(15)
                ->withHeaders(['User-Agent' => 'BeritaPraktisBot/1.0 (+https://beritapraktis.com)'])
                ->get($feedUrl);

            if (!$response->successful()) {
                return ['imported' => 0, 'skipped' => 0, 'errors' => ["Gagal mengambil RSS (HTTP {$response->status()})."]];
            }

            $items = $this->parseRssItems($response->body(), $limit);
        } catch (\Throwable $e) {
            return ['imported' => 0, 'skipped' => 0, 'errors' => ['Error koneksi RSS: ' . $e->getMessage()]];
        }

        if (empty($items)) {
            return ['imported' => 0, 'skipped' => 0, 'errors' => ['Tidak ada item berita ditemukan pada feed tersebut.']];
        }

        $usedImages = Article::whereNotNull('featured_image')
            ->latest('id')
            ->limit(150)
            ->pluck('featured_image')
            ->all();

        foreach ($items as $item) {
            $title = trim(html_entity_decode(strip_tags($item['title']), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            $link = trim($item['link']);
            $desc = trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($item['description']), ENT_QUOTES | ENT_HTML5, 'UTF-8')) ?? '');

            if ($title === '' || $link === '') {
                $skipped++;
                continue;
            }

            $slug = Str::slug($title);
            if (Article::withTrashed()->where('slug', $slug)->exists()) {
                $skipped++;
                continue;
            }

            // Safety check against banned keywords (Judol, Game Online, Pornografi)
            if (!empty($this->validator->findBannedKeywords($title, $desc, [$link]))) {
                $skipped++;
                continue;
            }

            $photo = $this->fetchUniquePhoto($title, $category->slug, $usedImages);
            $usedImages[] = $photo['url'];

            $summary = $desc !== '' ? Str::limit($desc, 420) : "Ringkasan informasi terkini mengenai {$title}.";
            $content = $this->buildSaduranContent($title, $summary, $sourceName, $link, $category->name);
            $wordCount = $this->validator->countWords($content);
            $outboundLinks = $this->validator->extractOutboundLinks($content);

            Article::create([
                'user_id' => $author->id,
                'category_id' => $category->id,
                'title' => Str::limit($title, 175, ''),
                'slug' => $slug,
                'excerpt' => Str::limit($summary, 160),
                'content' => $content,
                'featured_image' => $photo['url'],
                'image_alt' => 'Foto editorial: ' . Str::limit($title, 120, ''),
                'image_caption' => "{$photo['credit']} | Sumber saduran: {$sourceName}",
                'word_count' => $wordCount,
                'outbound_links_count' => count($outboundLinks),
                'outbound_links' => $outboundLinks,
                'status' => $publishImmediately ? 'published' : 'pending_review',
                'published_at' => $publishImmediately ? now() : null,
                'reviewed_by' => $publishImmediately ? $author->id : null,
                'reviewed_at' => $publishImmediately ? now() : null,
            ]);

            $imported++;
        }

        if ($imported > 0 && $publishImmediately) {
            \App\Http\Controllers\Admin\ModerationController::clearSeoCaches();
        }

        return ['imported' => $imported, 'skipped' => $skipped, 'errors' => $errors];
    }

    /**
     * Fetch a unique real photo from Pexels API (when PEXELS_API_KEY is set),
     * falling back to a rotating pool of curated Pexels stock URLs per category.
     *
     * @param list<string> $usedImages
     * @return array{url: string, credit: string}
     */
    private function fetchUniquePhoto(string $title, string $categorySlug, array $usedImages): array
    {
        $apiKey = (string) config('services.pexels.key');

        if ($apiKey !== '') {
            try {
                $query = $this->buildPhotoSearchQuery($title, $categorySlug);
                $response = Http::timeout(8)
                    ->withHeaders(['Authorization' => $apiKey])
                    ->get('https://api.pexels.com/v1/search', [
                        'query' => $query,
                        'orientation' => 'landscape',
                        'per_page' => 15,
                    ]);

                if ($response->successful()) {
                    $photos = $response->json('photos', []);
                    foreach ($photos as $photo) {
                        $url = $photo['src']['large'] ?? $photo['src']['medium'] ?? null;
                        if ($url && !in_array($url, $usedImages, true)) {
                            $photographer = trim((string) ($photo['photographer'] ?? 'Pexels'));
                            return [
                                'url' => $url,
                                'credit' => "Foto: {$photographer} / Pexels",
                            ];
                        }
                    }
                }
            } catch (\Throwable) {
                // Fallback to curated Pexels pool below
            }
        }

        $fallbackPools = [
            'politik' => ['8070162', '39502725', '10344433', '39420194', '35380038', '38004630'],
            'bisnis-ekonomi' => ['34092', '40441676', '37945815', '11045736', '7078373', '7580827'],
            'teknologi' => ['1181354', '5380792', '34258667', '37564547', '37605911', '207580'],
            'pertanian' => ['30260658', '32954659', '39342965', '29294543', '12835129', '33597069'],
            'pendidikan' => ['6684503', '8199627', '8199146', '31367511', '31367512', '31367502'],
            'hukum-kriminal' => ['34817076', '5669619', '6077189', '6077422', '5668481', '6077328'],
            'gaya-hidup' => ['23601365', '37433237', '33537529', '18498712', '6039243', '16961403'],
            'press-release' => ['4331578', '7413910', '7413917', '7413921', '7413913', '34774346'],
        ];

        $pool = $fallbackPools[$categorySlug] ?? $fallbackPools['press-release'];
        foreach ($pool as $id) {
            $url = "https://images.pexels.com/photos/{$id}/pexels-photo-{$id}.jpeg?auto=compress&cs=tinysrgb&w=1200";
            if (!in_array($url, $usedImages, true)) {
                return [
                    'url' => $url,
                    'credit' => 'Foto: Pexels (Royalty-Free)',
                ];
            }
        }

        // Rotate if all pool items were recently used
        $randomId = $pool[array_rand($pool)];
        return [
            'url' => "https://images.pexels.com/photos/{$randomId}/pexels-photo-{$randomId}.jpeg?auto=compress&cs=tinysrgb&w=1200",
            'credit' => 'Foto: Pexels (Royalty-Free)',
        ];
    }

    private function buildPhotoSearchQuery(string $title, string $categorySlug): string
    {
        $categoryKeywords = [
            'politik' => 'government parliament diplomacy',
            'bisnis-ekonomi' => 'business finance economy office',
            'teknologi' => 'technology computer server digital',
            'pertanian' => 'agriculture farm harvest nature',
            'pendidikan' => 'education university students library',
            'hukum-kriminal' => 'law court justice legal',
            'gaya-hidup' => 'lifestyle travel health wellness',
            'press-release' => 'conference press media corporate',
        ];

        $words = preg_split('/\s+/u', preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $title) ?? '') ?: [];
        $keywords = array_slice(array_filter($words, fn (string $w) => mb_strlen($w) >= 5), 0, 2);

        return trim(implode(' ', $keywords) . ' ' . ($categoryKeywords[$categorySlug] ?? 'news'));
    }

    /**
     * Parse RSS XML items safely with or without ext-xml.
     *
     * @return list<array{title: string, link: string, description: string}>
     */
    private function parseRssItems(string $xmlString, int $limit): array
    {
        $items = [];

        if (function_exists('simplexml_load_string')) {
            libxml_use_internal_errors(true);
            $xml = @simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NOCDATA);
            if ($xml && isset($xml->channel->item)) {
                foreach ($xml->channel->item as $node) {
                    $items[] = [
                        'title' => (string) ($node->title ?? ''),
                        'link' => (string) ($node->link ?? ''),
                        'description' => (string) ($node->description ?? ''),
                    ];
                    if (count($items) >= $limit) {
                        break;
                    }
                }
                return $items;
            }
        }

        // Regex fallback if ext-xml / simplexml is unavailable
        if (preg_match_all('/<item\b[^>]*>(.*?)<\/item>/isu', $xmlString, $matches)) {
            foreach (array_slice($matches[1], 0, $limit) as $block) {
                $title = $this->extractTag($block, 'title');
                $link = $this->extractTag($block, 'link');
                $desc = $this->extractTag($block, 'description');
                $items[] = [
                    'title' => $title,
                    'link' => $link,
                    'description' => $desc,
                ];
            }
        }

        return $items;
    }

    private function extractTag(string $block, string $tag): string
    {
        if (preg_match("/<{$tag}[^>]*>(?:<!\[CDATA\[)?(.*?)(?:\]\]>)?<\/{$tag}>/isu", $block, $m)) {
            return trim($m[1]);
        }
        return '';
    }

    private function buildSaduranContent(string $title, string $summary, string $sourceName, string $sourceUrl, string $categoryName): string
    {
        return <<<HTML
<p><strong>JAKARTA, BeritaPraktis</strong> — {$summary}</p>

<h2>Sorotan Utama &amp; Konteks {$categoryName}</h2>
<p>Perkembangan terbaru terkait <em>{$title}</em> menjadi topik yang mendapat perhatian luas di kanal {$categoryName}. Informasi awal yang dihimpun menunjukkan adanya dinamika penting yang relevan bagi pembaca maupun pelaku industri di Indonesia.</p>
<p>Dalam penyampaian informasi publik, ketepatan fakta dan rujukan sumber resmi menjadi prinsip utama agar masyarakat memperoleh gambaran yang berimbang dan terhindar dari disinformasi.</p>

<h2>Implikasi bagi Pembaca</h2>
<p>Ringkasan ini disusun untuk memberikan pemahaman cepat mengenai pokok persoalan, latar belakang kebijakan, serta potensi dampaknya bagi aktivitas masyarakat sehari-hari. Pembaca disarankan untuk selalu mengikuti pembaruan lanjutan dari sumber-sumber tepercaya.</p>

<blockquote>
<p><strong>Sumber Saduran Resmi:</strong> Disadur secara etis dari <a href="{$sourceUrl}" target="_blank" rel="noopener">{$sourceName}</a>. Klik tautan sumber untuk membaca laporan lengkap aslinya.</p>
</blockquote>
HTML;
    }
}
