# Blueprint Proyek: BeritaPraktis (Platform Berita & Press Release Gratis)

> **Catatan Lingkungan & Lokasi Proyek:**
> - **Repository:** `azzul/beritapraktis`
> - **Target Path Lokal (XAMPP/Windows):** `D:\xampp\htdocs\beritapraktis`
> - **Stack Utama:** Laravel 11+ (PHP 8.2+), MySQL/MariaDB, Blade + Tailwind CSS + Alpine.js (ringan, tanpa SPA bloat).
> - **Tujuan Dokumen:** Panduan arsitektur, spesifikasi teknis, aturan bisnis, dan roadmap implementasi lengkap agar AI Agent atau developer dapat langsung membangun proyek ini secara konsisten.

---

## 1. Visi & Tujuan Besar Proyek

**BeritaPraktis** adalah portal berita dan platform **Press Release / Post Berita Gratis** berbasis web. Siapa saja dapat mendaftar/masuk menggunakan akun Google dan mengirimkan berita atau press release secara gratis di berbagai kategori (Politik, Bisnis, Teknologi, Pertanian, dll.), dengan syarat **wajib melewati validasi konten otomatis** dan **persetujuan (approval) manual oleh Admin** yang terintegrasi dengan notifikasi **Telegram**.

Selain menjadi media berita umum, website ini dirancang dengan **SEO Berita yang sangat kuat** dan memiliki halaman landing khusus untuk menjaring trafik organik dari kata kunci:
- **Press Release Gratis** (`/press-release-gratis`)
- **Backlink Berita Gratis** (`/backlink-berita-gratis`)
- **Post Berita Gratis** (`/post-berita-gratis`)

---

## 2. Stack Teknologi & Prasyarat (Kompatibel XAMPP Lokal)

- **Backend:** Laravel 11 / 12 (PHP >= 8.2)
- **Database:** MySQL / MariaDB (default XAMPP)
- **Frontend:** Laravel Blade + Tailwind CSS (via Vite) + Alpine.js (untuk interaktivitas ringan seperti live word counter, link counter, dan preview gambar)
- **Rich Text Editor:** Quill.js atau Tiptap (ringan, mudah dikustomisasi untuk menghitung jumlah kata & jumlah tag `<a>` secara real-time)
- **Autentikasi:** `laravel/socialite` (Google OAuth 2.0)
- **Image Processing:** `intervention/image` (validasi dimensi, auto-resize, dan konversi ke `.webp` untuk performa & SEO)
- **Notifikasi & Approval:** Telegram Bot API (HTTP Client Laravel / Webhook)
- **Admin Panel:** Custom Blade Admin Dashboard (ringan & cepat) atau FilamentPHP (opsional, disarankan Custom Blade agar super ringan dan sesuai desain editorial)

---

## 3. Role Pengguna & Alur Utama

### A. Role Pengguna
1. **Guest (Pembaca):** Membaca berita, mencari artikel, mengakses halaman SEO landing & halaman aturan.
2. **Contributor / Penulis (`role: user`):** Login via Google OAuth. Bisa menulis, mengedit draft, mengirim artikel untuk direview, dan melihat status artikelnya (`pending`, `approved/published`, `rejected`).
3. **Admin / Pemimpin Redaksi (`role: admin`):** Login via Google OAuth (email admin di-whitelist di `.env` / database). Menerima notifikasi Telegram setiap ada artikel masuk, melakukan approve/reject (via Dashboard atau langsung dari tombol Telegram), mengelola kategori, dan memblokir user/kata kunci terlarang.

