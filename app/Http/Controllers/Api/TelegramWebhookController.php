<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Admin\ModerationController;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ModerationLog;
use App\Services\TelegramNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TelegramWebhookController extends Controller
{
    public function handle(Request $request, TelegramNotificationService $telegram): JsonResponse
    {
        $secret = config('services.telegram.webhook_secret');
        if (!empty($secret)) {
            $headerSecret = $request->header('X-Telegram-Bot-Api-Secret-Token');
            if (!hash_equals($secret, (string) $headerSecret)) {
                return response()->json(['ok' => false, 'error' => 'Unauthorized'], 403);
            }
        }

        $callback = $request->input('callback_query');
        if (!$callback) {
            return response()->json(['ok' => true]);
        }

        $chatId = (string) ($callback['message']['chat']['id'] ?? '');
        $adminChatId = (string) config('services.telegram.admin_chat_id');
        if ($adminChatId === '' || $chatId !== $adminChatId) {
            return response()->json(['ok' => false], 403);
        }

        $callbackId = $callback['id'];
        $messageId = (int) ($callback['message']['message_id'] ?? 0);
        $data = (string) ($callback['data'] ?? '');

        if (!preg_match('/^(approve|reject):(\d+)$/', $data, $matches)) {
            $telegram->answerCallbackQuery($callbackId, 'Aksi tidak dikenal.');

            return response()->json(['ok' => true]);
        }

        [$action, $articleId] = [$matches[1], (int) $matches[2]];
        $article = Article::with(['user', 'category'])->find($articleId);

        if (!$article) {
            $telegram->answerCallbackQuery($callbackId, 'Artikel tidak ditemukan.', true);

            return response()->json(['ok' => true]);
        }

        $timestamp = now()->format('d M Y H:i');

        if ($action === 'approve') {
            $article->update([
                'status' => 'published',
                'rejection_reason' => null,
                'published_at' => $article->published_at ?? now(),
                'reviewed_at' => now(),
            ]);

            ModerationLog::create([
                'article_id' => $article->id,
                'action' => 'approved',
                'note' => 'Disetujui langsung via Telegram Bot.',
                'source' => 'telegram',
            ]);

            ModerationController::clearSeoCaches();

            $telegram->answerCallbackQuery($callbackId, '✅ Artikel disetujui & tayang!');
            $telegram->editMessageText(
                $chatId,
                $messageId,
                "✅ <b>APPROVED & PUBLISHED</b> ({$timestamp})\n\n"
                . "<b>Judul:</b> " . e($article->title) . "\n"
                . "<b>Kategori:</b> " . e($article->category->name ?? '-') . "\n"
                . "🔗 <a href=\"" . route('articles.show', $article->slug) . "\">Lihat Artikel Publik</a>"
            );
        } else {
            $article->update([
                'status' => 'rejected',
                'rejection_reason' => 'Ditolak oleh redaksi melalui moderasi cepat.',
                'reviewed_at' => now(),
            ]);

            ModerationLog::create([
                'article_id' => $article->id,
                'action' => 'rejected',
                'note' => 'Ditolak langsung via Telegram Bot.',
                'source' => 'telegram',
            ]);

            ModerationController::clearSeoCaches();

            $telegram->answerCallbackQuery($callbackId, '❌ Artikel ditolak.');
            $telegram->editMessageText(
                $chatId,
                $messageId,
                "❌ <b>REJECTED</b> ({$timestamp})\n\n"
                . "<b>Judul:</b> " . e($article->title) . "\n"
                . "<b>Penulis:</b> " . e($article->user->email ?? '-')
            );
        }

        return response()->json(['ok' => true]);
    }
}
