<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AiNewsWriterService
{
    private ?string $lastError = null;

    public function __construct(
        private ArticleValidationService $validator
    ) {}

    public function isConfigured(): bool
    {
        return trim((string) config('services.gemini.key')) !== '';
    }

    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    /**
     * Quick connection test for Admin diagnostics.
     *
     * @return array{ok: bool, message: string}
     */
    public function testConnection(): array
    {
        $apiKey = trim((string) config('services.gemini.key'));
        if ($apiKey === '') {
            return [
                'ok' => false,
                'message' => 'GEMINI_API_KEY masih kosong. Pastikan sudah diisi di .env dan jalankan php artisan config:clear.',
            ];
        }

        $model = trim((string) config('services.gemini.model', 'gemini-2.5-flash')) ?: 'gemini-2.5-flash';
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        try {
            $response = Http::timeout(15)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($endpoint, [
                    'contents' => [
                        ['parts' => [['text' => 'Balas dengan satu kata: OK']]],
                    ],
                ]);

            if (!$response->successful()) {
                $apiMsg = (string) data_get($response->json(), 'error.message', Str::limit($response->body(), 180));
                return [
                    'ok' => false,
                    'message' => "Gemini HTTP {$response->status()}: {$apiMsg}",
                ];
            }

            return [
                'ok' => true,
                'message' => "Koneksi Gemini AI berhasil! (Model: {$model}). Siap menulis berita 900+ kata.",
            ];
        } catch (\Throwable $e) {
            return [
                'ok' => false,
                'message' => 'Gagal menghubungi server Gemini: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Rewrite and expand an RSS news summary into a 900+ word ethical Indonesian news article.
     *
     * @return array{title: string, excerpt: string, image_keywords: string, content_html: string}|null
     */
    public function rewriteAndExpand(
        string $originalTitle,
        string $rawSummary,
        string $sourceName,
        string $sourceUrl,
        string $categoryName
    ): ?array {
        $this->lastError = null;

        $apiKey = trim((string) config('services.gemini.key'));
        if ($apiKey === '') {
            return null;
        }

        $model = trim((string) config('services.gemini.model', 'gemini-2.5-flash')) ?: 'gemini-2.5-flash';
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $prompt = <<<PROMPT
Anda adalah editor senior dan jurnalis profesional media online Indonesia yang menulis berita mendalam, objektif, dan ramah SEO.

Tugas Anda: sadur dan kembangkan berita berikut menjadi artikel berita berbahasa Indonesia yang segar, informatif, dan layak tayang.

DATA SUMBER:
- Kategori: {$categoryName}
- Judul Asli: {$originalTitle}
- Ringkasan Sumber: {$rawSummary}
- Media Sumber: {$sourceName}
- URL Sumber: {$sourceUrl}

ATURAN WAJIB:
1. Panjang isi artikel (`content_html`) WAJIB antara 900 sampai 1100 kata.
2. Gunakan gaya jurnalistik Indonesia yang natural, lugas, dan profesional (hindari kata klise seperti "di era digital yang serba cepat", "tak dapat dipungkiri", "mari kita telusuri").
3. JAGA ETIKA JURNALISTIK: Jangan mengarang fakta palsu, angka fiktif, atau kutipan wawancara fiktif yang tidak ada di sumber. Kembangkan artikel dengan menjelaskan konteks topik di Indonesia, latar belakang kebijakan/industri, implikasi praktis bagi masyarakat/pelaku usaha, serta hal yang perlu dicermati ke depan.
4. Format `content_html` menggunakan HTML bersih: hanya tag `<p>`, `<h2>`, `<h3>`, `<ul>`, `<li>`, `<strong>`, `<em>`. JANGAN gunakan tag `<h1>`, `<img>`, atau `<a>` di dalam `content_html`.
5. Sertakan minimal 4 subjudul `<h2>` yang relevan dengan isi berita.
6. Dilarang keras mengandung konten judi online, slot, game online ilegal, atau pornografi.
PROMPT;

        $generationConfig = [
            'temperature' => 0.5,
            'maxOutputTokens' => 8192,
            'responseMimeType' => 'application/json',
            'responseSchema' => [
                'type' => 'OBJECT',
                'properties' => [
                    'title' => ['type' => 'STRING'],
                    'excerpt' => ['type' => 'STRING'],
                    'image_keywords' => ['type' => 'STRING'],
                    'content_html' => ['type' => 'STRING'],
                ],
                'required' => ['title', 'excerpt', 'image_keywords', 'content_html'],
            ],
        ];

        if (str_contains($model, '2.5')) {
            $generationConfig['thinkingConfig'] = ['thinkingBudget' => 0];
        }

        try {
            $response = Http::timeout(45)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($endpoint, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt],
                            ],
                        ],
                    ],
                    'generationConfig' => $generationConfig,
                ]);

            if (!$response->successful()) {
                $apiMsg = (string) data_get($response->json(), 'error.message', Str::limit($response->body(), 180));
                $this->lastError = "Gemini HTTP {$response->status()}: {$apiMsg}";
                Log::warning($this->lastError);
                return null;
            }

            $rawText = (string) data_get($response->json(), 'candidates.0.content.parts.0.text', '');
            $rawText = trim($rawText);
            if ($rawText === '') {
                $this->lastError = 'Gemini mengembalikan respons kosong.';
                return null;
            }

            $rawText = preg_replace('/^```(?:json)?\s*|\s*```$/iu', '', $rawText) ?? $rawText;
            $decoded = json_decode($rawText, true);

            if (!is_array($decoded) || empty($decoded['content_html'])) {
                $this->lastError = 'Format JSON dari Gemini tidak valid (' . json_last_error_msg() . ').';
                Log::warning($this->lastError, ['raw' => Str::limit($rawText, 300)]);
                return null;
            }

            $cleanHtml = $this->validator->sanitizeContent((string) $decoded['content_html']);
            $wc = $this->validator->countWords($cleanHtml);
            if ($wc < 350) {
                $this->lastError = "Hasil tulisan Gemini terlalu pendek ({$wc} kata).";
                return null;
            }

            $title = trim(strip_tags((string) ($decoded['title'] ?? $originalTitle)));
            $excerpt = trim(strip_tags((string) ($decoded['excerpt'] ?? '')));
            $imageKeywords = trim(strip_tags((string) ($decoded['image_keywords'] ?? '')));

            return [
                'title' => Str::limit($title !== '' ? $title : $originalTitle, 175, ''),
                'excerpt' => Str::limit($excerpt !== '' ? $excerpt : $rawSummary, 160),
                'image_keywords' => $imageKeywords,
                'content_html' => $cleanHtml,
            ];
        } catch (\Throwable $e) {
            $this->lastError = 'Koneksi ke Gemini gagal: ' . $e->getMessage();
            Log::warning($this->lastError);
            return null;
        }
    }
}