### B. Alur Submit & Moderasi Artikel
1. User klik **"Kirim Berita / Press Release"** -> diarahkan login dengan **Google OAuth** jika belum login.
2. User membaca ringkasan **Aturan Penulisan** di atas/samping form editor.
3. User mengisi Judul, Kategori, Upload Foto Utama (Featured Image), dan Isi Artikel.
4. **Validasi Real-Time (Sebelum Klik Kirim):** Sistem secara otomatis mendeteksi dan menampilkan indikator visual + notifikasi jelas untuk syarat wajib (Min. 900 kata, Maks. 2 link, Wajib ada foto, Bebas kata terlarang). Tombol **"Kirim Artikel"** tidak bisa disubmit (atau menampilkan alert detail yang spesifik) jika syarat belum terpenuhi.
5. Setelah lolos validasi client-side & server-side, status artikel menjadi `pending_review`.
6. Sistem langsung mengirim **Notifikasi ke Telegram Admin** berisi ringkasan artikel + jumlah kata + jumlah link + tombol aksi cepat (**Approve** / **Reject** / **Buka di Admin**).
7. Admin melakukan review dan menyetujui atau menolak artikel.
8. Jika disetujui (`published`), artikel tayang di halaman publik, masuk ke `sitemap.xml`, `sitemap-news.xml`, `llms.txt`, dan `llms-full.txt`.

---

## 4. Aturan Konten & Validasi Ketat (WAJIB)

Semua aturan berikut harus diterapkan **baik di sisi Frontend (real-time feedback sebelum klik kirim)** maupun **Backend (FormRequest / Service Validation)** agar tidak bisa di-bypass.

### 1. Minimum 900 Kata
- **Frontend:** Penghitung kata real-time (strip HTML tags, hitung kata bersih) dengan indikator progress bar / badge status:
  - Merah: `< 900 kata` -> *"Minimal 900 kata. Saat ini: 420 kata (kurang 480 kata lagi)."*
  - Hijau: `>= 900 kata` -> *"Memenuhi syarat minimal 900 kata (954 kata)."*
- **Backend:** Validasi `str_word_count(strip_tags($content)) >= 900` (atau regex `\p{L}+` agar akurat untuk Bahasa Indonesia).

### 2. Maksimal 2 Link Keluar (Outbound Links)
- **Frontend:** Deteksi real-time semua tag `<a href="...">` maupun URL mentah (`http://`, `https://`, `www.`) di dalam konten editor.
  - Hijau: `0 - 2 link` -> *"Jumlah link aman (1/2 link)."*
  - Merah: `> 2 link` -> *"Maksimal hanya 2 link yang diperbolehkan! Saat ini terdeteksi 4 link. Hapus 2 link untuk melanjutkan."*
- **Backend:** Parse HTML menggunakan `DOMDocument`, hitung seluruh elemen `<a>` dan pola URL mentah. Tolak jika `> 2`. Tambahkan atribut keamanan `rel="noopener"` (atau `nofollow` / `sponsored` sesuai konfigurasi admin di `.env` / settings).

### 3. Wajib Ada Foto / Featured Image
- **Frontend:** Area upload gambar dengan live preview. Jika belum ada gambar yang dipilih, tampilkan badge merah: *"Wajib mengunggah foto/gambar utama artikel."*
- **Backend:** Validasi `required|image|mimes:jpg,jpeg,png,webp|max:2048` (maks 2MB). Wajib mengisi `image_alt` (untuk SEO). Gambar otomatis dikonversi ke format `.webp` dan di-resize proporsional (misal lebar maks `1200px`).

### 4. Zero Tolerance: Judi Online, Game Online, dan Pornografi
- **Sistem Filter Kata Kunci Terlarang (`banned_keywords`):**
  - Simpan daftar kata kunci terlarang di database (bisa ditambah admin) mencakup:
    - **Judi Online:** `slot`, `gacor`, `maxwin`, `togel`, `sbobet`, `casino`, `judol`, `taruhan bola`, `jackpot`, `rtp live`, `depo`, `withdraw`, `angka jitu`, dll.
    - **Game Online:** `top up game`, `diamond ml`, `free fire`, `pubg`, `joki game`, `situs game online`, `chip domino`, `higgs domino`, dll.
    - **Pornografi / Dewasa:** kata-kata eksplisit seksual, `bokep`, `jav`, `vcs`, `open bo`, `escort`, `situs dewasa`, dll.
