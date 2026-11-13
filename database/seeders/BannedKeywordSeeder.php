<?php

namespace Database\Seeders;

use App\Models\BannedKeyword;
use Illuminate\Database\Seeder;

class BannedKeywordSeeder extends Seeder
{
    public function run(): void
    {
        $keywords = [
            // Judi Online
            ['keyword' => 'slot gacor', 'category' => 'gambling'],
            ['keyword' => 'situs slot', 'category' => 'gambling'],
            ['keyword' => 'judi online', 'category' => 'gambling'],
            ['keyword' => 'judol', 'category' => 'gambling'],
            ['keyword' => 'togel', 'category' => 'gambling'],
            ['keyword' => 'maxwin', 'category' => 'gambling'],
            ['keyword' => 'sbobet', 'category' => 'gambling'],
            ['keyword' => 'rtp live', 'category' => 'gambling'],
            ['keyword' => 'casino online', 'category' => 'gambling'],
            ['keyword' => 'taruhan bola', 'category' => 'gambling'],
            ['keyword' => 'bandar togel', 'category' => 'gambling'],
            ['keyword' => 'slot online', 'category' => 'gambling'],
            ['keyword' => 'pragmatic play', 'category' => 'gambling'],
            ['keyword' => 'zeus slot', 'category' => 'gambling'],
            ['keyword' => 'bonus new member', 'category' => 'gambling'],

            // Game Online
            ['keyword' => 'game online', 'category' => 'online_game'],
            ['keyword' => 'top up diamond', 'category' => 'online_game'],
            ['keyword' => 'topup game', 'category' => 'online_game'],
            ['keyword' => 'higgs domino', 'category' => 'online_game'],
            ['keyword' => 'chip domino', 'category' => 'online_game'],
            ['keyword' => 'joki rank', 'category' => 'online_game'],
            ['keyword' => 'joki mobile legends', 'category' => 'online_game'],
            ['keyword' => 'situs game online', 'category' => 'online_game'],
            ['keyword' => 'akun ff', 'category' => 'online_game'],

            // Pornografi / Konten Dewasa
            ['keyword' => 'bokep', 'category' => 'pornography'],
            ['keyword' => 'pornografi', 'category' => 'pornography'],
            ['keyword' => 'situs dewasa', 'category' => 'pornography'],
            ['keyword' => 'video syur', 'category' => 'pornography'],
            ['keyword' => 'open bo', 'category' => 'pornography'],
            ['keyword' => 'vcs', 'category' => 'pornography'],
            ['keyword' => 'jav', 'category' => 'pornography'],
            ['keyword' => 'nonton film dewasa', 'category' => 'pornography'],
            ['keyword' => 'escort', 'category' => 'pornography'],
            ['keyword' => 'xxx', 'category' => 'pornography'],
        ];

        foreach ($keywords as $item) {
            BannedKeyword::updateOrCreate(['keyword' => $item['keyword']], $item);
        }
    }
}
