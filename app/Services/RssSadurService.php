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
        private ArticleValidationService $validator,
        private AiNewsWriterService $aiWriter
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

            $origSlug = Str::slug($title);
            if (
                Article::withTrashed()
                    ->where(fn ($q) => $q->where('slug', $origSlug)->orWhere('content', 'like', "%{$link}%"))
                    ->exists()
            ) {
                $skipped++;
                continue;
            }

            // Safety check against banned keywords (Judol, Game Online, Pornografi)
            if (!empty($this->validator->findBannedKeywords($title, $desc, [$link]))) {
                $skipped++;
                continue;
            }

            $summary = $desc !== '' ? Str::limit($desc, 1500) : "Ringkasan informasi terkini mengenai {$title}.";
            $aiResult = $this->aiWriter->rewriteAndExpand($title, $summary, $sourceName, $link, $category->name);

            if ($aiResult && empty($this->validator->findBannedKeywords($aiResult['title'], $aiResult['content_html'], [$link]))) {
                $finalTitle = $aiResult['title'];
                $finalExcerpt = $aiResult['excerpt'];
                $aiKeywords = $aiResult['image_keywords'];
                $content = $aiResult['content_html'] . "\n\n" . $this->buildSourceBlockquote($sourceName, $link);
            } else {
                if ($this->aiWriter->isConfigured() && $this->aiWriter->getLastError()) {
                    $errors[] = $this->aiWriter->getLastError();
                }
                $finalTitle = Str::limit($title, 175, '');
                $finalExcerpt = Str::limit($summary, 160);
                $aiKeywords = '';
                $content = $this->buildSaduranContent($title, $summary, $sourceName, $link, $category->name);
            }

            // Ensure unique slug even if AI rewrites title
            $slugBase = Str::slug($finalTitle) ?: $origSlug;
            $slug = $slugBase;
            $counter = 2;
            while (Article::withTrashed()->where('slug', $slug)->exists()) {
                $slug = "{$slugBase}-{$counter}";
                $counter++;
            }

            $photo = $this->fetchUniquePhoto($finalTitle, $category->slug, $usedImages, $aiKeywords);
            $usedImages[] = $photo['url'];

            $wordCount = $this->validator->countWords($content);
            $outboundLinks = $this->validator->extractOutboundLinks($content);

            Article::create([
                'user_id' => $author->id,
                'category_id' => $category->id,
                'title' => $finalTitle,
                'slug' => $slug,
                'excerpt' => $finalExcerpt,
                'content' => $content,
                'featured_image' => $photo['url'],
                'image_alt' => 'Foto editorial: ' . Str::limit($finalTitle, 120, ''),
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
     * Fetch a unique real photo from Pixabay API (when PIXABAY_API_KEY is set)
     * or Pexels API (when PEXELS_API_KEY is set), falling back to a rotating pool
     * of curated Pexels stock URLs per category.
     *
     * @param list<string> $usedImages
     * @return array{url: string, credit: string}
     */
    private function fetchUniquePhoto(string $title, string $categorySlug, array $usedImages, string $aiKeywords = ''): array
    {
        $queries = array_values(array_unique(array_filter([
            trim($aiKeywords),
            $this->buildPhotoSearchQuery($title, $categorySlug),
            $this->getCategoryEnglishQuery($categorySlug),
        ])));

        $pixabayKey = (string) config('services.pixabay.key');
        if ($pixabayKey !== '') {
            foreach ($queries as $q) {
                try {
                    $response = Http::timeout(8)->get('https://pixabay.com/api/', [
                        'key' => $pixabayKey,
                        'q' => $q,
                        'image_type' => 'photo',
                        'orientation' => 'horizontal',
                        'safesearch' => 'true',
                        'per_page' => 30,
                    ]);

                    if ($response->successful()) {
                        $hits = $response->json('hits', []);
                        foreach ($hits as $hit) {
                            $url = $hit['largeImageURL'] ?? $hit['webformatURL'] ?? null;
                            if ($url && !in_array($url, $usedImages, true)) {
                                $author = trim((string) ($hit['user'] ?? 'Pixabay'));
                                return [
                                    'url' => $url,
                                    'credit' => "Foto: {$author} / Pixabay",
                                ];
                            }
                        }
                    }
                } catch (\Throwable) {
                    // Try next query or fallback
                }
            }
        }

        $pexelsKey = (string) config('services.pexels.key');
        if ($pexelsKey !== '') {
            try {
                $response = Http::timeout(8)
                    ->withHeaders(['Authorization' => $pexelsKey])
                    ->get('https://api.pexels.com/v1/search', [
                        'query' => $queries[0],
                        'orientation' => 'landscape',
                        'per_page' => 20,
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
        shuffle($pool);
        foreach ($pool as $id) {
            $url = "https://images.pexels.com/photos/{$id}/pexels-photo-{$id}.jpeg?auto=compress&cs=tinysrgb&w=1200";
            if (!in_array($url, $usedImages, true)) {
                return [
                    'url' => $url,
                    'credit' => 'Foto: Pexels (Royalty-Free)',
                ];
            }
        }

        // If all category pool items were used, pick from all pools combined before repeating
        $allIds = array_merge(...array_values($fallbackPools));
        shuffle($allIds);
        foreach ($allIds as $id) {
            $url = "https://images.pexels.com/photos/{$id}/pexels-photo-{$id}.jpeg?auto=compress&cs=tinysrgb&w=1200";
            if (!in_array($url, $usedImages, true)) {
                return [
                    'url' => $url,
                    'credit' => 'Foto: Pexels (Royalty-Free)',
                ];
            }
        }

        $randomId = $pool[0];
        return [
            'url' => "https://images.pexels.com/photos/{$randomId}/pexels-photo-{$randomId}.jpeg?auto=compress&cs=tinysrgb&w=1200",
            'credit' => 'Foto: Pexels (Royalty-Free)',
        ];
    }

    private function buildPhotoSearchQuery(string $title, string $categorySlug): string
    {
        $lower = mb_strtolower($title);
        $topicMap = [
            'bencana|banjir|longsor|gempa|cuaca|hujan' => 'weather nature storm',
            'presiden|menteri|dpr|pemerintah|politik|pilkada|kementerian' => 'government building parliament',
            'pajak|keuangan|ekonomi|inflasi|bank|rupiah|bursa|saham|investasi' => 'finance economy chart',
            'ekspor|impor|pelabuhan|logistik|industri|pabrik|manufaktur' => 'industry factory shipping',
            'umkm|bisnis|perdagangan|pasar|ritel|kuliner' => 'business shop market',
            'ai|kecerdasan buatan|digital|aplikasi|startup|teknologi|internet|siber' => 'technology computer digital',
            'pertanian|petani|pangan|beras|panen|kebun|ternak' => 'agriculture farm harvest',
            'pendidikan|sekolah|kampus|universitas|mahasiswa|guru|siswa' => 'university campus education',
            'hukum|polri|kpk|kejaksaan|pengadilan|pidana|polisi|korupsi' => 'law court justice',
            'kesehatan|rumah sakit|dokter|obat|gizi' => 'hospital healthcare medical',
            'wisata|pariwisata|hotel|pantai|bandara|kereta|transportasi' => 'travel tourism transport',
        ];

        foreach ($topicMap as $pattern => $englishQuery) {
            if (preg_match("/({$pattern})/u", $lower)) {
                return $englishQuery;
            }
        }

        return $this->getCategoryEnglishQuery($categorySlug);
    }

    private function getCategoryEnglishQuery(string $categorySlug): string
    {
        return match ($categorySlug) {
            'politik' => 'government parliament diplomacy',
            'bisnis-ekonomi' => 'business finance economy office',
            'teknologi' => 'technology computer server digital',
            'pertanian' => 'agriculture farm harvest nature',
            'pendidikan' => 'education university students library',
            'hukum-kriminal' => 'law court justice legal',
            'gaya-hidup' => 'lifestyle travel health wellness',
            default => 'conference press media corporate',
        };
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
                    $contentNs = $node->children('content', true);
                    $encoded = isset($contentNs->encoded) ? (string) $contentNs->encoded : '';
                    $rawDesc = (string) ($node->description ?? '');
                    $bestDesc = mb_strlen(strip_tags($encoded)) > mb_strlen(strip_tags($rawDesc)) ? $encoded : $rawDesc;

                    $items[] = [
                        'title' => (string) ($node->title ?? ''),
                        'link' => (string) ($node->link ?? ''),
                        'description' => $bestDesc,
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
                $encoded = $this->extractTag($block, 'content:encoded');
                $rawDesc = $this->extractTag($block, 'description');
                $bestDesc = mb_strlen(strip_tags($encoded)) > mb_strlen(strip_tags($rawDesc)) ? $encoded : $rawDesc;

                $items[] = [
                    'title' => $title,
                    'link' => $link,
                    'description' => $bestDesc,
                ];
            }
        }

        return $items;
    }

    private function extractTag(string $block, string $tag): string
    {
        $escaped = preg_quote($tag, '/');
        if (preg_match("/<{$escaped}[^>]*>(?:<!\[CDATA\[)?(.*?)(?:\]\]>)?<\/{$escaped}>/isu", $block, $m)) {
            return trim($m[1]);
        }
        return '';
    }

    private function buildSourceBlockquote(string $sourceName, string $sourceUrl): string
    {
        return <<<HTML
<blockquote>
<p><strong>Sumber Saduran Resmi:</strong> Disadur secara etis dari <a href="{$sourceUrl}" target="_blank" rel="noopener">{$sourceName}</a>. Klik tautan sumber untuk membaca laporan lengkap aslinya.</p>
</blockquote>
HTML;
    }

    private function buildSaduranContent(string $title, string $summary, string $sourceName, string $sourceUrl, string $categoryName): string
    {
        $sentences = preg_split('/(?<=[.!?])\s+/u', trim($summary)) ?: [$summary];
        $chunks = array_chunk($sentences, max(2, (int) ceil(count($sentences) / 2)));
        $leadPara = implode(' ', $chunks[0] ?? [$summary]);
        $secondPara = isset($chunks[1]) ? '<p>' . implode(' ', $chunks[1]) . '</p>' : '';
        $shortTopic = Str::limit($title, 70, '');

        return <<<HTML
<p><strong>JAKARTA, BeritaPraktis</strong> — {$leadPara}</p>
{$secondPara}

<h2>Latar Belakang: {$shortTopic}</h2>
<p>Informasi mengenai {$title} menjadi sorotan utama pada kanal {$categoryName}. Perkembangan ini mencerminkan dinamika terbaru yang berdampak langsung terhadap layanan publik, aktivitas masyarakat, maupun ekosistem terkait di Indonesia.</p>
<p>Mengacu pada laporan {$sourceName}, langkah yang ditempuh oleh pihak terkait diharapkan mampu menjawab kebutuhan di lapangan secara lebih efektif sekaligus menjaga standar pelayanan kepada masyarakat.</p>

<h2>Dampak dan Respons di Lapangan</h2>
<p>Bagi masyarakat dan pemangku kepentingan di sektor {$categoryName}, pembaruan ini perlu dicermati sebagai bagian dari penyesuaian kebijakan maupun peningkatan kualitas layanan operasional. Keterbukaan informasi dan kesiapan teknis di lapangan menjadi kunci agar pelaksanaannya berjalan lancar.</p>

<blockquote>
<p><strong>Sumber Saduran Resmi:</strong> Disadur secara etis dari <a href="{$sourceUrl}" target="_blank" rel="noopener">{$sourceName}</a>. Klik tautan sumber untuk membaca laporan lengkap aslinya.</p>
</blockquote>
HTML;
    }
}