- **Mekanisme Deteksi:**
  - Cek pada **Judul**, **Isi Artikel**, **Meta/Excerpt**, dan **Domain/URL Link**.
  - Normalisasi teks (hapus spasi/simbol penyamaran seperti `s.l.o.t` atau `j*d*`).
  - Jika terdeteksi di frontend: tampilkan peringatan keras merah bahwa konten terindikasi melanggar kebijakan (Judi Online / Game Online / Pornografi) dan blokir pengiriman.
  - Jika lolos/disamarkan di frontend, backend tetap memblokir atau menandai `flagged` untuk ditolak admin.

### 5. Halaman Aturan Artikel & Press Release (`/aturan-press-release`)
- Halaman khusus yang menjelaskan secara transparan:
  - Syarat teknis (900+ kata, maks 2 link, wajib foto orisinal/bebas hak cipta).
  - Konten yang dilarang keras (Judol, Game Online, Pornografi, SARA, Hoax, Ujaran Kebencian, Plagiarisme).
  - Kebijakan moderasi (waktu review, hak redaksi untuk mengedit/menolak/menghapus tanpa pemberitahuan jika melanggar di kemudian hari).
  - Checkbox persetujuan aturan sebelum tombol kirim aktif.

---

## 5. Integrasi Notifikasi & Approval Telegram

### Fitur Telegram Bot
- Konfigurasi di `.env`:
  ```env
  TELEGRAM_BOT_TOKEN=your_bot_token
  TELEGRAM_ADMIN_CHAT_ID=your_chat_id
  TELEGRAM_WEBHOOK_SECRET=random_secret_string
  ```
- **Saat Artikel Baru Masuk (`pending_review`):**
  - Kirim pesan via `TelegramService` (disarankan lewat Laravel Queue / `afterResponse` agar submit user tidak lemot):
    - **Isi Pesan:**
      - Judul Artikel
      - Kategori
      - Penulis (Nama + Email Google)
      - Statistik: `Jumlah Kata: 980 | Jumlah Link: 2`
      - Daftar URL keluar yang disisipkan (agar admin langsung tahu tujuan backlink-nya!)
      - Link Preview Admin: `https://domain.com/admin/articles/{id}`
    - **Inline Keyboard Buttons:**
      - `✅ Approve & Publish` (`callback_data: approve:{article_id}`)
      - `❌ Reject` (`callback_data: reject:{article_id}`)
      - `🔍 Cek di Web` (URL button ke halaman preview admin)
- **Webhook Handler (`/api/telegram/webhook`):**
  - Verifikasi `TELEGRAM_WEBHOOK_SECRET` dan pastikan `chat_id` pengirim sama dengan `TELEGRAM_ADMIN_CHAT_ID`.
  - Jika Admin klik **Approve**: ubah status menjadi `published`, set `published_at = now()`, edit pesan Telegram menjadi *"✅ APPROVED by Admin pada [timestamp]"*.
  - Jika Admin klik **Reject**: ubah status menjadi `rejected`, edit pesan Telegram menjadi *"❌ REJECTED by Admin pada [timestamp]"*.

---

## 6. Desain UI/UX: Modern, Ringan, & "Tidak Khas AI"

> **Prinsip Desain:** Hindari tampilan template AI generik (gradient ungu-biru neon, card mengambang dengan shadow tebal berlebihan, ikon emoji di setiap heading, layout dashboard yang terasa seperti SaaS bukan portal berita).

### Arah Visual Editorial Modern
- **Gaya:** *Clean Digital Broadsheet / Editorial Magazine* (terinspirasi dari *Kompas.id*, *Financial Times*, *The Verge*, *Axios*).
- **Palet Warna:**
  - Background utama: Putih Kertas bersih (`#FFFFFF` / `#FAFAFA`)
  - Teks utama: Hitam Tinta tajam (`#111827`) dengan kontras tinggi untuk kenyamanan membaca artikel panjang (900+ kata).
  - Teks sekunder / metadata: Abu-abu editorial (`#4B5563` / `#6B7280`).
  - Warna Aksen Utama: Biru Jurnalistik (`#1D4ED8`) atau Merah Redaksi (`#B91C1C`) — digunakan hemat hanya untuk label kategori, garis aksen, dan tombol CTA utama.
  - Border tipis (`1px solid #E5E7EB`) sebagai pemisah grid berita, bukan `box-shadow` berat.
