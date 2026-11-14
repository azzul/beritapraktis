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
     * Candidate models ordered by preference, deduplicated.
     *
     * @return list<string>
     */
    private function getCandidateModels(): array
    {
        $configured = trim((string) config('services.gemini.model', 'gemini-3.8-flash'));

        return array_values(array_unique(array_filter([
            $configured,
            'gemini-3.8-flash',
            'gemini-3.6-flash',
            'gemini-3.5-flash-lite',
            'gemini-2.5-flash',
        ])));
    }

    /**
     * Build generationConfig tailored to the target Gemini model family.
     *
     * @return array<string, mixed>
     */
    private function buildThinkingConfig(string $model): array
    {
        if (str_contains($model, '3.8')) {
            return ['thinkingLevel' => 'low'];
        }

        if (str_contains($model, '3.')) {
            return ['thinkingLevel' => 'minimal'];
        }

        if (str_contains($model, '2.5')) {
            return ['thinkingBudget' => 0];
        }

        return [];
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

        $errors = [];

        foreach ($this->getCandidateModels() as $model) {
            $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

            $generationConfig = [
                'maxOutputTokens' => 256,
            ];
            $thinkingConfig = $this->buildThinkingConfig($model);
            if ($thinkingConfig !== []) {
                $generationConfig['thinkingConfig'] = $thinkingConfig;
            }

            try {
                $response = Http::connectTimeout(8)
                    ->timeout(15)
                    ->withOptions([
                        'curl' => [
                            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                        ],
                    ])
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post($endpoint, [
                        'contents' => [
                            ['parts' => [['text' => 'Balas dengan satu kata: OK']]],
                        ],
                        'generationConfig' => $generationConfig,
                    ]);

                if ($response->successful()) {
                    return [
                        'ok' => true,
                        'message' => "Koneksi Gemini AI berhasil! (Model aktif: {$model}). Siap menulis berita 900+ kata.",
                    ];
                }

                $apiMsg = (string) data_get($response->json(), 'error.message', Str::limit($response->body(), 160));
                $errors[] = "{$model} (HTTP {$response->status()}): {$apiMsg}";

                // Try next model on 404 (deprecated/not found), 429 (rate limit), or 5xx (overloaded)
                if (in_array($response->status(), [404, 429], true) || $response->status() >= 500) {
                    continue;
                }

                return [
                    'ok' => false,
                    'message' => "Gemini HTTP {$response->status()} ({$model}): {$apiMsg}",
                ];
            } catch (\Throwable $e) {
                $safeMsg = preg_replace('/key=[^&\s]+/', 'key=***', $e->getMessage()) ?? $e->getMessage();
                $errors[] = "{$model} (timeout/koneksi): {$safeMsg}";
                continue;
            }
        }

        return [
            'ok' => false,
            'message' => 'Semua model Gemini gagal merespons: ' . implode(' | ', $errors),
        ];
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

        $prompt = <<<PROMPT
Anda adalah editor senior dan jurnalis profesional media online Indonesia yang menulis berita mendalam, menarik dibaca, objektif, dan ramah SEO/AdSense.

Tugas Anda: sadur dan kembangkan berita berikut menjadi artikel berita berbahasa Indonesia yang segar, informatif, dan layak tayang.

DATA SUMBER:
- Kategori: {$categoryName}
- Judul Asli: {$originalTitle}
- Ringkasan Sumber: {$rawSummary}
- Media Sumber: {$sourceName}
- URL Sumber: {$sourceUrl}

ATURAN WAJIB:
1. Buat `title` (judul berita) yang tajam, menarik minat klik pembaca secara natural, dan mengandung kata kunci utama topik (maksimal 12 kata, hindari clickbait berlebihan atau kebohongan).
2. Panjang isi artikel (`content_html`) WAJIB antara 900 sampai 1100 kata.
3. Gunakan gaya jurnalistik Indonesia yang mengalir, lugas, dan profesional (hindari kata klise seperti "di era digital yang serba cepat", "tak dapat dipungkiri", "mari kita telusuri").
4. JAGA ETIKA JURNALISTIK: Jangan mengarang fakta palsu, angka fiktif, atau kutipan wawancara fiktif yang tidak ada di sumber. Kembangkan artikel dengan menjelaskan konteks topik di Indonesia, latar belakang kebijakan/industri, implikasi praktis bagi masyarakat/pelaku usaha, serta hal yang perlu dicermati ke depan.
5. Format `content_html` menggunakan HTML bersih: hanya tag `<p>`, `<h2>`, `<h3>`, `<ul>`, `<li>`, `<strong>`, `<em>`. JANGAN gunakan tag `<h1>`, `<img>`, atau `<a>` di dalam `content_html`.
6. Sertakan 4 sampai 5 subjudul `<h2>` yang spesifik, menarik, dan memancing rasa ingin tahu sesuai isi berita (JANGAN pakai subjudul generik seperti "Sorotan Utama" atau "Kesimpulan").
7. Dilarang keras mengandung konten judi online, slot, game online ilegal, atau pornografi.
PROMPT;

        foreach ($this->getCandidateModels() as $model) {
            $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

            $generationConfig = [
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

            if (!str_contains($model, '3.')) {
                $generationConfig['temperature'] = 0.55;
            }

            $thinkingConfig = $this->buildThinkingConfig($model);
            if ($thinkingConfig !== []) {
                $generationConfig['thinkingConfig'] = $thinkingConfig;
            }

            try {
                $response = Http::connectTimeout(10)
                    ->timeout(45)
                    ->withOptions([
                        'curl' => [
                            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                        ],
                    ])
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
                    $this->lastError = "Gemini HTTP {$response->status()} ({$model}): {$apiMsg}";
                    Log::warning($this->lastError);

                    if (in_array($response->status(), [404, 429], true) || $response->status() >= 500) {
                        continue;
                    }

                    return null;
                }

                $rawText = (string) data_get($response->json(), 'candidates.0.content.parts.0.text', '');
                $rawText = trim($rawText);
                if ($rawText === '') {
                    $this->lastError = "Gemini ({$model}) mengembalikan respons kosong.";
                    continue;
                }

                $rawText = preg_replace('/^```(?:json)?\s*|\s*```$/iu', '', $rawText) ?? $rawText;
                $decoded = json_decode($rawText, true);

                if (!is_array($decoded) || empty($decoded['content_html'])) {
                    $this->lastError = "Format JSON dari Gemini ({$model}) tidak valid (" . json_last_error_msg() . ').';
                    Log::warning($this->lastError, ['raw' => Str::limit($rawText, 300)]);
                    continue;
                }

                $cleanHtml = $this->validator->sanitizeContent((string) $decoded['content_html']);
                $wc = $this->validator->countWords($cleanHtml);
                if ($wc < 350) {
                    $this->lastError = "Hasil tulisan Gemini ({$model}) terlalu pendek ({$wc} kata).";
                    continue;
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
                $safeMsg = preg_replace('/key=[^&\s]+/', 'key=***', $e->getMessage()) ?? $e->getMessage();
                $this->lastError = "Koneksi ke Gemini ({$model}) gagal: {$safeMsg}";
                Log::warning($this->lastError);
                continue;
            }
        }

        return null;
    }
}
