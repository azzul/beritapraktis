<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\User;
use App\Services\RssSadurService;
use Illuminate\Console\Command;

class FetchRssNewsCommand extends Command
{
    protected $signature = 'news:auto-sadur
        {--total=5 : Total maksimal artikel baru per eksekusi (0 = tanpa batas total)}
        {--limit=2 : Maksimal berita yang dicek per feed RSS}
        {--publish : Langsung terbitkan tanpa review}';

    protected $description = 'Ambil dan sadur berita terbaru secara etis (dengan AI Gemini jika aktif) dari preset RSS resmi Indonesia';

    public function handle(RssSadurService $sadur): int
    {
        $adminEmail = config('services.beritapraktis.admin_email', 'admin@beritapraktis.com');
        $author = User::where('role', 'admin')->first() ?? User::where('email', $adminEmail)->first();

        if (!$author) {
            $this->error('User admin belum tersedia. Jalankan db:seed terlebih dahulu.');
            return self::FAILURE;
        }

        $totalLimit = max(0, (int) $this->option('total'));
        $perFeedLimit = max(1, (int) $this->option('limit'));
        $publish = (bool) $this->option('publish');
        $totalImported = 0;

        foreach (RssSadurService::presets() as $preset) {
            if ($totalLimit > 0 && $totalImported >= $totalLimit) {
                break;
            }

            $category = Category::where('slug', $preset['category_slug'])->first();
            if (!$category) {
                continue;
            }

            $remaining = $totalLimit > 0 ? min($perFeedLimit, $totalLimit - $totalImported) : $perFeedLimit;
            $res = $sadur->importFromFeed($preset['url'], $preset['name'], $category, $author, $remaining, $publish);
            $totalImported += $res['imported'];
            $this->info("[{$preset['name']}] Imported: {$res['imported']}, Skipped: {$res['skipped']}");
        }

        $this->info("Total artikel baru disadur: {$totalImported}");
        return self::SUCCESS;
    }
}