- **Tipografi:**
  - **Judul Berita (Headlines):** Serif modern yang berwibawa atau Sans-serif tegas (misal: `Newsreader` / `Playfair Display` untuk judul utama, atau `Plus Jakarta Sans` / `Inter` yang rapi).
  - **Body Artikel:** Font yang sangat nyaman dibaca panjang (`Source Serif 4` atau `Inter` ukuran `17px–18px`, `line-height: 1.75`, `max-width: 72ch`).
- **Performa & Ringan:**
  - Skor Lighthouse Performance target **95+**.
  - Minimalisir JavaScript di halaman baca artikel publik (hampir 100% HTML/CSS statis hasil render Blade + cache).
  - Gunakan `loading="lazy"` untuk gambar di bawah fold, dan `fetchpriority="high"` untuk gambar hero/utama artikel.

---

## 7. Arsitektur SEO Berita Tingkat Lanjut

### A. Structured Data (JSON-LD)
Wajib terpasang otomatis di `<head>` pada halaman terkait:
1. **`NewsArticle`** (di halaman detail berita):
   - `headline`, `description`, `image` (array URL webp), `datePublished`, `dateModified`, `author` (`Person`), `publisher` (`NewsMediaOrganization` + logo), `articleSection` (nama kategori), `wordCount`, `mainEntityOfPage`.
2. **`BreadcrumbList`**: `Home -> Kategori -> Judul Berita`.
3. **`WebSite` + `SearchAction`**: Di halaman utama untuk Sitelinks Search Box Google.
4. **`FAQPage`**: Di halaman landing SEO (`/press-release-gratis`, `/backlink-berita-gratis`, `/post-berita-gratis`).

### B. Standar Crawler AI & Mesin Pencari
1. **`/llms.txt`** (Dynamic route dengan cache):
   - Format Markdown terstruktur sesuai standar `llmstxt.org`.
   - Berisi deskripsi singkat **BeritaPraktis**, daftar kategori berita, panduan singkat cara kirim press release gratis, dan tautan ke halaman-halaman penting serta `/llms-full.txt`.
2. **`/llms-full.txt`** (Dynamic route dengan cache):
   - Berisi ringkasan lengkap artikel-artikel terbaru yang sudah `published` (Judul, URL, Kategori, Tanggal, Ringkasan/Excerpt, dan poin utama) dalam format Markdown bersih agar mudah diindeks oleh AI Search (ChatGPT Search, Perplexity, Gemini, Claude).
3. **`/sitemap.xml`** (Sitemap Index):
   - `/sitemap-pages.xml` (Homepage, halaman SEO landing, halaman aturan, kategori)
   - `/sitemap-articles.xml` (Seluruh artikel `published` dengan `<lastmod>`)
   - `/sitemap-news.xml` (Khusus **Google News Sitemap** — artikel yang terbit dalam 48 jam terakhir dengan tag `<news:news>`, `<news:publication>`, `<news:publication_date>`, `<news:title>`)
4. **`/robots.txt`**:
   - Izinkan crawler utama + AI bots (`Googlebot`, `Googlebot-News`, `GPTBot`, `PerplexityBot`, `ClaudeBot`), blokir `/admin`, `/dashboard`, `/api`, `/login`.
   - Cantumkan `Sitemap: https://domain.com/sitemap.xml`.
5. **`/feed`** (RSS 2.0):
   - Memudahkan agregator berita dan pembaca RSS.

### C. Halaman Landing SEO Promosi
Buat 3 halaman statis/dinamis dengan konten kaya (copywriting persuasif, tabel perbandingan, langkah-langkah, FAQ + Schema, dan CTA ke Google Login / Form Submit):
1. **`/press-release-gratis`** — Fokus keyword: *press release gratis, kirim press release gratis, publikasi press release*.
2. **`/backlink-berita-gratis`** — Fokus keyword: *backlink berita gratis, backlink media nasional gratis, tanam backlink artikel*.
3. **`/post-berita-gratis`** — Fokus keyword: *post berita gratis, pasang berita gratis, website kirim berita gratis*.

