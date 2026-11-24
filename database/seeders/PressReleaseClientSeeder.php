<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use App\Services\ArticleValidationService;
use App\Services\ImageOptimizationService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PressReleaseClientSeeder extends Seeder
{
    public function run(): void
    {
        $adminEmail = config('services.beritapraktis.admin_email', 'admin@beritapraktis.com');
        $author = User::firstOrCreate(
            ['email' => $adminEmail],
            [
                'name' => 'Redaksi BeritaPraktis',
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $category = Category::where('slug', 'press-release')->first() ?? Category::first();
        $validator = app(ArticleValidationService::class);
        $imageService = app(ImageOptimizationService::class);

        $articles = [
            [
                'title' => 'Pusat Seragam Sekolah Perkuat Kapasitas Konveksi Tangan Pertama di Solo Raya untuk Pengadaan Nasional 2026',
                'excerpt' => 'Berpengalaman sejak 2005 di Karanganyar, Pusat Seragam Sekolah mengoptimalkan kapasitas produksi 10.000+ setel per bulan untuk sekolah dan mitra toko se-Indonesia.',
                'content' => $this->buildPusatSeragamArticle(),
                'source_name' => 'Pusat Seragam Sekolah',
                'seed_image' => public_path('images/seed/press-pusat-seragam.jpg'),
                'fallback_image' => 'images/seed/press-pusat-seragam.jpg',
                'image_caption' => 'Foto: Dokumentasi Konveksi | Sumber rilis: Pusat Seragam Sekolah',
            ],
            [
                'title' => 'Sarana Rent Car Hadirkan Layanan Rental Mobil Jakarta 24 Jam dengan Sopir Profesional untuk Korporasi hingga Antar-Jemput Bandara',
                'excerpt' => 'Didukung 16 pilihan armada mulai dari Hiace, Alphard, hingga Innova Zenix, Sarana Rent Car melayani perjalanan dinas BUMN, kementerian, dan keluarga 24 jam.',
                'content' => $this->buildSaranaRentCarArticle(),
                'source_name' => 'Sarana Rent Car',
                'seed_image' => public_path('images/seed/press-sarana-rentcar.jpg'),
                'fallback_image' => 'images/seed/press-sarana-rentcar.jpg',
                'image_caption' => 'Foto: Dokumentasi Armada Eksekutif | Sumber rilis: Sarana Rent Car',
            ],
        ];

        foreach ($articles as $index => $item) {
            $slug = Str::slug($item['title']);

            if (Article::where('slug', $slug)->exists()) {
                $this->command?->info("Lewati (sudah ada): {$item['title']}");
                continue;
            }

            $storedPath = $imageService->storeFromLocalFile($item['seed_image'], $slug);
            $featuredImage = $storedPath ?? $item['fallback_image'];

            $content = $item['content'];
            $wordCount = $validator->countWords($content);
            $links = $validator->extractOutboundLinks($content);

            Article::create([
                'user_id' => $author->id,
                'category_id' => $category->id,
                'title' => $item['title'],
                'slug' => $slug,
                'excerpt' => $item['excerpt'],
                'content' => $content,
                'featured_image' => $featuredImage,
                'image_alt' => 'Foto editorial: ' . $item['title'],
                'image_caption' => $item['image_caption'],
                'word_count' => $wordCount,
                'outbound_links_count' => count($links),
                'outbound_links' => $links,
                'status' => 'published',
                'views_count' => rand(60, 240),
                'published_at' => now()->subMinutes(($index + 1) * 15),
                'reviewed_by' => $author->id,
                'reviewed_at' => now()->subMinutes(($index + 1) * 15),
            ]);

            $this->command?->info("Berhasil terbit ({$wordCount} kata, image: {$featuredImage}): {$item['title']}");
        }
    }

    private function buildPusatSeragamArticle(): string
    {
        return <<<HTML
<p><strong>KARANGANYAR, BeritaPraktis</strong> — Kebutuhan pengadaan seragam sekolah yang rapi, nyaman dikenakan siswa sepanjang hari, dan selesai tepat waktu menjelang tahun ajaran baru terus meningkat di berbagai daerah Indonesia. Menjawab tantangan distribusi serta standar mutu garmen nasional, <a href="https://pusatseragamsekolah.com" target="_blank" rel="noopener">Pusat Seragam Sekolah</a> memperluas kapasitas produksi di sentra industri tekstil Karanganyar, Solo Raya, Jawa Tengah, dengan kemampuan produksi mencapai lebih dari 10.000 setel seragam setiap bulannya.</p>

<p>Beroperasi sejak tahun 2005, pabrik konveksi tangan pertama ini telah bermitra dengan lebih dari 1.000 sekolah, pondok pesantren, yayasan pendidikan, koperasi sekolah, hingga toko seragam di seluruh Indonesia. Pengalaman lebih dari dua dekade di industri garmen menjadikan proses produksi berjalan terintegrasi di workshop milik sendiri tanpa perantara makelar, sehingga harga tetap kompetitif langsung dari pabrik.</p>

<h2>Ragam Produksi Lengkap untuk Jenjang TK/PAUD hingga SMA/SMK</h2>
<p>Sebagai mitra <a href="https://pusatseragamsekolah.com" target="_blank" rel="noopener">konveksi seragam sekolah tangan pertama</a>, Pusat Seragam Sekolah memproduksi seluruh kebutuhan pakaian seragam peserta didik secara menyeluruh. Lini produksi utamanya mencakup seragam nasional OSIS dan Pramuka untuk jenjang SD, SMP, hingga SMA/SMK dengan standar jahitan garmen yang presisi dan pola ukuran yang konsisten.</p>

<p>Selain seragam nasional, sekolah dan yayasan juga dapat memesan seragam batik sekolah dengan motif custom berlogo resmi institusi melalui proses printing maupun sablon berkualitas anti luntur, setelan baju olahraga dan training berbahan PE Double, Lotto, atau Diadora yang adem, seragam kotak-kotak untuk TK/PAUD hingga SMA, jas almamater, wearpack SMK, serta perlengkapan atribut seperti topi, dasi bordir, ikat pinggang, dan kaos kaki rajut berlogo.</p>

<h2>Standar Pemilihan Kain Nasional dan Layanan Desain Gratis</h2>
<p>Salah satu kekhawatiran utama panitia pengadaan sekolah maupun orang tua siswa adalah ketidaksesuaian kualitas kain saat barang diterima dalam jumlah besar. Untuk menjaga mutu, seluruh produksi menggunakan pilihan bahan tekstil berstandar nasional mulai dari kain Oxford Sariwarna, Tetoron Cotton (TC), dan Katun Prima/Primisima untuk kemeja serta batik, hingga Nagata Drill, American Drill, dan Famatex untuk celana maupun rok.</p>

<p>Menariknya, pihak sekolah maupun yayasan yang belum memiliki rancangan visual seragam identitas dapat memanfaatkan fasilitas gratis pembuatan mockup desain motif batik dan kaos olahraga dari tim desainer internal. Pemesan juga dapat meminta pengiriman sampel bahan (handfeel kain) terlebih dahulu untuk memastikan ketebalan, tekstur, serta warna sesuai anggaran sekolah sebelum memasuki tahap produksi massal.</p>

<h2>Quality Control Berlapis dan Garansi Ketepatan Waktu Pengiriman</h2>
<p>Setiap pesanan yang masuk di lini produksi Pusat Seragam Sekolah melalui tahapan Quality Control (QC) ketat sebelum dikemas. Pemeriksaan mencakup kerapian jahitan, kekuatan pemasangan kancing dan resleting, akurasi bordir komputer maupun cetakan logo sekolah, hingga kesesuaian tabel ukuran (size chart) masing-masing jenjang kelas.</p>

<p>Dengan dukungan manajemen jadwal produksi yang terukur, konveksi ini memberikan jaminan ketepatan waktu penyelesaian sesuai kesepakatan kontrak, terutama pada puncak musim penerimaan peserta didik baru. Informasi lengkap mengenai rekam jejak perusahaan dan fasilitas pabrik sejak 2005 dapat ditinjau melalui halaman <a href="https://pusatseragamsekolah.com/about-us" target="_blank" rel="noopener">profil pabrik konveksi sejak 2005</a>.</p>

<h2>Dukungan bagi Koperasi Sekolah, Pondok Pesantren, dan Mitra Toko Seragam</h2>
<p>Selain melayani pengadaan langsung oleh komite atau panitia sekolah negeri dan swasta, lini produksi di Karanganyar juga dirancang untuk mendukung kebutuhan mitra distributor, koperasi sekolah, serta toko perlengkapan pelajar di berbagai kabupaten dan kota. Dengan sistem harga grosir langsung dari pabrik, mitra toko dapat memperoleh margin usaha yang sehat sekaligus menjaga konsistensi stok di setiap musim pendaftaran siswa baru.</p>

<p>Untuk lembaga pendidikan berbasis asrama seperti pondok pesantren dan sekolah Islam terpadu yang umumnya membutuhkan kombinasi seragam formal, seragam harian khas yayasan, hingga busana olahraga tertutup, tim produksi mampu menyesuaikan pola potongan maupun spesifikasi bahan agar tetap nyaman dipakai beraktivitas dari pagi hingga sore hari. Fleksibilitas ukuran mulai dari anak usia TK/PAUD hingga ukuran khusus remaja dan dewasa turut memastikan seluruh siswa terlayani dengan baik.</p>

<h2>Transparansi Sistem Pembayaran dan Konsultasi Anggaran Sekolah</h2>
<p>Setiap institusi pendidikan tentu memiliki pagu anggaran pengadaan yang berbeda-beda sesuai kebijakan yayasan maupun kesepakatan komite sekolah. Oleh karena itu, tim layanan pelanggan Pusat Seragam Sekolah terbiasa membantu panitia menyesuaikan pilihan kombinasi bahan kain—misalnya memadukan kualitas kain atasan dan bawahan yang tetap nyaman namun masuk dalam batas anggaran yang telah ditetapkan—tanpa mengurangi kerapian jahitan maupun ketahanan warna.</p>

<p>Seluruh transaksi juga didukung skema pembayaran bertahap yang jelas, yaitu Down Payment (DP) sebesar 50 persen saat desain dan spesifikasi kain disetujui untuk memulai proses produksi, serta pelunasan 50 persen setelah seluruh seragam selesai diproduksi, lulus pemeriksaan kualitas, dan siap dikirimkan beserta dokumentasi barang jadi. Pendekatan kerja yang terstruktur ini membuat panitia pengadaan merasa lebih tenang dan terlindungi hingga barang tiba di lokasi.</p>

<h2>Kemudahan Pemesanan Online dari Seluruh Indonesia</h2>
<p>Meski workshop konveksi berpusat di Kebakkramat, Kabupaten Karanganyar (Solo Raya), jangkauan layanan Pusat Seragam Sekolah mencakup seluruh provinsi di Indonesia—mulai dari Jabodetabek, Jawa Barat, Jawa Timur, Sumatra, Kalimantan, Sulawesi, Bali, Nusa Tenggara, Maluku, hingga Papua. Pengiriman dikelola melalui mitra ekspedisi kargo darat, laut, dan udara tepercaya dengan tarif hemat untuk pengiriman partai besar.</p>

<p>Bagi sekolah, pondok pesantren, atau mitra toko seragam yang ingin mempelajari alur konsultasi, ketentuan minimal order yang ringan mulai 30–50 pcs untuk desain custom, hingga skema pembayaran DP 50 persen, seluruh prosedurnya dijelaskan secara terbuka di halaman <a href="https://pusatseragamsekolah.com/cara-order" target="_blank" rel="noopener">cara order konveksi seragam sekolah</a>. Sementara untuk menjadwalkan kunjungan langsung ke pabrik atau meminta penawaran harga resmi, panitia pengadaan dapat langsung mengakses halaman <a href="https://pusatseragamsekolah.com/contact-us" target="_blank" rel="noopener">konsultasi pengadaan seragam sekolah</a>.</p>
HTML;
    }

    private function buildSaranaRentCarArticle(): string
    {
        return <<<HTML
<p><strong>JAKARTA, BeritaPraktis</strong> — Tingginya mobilitas bisnis, agenda kenegaraan, serta kebutuhan perjalanan keluarga di wilayah DKI Jakarta menuntut layanan transportasi darat yang tidak hanya memiliki armada terawat, tetapi juga pengemudi yang berpengalaman dan siap siaga setiap saat. Menjawab kebutuhan tersebut, <a href="https://saranarentcar.id" target="_blank" rel="noopener">Sarana Rent Car</a> memperkuat layanan sewa mobil Jakarta 24 jam dengan sistem pengemudi profesional (plus sopir) untuk melayani segmen korporasi, instansi pemerintah, tamu VIP, hingga perjalanan rombongan dan wisata luar kota.</p>

<p>Berbasis operasional di kawasan Grogol Petamburan, Jakarta Barat, penyedia jasa transportasi dengan pengalaman lebih dari 10 tahun ini telah melayani lebih dari 5.000 perjalanan di Jabodetabek maupun lintas kota di Pulau Jawa dan Bali. Fokus pada layanan sewa mobil dengan sopir memungkinkan pelanggan terbebas dari stres menghadapi kemacetan ibu kota, aturan ganjil-genap, hingga pencarian lahan parkir di kawasan pusat bisnis.</p>

<h2>16 Pilihan Armada Terawat untuk Kebutuhan Keluarga, Rombongan, hingga VIP</h2>
<p>Kelengkapan armada menjadi salah satu keunggulan utama layanan <a href="https://saranarentcar.id" target="_blank" rel="noopener">rental mobil Jakarta dengan sopir 24 jam</a> ini. Untuk kebutuhan perjalanan keluarga maupun dinas harian yang nyaman, tersedia Toyota Innova Zenix (bensin dan hybrid) serta Innova Reborn dengan kabin lega dan suspensi empuk. Sementara bagi pelaku usaha atau tamu eksekutif yang mengutamakan kenyamanan kelas atas dan tampilan berwibawa, tersedia unit Toyota Alphard dan Toyota Fortuner.</p>

<p>Adapun untuk kebutuhan perjalanan rombongan seperti gathering kantor, kunjungan kerja, ziarah, atau wisata keluarga besar, pelanggan dapat memilih Toyota Hiace Commuter, Hiace Premio, Hiace Premio Luxury berkapasitas 10 hingga 14 kursi dengan AC dingin merata sampai baris belakang, serta Isuzu ELF berkapasitas besar. Setiap unit menjalani pemeriksaan rutin meliputi kebersihan interior, kelancaran pendingin kabin, serta kelaikan mesin sebelum diberangkatkan ke titik jemput pelanggan.</p>

<h2>Rekam Jejak Melayani BUMN, Kementerian RI, hingga Event Kenegaraan</h2>
<p>Keandalan standar operasional Sarana Rent Car tercermin dari kepercayaan berbagai institusi nasional dan acara berskala besar. Dalam portofolio layanan korporat dan instansinya, armada dan pengemudi perusahaan ini tercatat telah mendukung kebutuhan mobilitas Bank BRI, Bank Mandiri, Angkasa Pura, Kementerian Agama RI, Kementerian Dalam Negeri RI, Kementerian Sosial RI, Dinas Pendidikan, Ikatan Dokter Indonesia (IDI), hingga perhelatan internasional Presidensi G20 Indonesia.</p>

<p>Pengalaman menangani tamu delegasi, perjalanan dinas kementerian, serta operasional perbankan membentuk standar etika pengemudi yang rapi, sopan, menguasai rute alternatif Jabodetabek, serta disiplin terhadap ketepatan waktu penjemputan—baik di gedung perkantoran, hotel, rumah tinggal, maupun stasiun kereta.</p>

<h2>Jangkauan Penjemputan Luas di Seluruh Jabodetabek dan Layanan Mobil Pengantin</h2>
<p>Dari basis operasional di Jakarta Barat, layanan penjemputan mencakup seluruh wilayah Jakarta Pusat, Jakarta Selatan, Jakarta Timur, Jakarta Utara, hingga kota penyangga seperti Tangerang, Bekasi, Depok, dan Bogor, termasuk kawasan padat aktivitas seperti Grogol, Kebon Jeruk, Pantai Indah Kapuk (PIK), Kelapa Gading, hingga Cengkareng. Pelanggan cukup mengirimkan titik lokasi penjemputan, tanggal, serta tujuan perjalanan melalui WhatsApp, dan tim admin yang siaga 24 jam akan segera mengonfirmasi ketersediaan unit serta estimasi tarif terbaik.</p>

<p>Selain kebutuhan dinas kantor dan perjalanan keluarga, tersedia pula layanan mobil pengantin (wedding car) dan transportasi rombongan keluarga besar untuk acara resepsi pernikahan. Pengaturan jadwal yang terkoordinasi membantu pihak keluarga maupun penyelenggara acara memastikan tamu penting dan pasangan pengantin tiba di gedung resepsi dengan nyaman dan tepat waktu tanpa perlu mengoordinasikan banyak kendaraan secara terpisah.</p>

<h2>Mengapa Sewa Mobil dengan Sopir Lebih Efisien untuk Mobilitas Jakarta?</h2>
<p>Bagi eksekutif perusahaan, tamu luar daerah yang baru mendarat di Jakarta, maupun rombongan acara kantor, menyewa kendaraan lengkap dengan sopir berpengalaman memberikan efisiensi waktu dan energi yang signifikan. Selama perjalanan di tengah kepadatan lalu lintas Sudirman, Thamrin, Kuningan, atau jalur tol lingkar dalam, penumpang tetap dapat beristirahat, menyiapkan materi rapat, maupun berkomunikasi dengan rekan kerja secara leluasa di dalam kabin.</p>

<p>Selain itu, pelanggan tidak perlu menanggung risiko kelelahan menyetir jarak jauh saat melakukan kunjungan kerja ke luar kota maupun perjalanan wisata akhir pekan bersama keluarga. Seluruh rincian paket—apakah sudah termasuk bahan bakar minyak (BBM), biaya tol, parkir, serta akomodasi pengemudi untuk rute luar kota—dijelaskan secara terbuka sejak awal sebelum proses konfirmasi DP dilakukan.</p>

<h2>Layanan Antar-Jemput Bandara 24 Jam dan Rute Luar Kota Jawa–Bali</h2>
<p>Salah satu layanan yang paling banyak dimanfaatkan pelanggan bisnis maupun wisatawan adalah antar-jemput Bandara Internasional Soekarno-Hatta dan Bandara Halim Perdanakusuma yang beroperasi penuh 24 jam, termasuk untuk jadwal penerbangan dini hari maupun tengah malam. Pengemudi akan memantau jadwal kedatangan pesawat secara berkala dan menunggu di area terminal sehingga penumpang dapat langsung melanjutkan perjalanan tanpa antre.</p>

<p>Selain mobilitas dalam kota Jakarta, Tangerang, Bekasi, Depok, dan Bogor, tersedia pula paket perjalanan luar kota pulang-pergi (PP) maupun menginap dari Jakarta menuju Bandung, Puncak, Sukabumi, Anyer, Cirebon, Dieng, Semarang, Yogyakarta, Solo, Malang, Surabaya, Lampung, hingga Pulau Bali. Pelanggan dapat memilih durasi sewa 12 jam, full day, drop-off, harian, mingguan, hingga kontrak sewa bulanan bagi perusahaan dengan rincian komponen biaya yang dijelaskan secara transparan sejak awal sebelum pembayaran DP.</p>

<p>Bagi instansi, perusahaan, maupun keluarga yang membutuhkan transportasi nyaman dengan pengemudi berpengalaman di Jakarta dan sekitarnya, daftar lengkap armada serta konsultasi pemesanan 24 jam dapat diakses langsung melalui situs resmi <a href="https://saranarentcar.id" target="_blank" rel="noopener">layanan sewa Hiace dan Alphard Jakarta</a>.</p>
HTML;
    }
}
