<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramNotificationService
{
    public function isConfigured(): bool
    {
        return !empty(config('services.telegram.bot_token'))
            && !empty(config('services.telegram.admin_chat_id'));
    }

    public function notifyNewSubmission(Article $article): void
    {
        if (!$this->isConfigured()) {
            return;
        }

        $article->loadMissing(['user', 'category']);

        $linksText = 'Tidak ada link keluar.';
        if (!empty($article->outbound_links)) {
            $items = array_map(fn ($url, $idx) => ($idx + 1) . '. ' . $url, $article->outbound_links, array_keys($article->outbound_links));
            $linksText = implode("\n", $items);
        }

        $adminUrl = route('admin.articles.show', $article->id);

        $message = "📰 <b>ARTIKEL BARU MENUNGGU APPROVAL</b>\n\n"
            . "<b>Judul:</b> " . e($article->title) . "\n"
            . "<b>Kategori:</b> " . e($article->category->name ?? '-') . "\n"
            . "<b>Penulis:</b> " . e($article->user->name ?? '-') . " (" . e($article->user->email ?? '-') . ")\n"
            . "<b>Statistik:</b> {$article->word_count} kata | {$article->outbound_links_count}/2 link\n\n"
            . "<b>Outbound Links:</b>\n" . e($linksText) . "\n\n"
            . "🔗 <a href=\"{$adminUrl}\">Buka Panel Preview Admin</a>";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '✅ Approve & Publish', 'callback_data' => "approve:{$article->id}"],
                    ['text' => '❌ Reject', 'callback_data' => "reject:{$article->id}"],
                ],
                [
                    ['text' => '🔍 Review Lengkap di Web', 'url' => $adminUrl],
                ],
            ],
        ];

        $this->sendRequest('sendMessage', [
            'chat_id' => config('services.telegram.admin_chat_id'),
            'text' => $message,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true,
            'reply_markup' => json_encode($keyboard),
        ]);
    }

    public function answerCallbackQuery(string $callbackQueryId, string $text, bool $showAlert = false): void
    {
        $this->sendRequest('answerCallbackQuery', [
            'callback_query_id' => $callbackQueryId,
            'text' => $text,
            'show_alert' => $showAlert,
        ]);
    }

    public function editMessageText(int|string $chatId, int $messageId, string $text): void
    {
        $this->sendRequest('editMessageText', [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => $text,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true,
        ]);
    }

    private function sendRequest(string $method, array $payload): void
    {
        $token = config('services.telegram.bot_token');
        if (empty($token)) {
            return;
        }

        try {
            Http::timeout(8)->post("https://api.telegram.org/bot{$token}/{$method}", $payload);
        } catch (\Throwable $e) {
            Log::warning('Telegram API request failed: ' . $e->getMessage(), ['method' => $method]);
        }
    }
}