---

## 8. Skema Database (MySQL / Migrations)

### 1. `users`
- `id`, `name`, `email` (unique), `google_id` (nullable, indexed), `avatar` (nullable)
- `role` (`enum: 'user', 'admin'`, default `'user'`)
- `is_banned` (`boolean`, default `false`)
- `ban_reason` (`string`, nullable)
- `email_verified_at`, `remember_token`, `timestamps`

### 2. `categories`
- `id`, `name` (misal: Politik, Bisnis, Teknologi, Pertanian, Hukum, Pendidikan, Gaya Hidup)
- `slug` (unique), `description` (untuk meta description halaman kategori), `is_active` (`boolean`), `timestamps`

### 3. `articles`
- `id`, `user_id` (FK -> `users.id`), `category_id` (FK -> `categories.id`)
- `title` (`string`), `slug` (`string`, unique)
- `excerpt` (`text` — untuk meta description & card preview)
- `content` (`longText` — HTML bersih yang sudah di-sanitasi)
- `featured_image` (`string` — path ke file `.webp`)
- `image_alt` (`string` — alt text wajib untuk SEO)
- `image_caption` (`string`, nullable)
- `word_count` (`integer` — disimpan saat submit/update)
- `outbound_links_count` (`tinyInteger` — maks 2)
- `outbound_links` (`json` — menyimpan array URL keluar agar mudah diaudit admin & ditampilkan di notif Telegram)
- `status` (`enum: 'draft', 'pending_review', 'published', 'rejected'`, default `'draft'`)
- `rejection_reason` (`text`, nullable)
- `views_count` (`unsignedBigInteger`, default `0`)
- `published_at` (`timestamp`, nullable, indexed)
- `reviewed_by` (`foreignId`, nullable -> `users.id`), `reviewed_at` (`timestamp`, nullable)
- `timestamps`, `softDeletes`

### 4. `banned_keywords`
- `id`, `keyword` (`string`, unique)
- `category` (`enum: 'gambling', 'online_game', 'pornography', 'other'`)
- `is_active` (`boolean`, default `true`)
- `timestamps`

### 5. `moderation_logs`
- `id`, `article_id` (FK), `admin_id` (FK, nullable jika via Telegram), `action` (`approved`, `rejected`, `flagged`), `note` (`text`, nullable), `source` (`web` / `telegram`), `timestamps`

---

## 9. Struktur Direktori & Modul Utama Laravel

```text
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/GoogleAuthController.php
│   │   ├── Public/
│   │   │   ├── HomeController.php
│   │   │   ├── ArticleController.php        # Detail berita & kategori
│   │   │   ├── LandingSeoController.php     # /press-release-gratis, dll.
│   │   │   ├── RuleController.php           # /aturan-press-release
│   │   │   └── SeoFileController.php        # sitemap.xml, sitemap-news.xml, llms.txt, llms-full.txt, feed
│   │   ├── Contributor/
│   │   │   └── SubmitArticleController.php  # Form, draft, submit
│   │   ├── Admin/
│   │   │   ├── DashboardController.php
│   │   │   ├── ModerationController.php     # Approve/Reject dari web
│   │   │   ├── CategoryController.php
│   │   │   └── BannedKeywordController.php
│   │   └── Api/
│   │       └── TelegramWebhookController.php
│   ├── Middleware/
│   │   ├── EnsureUserIsAdmin.php
│   │   └── EnsureUserNotBanned.php
│   └── Requests/
│       └── StoreArticleRequest.php          # Validasi 900 kata, max 2 link, image, banned keywords
├── Services/
│   ├── ArticleValidationService.php         # Hitung kata, parse DOM link, cek banned keywords
│   ├── ImageOptimizationService.php         # Convert & resize ke WebP
│   ├── TelegramNotificationService.php      # Kirim notif & inline keyboard ke admin
│   └── SeoSchemaService.php                 # Generator JSON-LD (NewsArticle, FAQ, Breadcrumb)
```

---

## 10. Roadmap Implementasi Bertahap (Untuk AI Agent)

### Fase 1: Setup Proyek & Fondasi
- Inisialisasi Laravel di root proyek (`D:\xampp\htdocs\beritapraktis` di lokal atau repo workspace).
- Konfigurasi `.env` (Database MySQL, Google OAuth credentials, Telegram Bot Token & Admin Chat ID).
- Install package: `laravel/socialite`, `intervention/image`, `mews/purifier` (untuk sanitasi HTML editor agar aman dari XSS namun tetap mengizinkan tag artikel standar).
- Buat semua Migration & Seeder (Seeder kategori awal: *Politik, Bisnis, Teknologi, Pertanian, Pendidikan, Hukum, Gaya Hidup*; Seeder `banned_keywords` awal untuk judi online, game online, pornografi).

### Fase 2: Autentikasi Google OAuth & Role
- Implementasi `GoogleAuthController` (`redirect`, `callback`).
- Auto-assign `role = 'admin'` jika email cocok dengan `ADMIN_EMAIL` di `.env`.
- Middleware proteksi rute `/dashboard` (user) dan `/admin` (admin).

### Fase 3: Editor Artikel + Validasi Pre-Submit Real-Time
- Bangun halaman form `/dashboard/tulis-berita` dengan Rich Text Editor + Alpine.js.
- Implementasi **Live Validator Panel** (sticky di sisi kanan/atas form):
  1. Counter Kata (`0 / 900 kata`) — blokir submit & tampilkan pesan jelas jika `< 900`.
  2. Counter Link (`0 / 2 link`) — blokir submit & tampilkan pesan jelas jika `> 2`.
  3. Status Foto Utama (`Belum ada` / `Siap`) — blokir submit jika kosong.
  4. Status Pemeriksaan Kata Terlarang (Judol / Game Online / Pornografi).
  5. Checkbox *"Saya telah membaca dan menyetujui Aturan Press Release BeritaPraktis"*.
- Implementasi `ArticleValidationService` di backend agar aturan 1–4 divalidasi ulang secara ketat sebelum masuk database.

### Fase 4: Moderasi Admin & Integrasi Telegram
- Implementasi `TelegramNotificationService` saat artikel berstatus `pending_review`.
- Implementasi `TelegramWebhookController` untuk menangani tombol inline **Approve** & **Reject** langsung dari Telegram.
- Bangun Panel Admin Web (`/admin`) untuk review artikel lengkap, kelola kategori, kelola `banned_keywords`, dan ban user nakal.

### Fase 5: Tampilan Editorial Publik & Strong SEO
- Bangun halaman publik berestetika editorial modern, ringan, dan responsif (Homepage, Halaman Kategori, Halaman Detail Berita, Halaman Pencarian).
- Implementasi `SeoSchemaService` (JSON-LD `NewsArticle`, `BreadcrumbList`, `WebSite`, `FAQPage`).
- Implementasi `SeoFileController` (`/sitemap.xml`, `/sitemap-news.xml`, `/llms.txt`, `/llms-full.txt`, `/robots.txt`, `/feed`) dengan caching (`Cache::remember`) yang otomatis di-invalidate saat ada artikel baru di-approve.
- Bangun 3 Halaman Landing SEO (`/press-release-gratis`, `/backlink-berita-gratis`, `/post-berita-gratis`) + Halaman `/aturan-press-release`.

---

## 11. Variabel Environment (`.env.example`) yang Dibutuhkan

```env
APP_NAME="BeritaPraktis"
APP_URL=http://localhost/beritapraktis/public

ADMIN_EMAIL=emailadmin@gmail.com

GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"

TELEGRAM_BOT_TOKEN=
TELEGRAM_ADMIN_CHAT_ID=
TELEGRAM_WEBHOOK_SECRET=

# Kebijakan link keluar: nofollow, noopener, atau dofollow
ARTICLE_OUTBOUND_LINK_REL="noopener"
```
