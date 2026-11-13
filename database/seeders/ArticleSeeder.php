<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use App\Services\ArticleValidationService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
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

        $categories = Category::pluck('id', 'slug');
        $validator = app(ArticleValidationService::class);

        $articles = [
            // ================= POLITIK (5) =================
            [
                'category' => 'politik',
                'title' => 'Pemerintah Perkuat Kesiapsiagaan Nasional Antisipasi Bencana Hidrometeorologi di Puncak Musim Hujan',
                'excerpt' => 'Pemerintah pusat mengoordinasikan kementerian dan pemerintah daerah untuk mengantisipasi potensi banjir serta tanah longsor di sejumlah wilayah Indonesia.',
                'summary' => 'Memasuki puncak musim hujan pada November 2026, pemerintah pusat memperkuat langkah antisipasi nasional terhadap potensi bencana hidrometeorologi seperti banjir dan tanah longsor. Koordinasi lintas kementerian, BNPB, BMKG, serta pemerintah daerah terus diintensifkan guna memastikan kesiapan logistik, jalur evakuasi, dan sistem peringatan dini di wilayah rawan. Langkah preventif ini menjadi prioritas agar dampak sosial maupun ekonomi terhadap masyarakat dapat ditekan seminimal mungkin.',
                'source_name' => 'Sekretariat Negara RI',
                'source_url' => 'https://www.setneg.go.id/listcontent/listberita/berita_presiden_dan_pemerintah/10',
            ],
            [
                'category' => 'politik',
                'title' => 'Presiden Prabowo Prioritaskan Normalisasi Sungai untuk Cegah Banjir Susulan di Sumatra',
                'excerpt' => 'Program normalisasi sungai dan pemulihan infrastruktur pascabencana di wilayah Sumatra menjadi fokus utama rapat terbatas kabinet.',
                'summary' => 'Presiden Prabowo Subianto menegaskan pentingnya percepatan normalisasi sungai sebagai langkah jangka menengah dan panjang guna mencegah banjir susulan di berbagai provinsi di Sumatra. Dalam rapat terbatas evaluasi satu tahun pemulihan pascabencana, kepala negara menginstruksikan kementerian teknis terkait untuk memperkuat tanggul, membersihkan sedimentasi alur sungai, serta memastikan tata ruang wilayah aliran sungai berjalan sesuai aturan.',
                'source_name' => 'Sekretariat Negara RI',
                'source_url' => 'https://www.setneg.go.id/listcontent/listberita/berita_presiden_dan_pemerintah/10',
            ],
            [
                'category' => 'politik',
                'title' => 'Pemerintah Sepakati Pengangkatan PPPK Paruh Waktu Menjadi Penuh Waktu Lintas Profesi',
                'excerpt' => 'Kebijakan penataan pegawai pemerintah dengan perjanjian kerja (PPPK) lintas profesi disepakati untuk meningkatkan kepastian karier aparatur.',
                'summary' => 'Pemerintah resmi menyepakati skema pengangkatan Pegawai Pemerintah dengan Perjanjian Kerja (PPPK) paruh waktu menjadi penuh waktu di berbagai sektor profesi pelayanan publik. Kebijakan ini diambil setelah melalui kajian mendalam terkait kebutuhan formasi nasional dan kapasitas fiskal daerah, dengan harapan meningkatkan kualitas layanan pendidikan, kesehatan, dan administrasi pemerintahan di daerah.',
                'source_name' => 'Sekretariat Negara RI',
                'source_url' => 'https://www.setneg.go.id/listcontent/listberita/berita_presiden_dan_pemerintah/10',
            ],
            [
                'category' => 'politik',
                'title' => 'Sebelas Duta Besar Negara Sahabat Serahkan Surat Kepercayaan dan Perkuat Kemitraan Strategis',
                'excerpt' => 'Sebelas duta besar luar biasa dan berkuasa penuh menyerahkan surat kepercayaan kepada Presiden RI di Istana Merdeka.',
                'summary' => 'Diplomasi bilateral Indonesia kembali diperkuat menyusul penyerahan surat kepercayaan dari sebelas Duta Besar Luar Biasa dan Berkuasa Penuh (LBBP) negara sahabat kepada Presiden RI. Para perwakilan diplomatik tersebut menyatakan komitmen untuk memperluas kerja sama di bidang perdagangan, transisi energi, pendidikan, serta ketahanan pangan bersama Indonesia.',
                'source_name' => 'Sekretariat Negara RI',
                'source_url' => 'https://www.setneg.go.id/listcontent/listberita/berita_presiden_dan_pemerintah/10',
            ],
            [
                'category' => 'politik',
                'title' => 'Presiden Instruksikan Revitalisasi Stasiun Gambir, Kawasan Monas, dan Pengembangan Kereta Api Bali',
                'excerpt' => 'Modernisasi simpul transportasi publik di Jakarta dan rencana jalur perkeretaapian di Bali masuk dalam agenda prioritas infrastruktur.',
                'summary' => 'Dalam upaya meningkatkan integrasi transportasi massal dan wajah kawasan ibu kota, Presiden menginstruksikan percepatan revitalisasi Stasiun Gambir dan penataan kawasan Monumen Nasional (Monas). Selain itu, pemerintah juga mendorong studi dan pengembangan jaringan kereta api di Pulau Bali guna mengurai kepadatan lalu lintas serta mendukung pariwisata berkelanjutan.',
                'source_name' => 'Sekretariat Negara RI',
                'source_url' => 'https://www.setneg.go.id/listcontent/listberita/berita_presiden_dan_pemerintah/10',
            ],

            // ================= BISNIS & EKONOMI (5) =================
            [
                'category' => 'bisnis-ekonomi',
                'title' => 'Kementerian Keuangan Catat Penerimaan Pajak Tumbuh 23,2 Persen Hingga Oktober 2026',
                'excerpt' => 'Kinerja penerimaan perpajakan nasional menunjukkan tren positif seiring penguatan aktivitas ekonomi domestik dan reformasi administrasi.',
                'summary' => 'Kementerian Keuangan melaporkan realisasi penerimaan pajak nasional tumbuh sebesar 23,2 persen hingga akhir Oktober 2026 dibandingkan periode yang sama tahun sebelumnya. Pertumbuhan ini ditopang oleh membaiknya kepatuhan wajib pajak, digitalisasi sistem pelaporan perpajakan, serta ekspansi sektor industri pengolahan dan perdagangan dalam negeri.',
                'source_name' => 'Kemenkeu / IQPlus',
                'source_url' => 'https://bcasekuritas.co.id/latest-news/news/menkeu-penerimaan-pajak-tumbuh-232-persen-hingga-oktober-2026',
            ],
            [
                'category' => 'bisnis-ekonomi',
                'title' => 'BPS Rilis Inflasi Oktober 2026 Terkendali di Level 0,22 Persen Secara Bulanan',
                'excerpt' => 'Badan Pusat Statistik mencatat stabilitas harga pangan dan barang konsumsi menjaga laju inflasi nasional tetap dalam sasaran.',
                'summary' => 'Badan Pusat Statistik (BPS) melaporkan tingkat inflasi bulanan (month-to-month) pada Oktober 2026 berada di angka 0,22 persen, sementara inflasi tahun kalender (year-to-date) tercatat sebesar 2,40 persen. Komponen inti juga tetap terjaga stabil di level 2,56 persen secara tahunan, mencerminkan daya beli masyarakat yang relatif solid dan distribusi pasokan pangan yang terkendali.',
                'source_name' => 'Badan Pusat Statistik RI',
                'source_url' => 'https://www.bps.go.id/id/pressrelease',
            ],
            [
                'category' => 'bisnis-ekonomi',
                'title' => 'Kunjungan Wisatawan Nusantara Tembus 96,69 Juta Perjalanan pada September 2026',
                'excerpt' => 'Mobilitas wisatawan domestik dan tingkat hunian hotel bintang mengalami peningkatan signifikan menjelang kuartal keempat.',
                'summary' => 'Data resmi Badan Pusat Statistik menunjukkan jumlah perjalanan wisatawan nusantara pada September 2026 mencapai 96,69 juta perjalanan atau tumbuh 2,47 persen. Di saat yang sama, kunjungan wisatawan mancanegara tercatat sebanyak 1,43 juta kunjungan dengan tingkat penghunian kamar hotel bintang mencapai 51,78 persen, menandakan pemulihan sektor perhotelan dan pariwisata yang konsisten.',
                'source_name' => 'Badan Pusat Statistik RI',
                'source_url' => 'https://www.bps.go.id/id/pressrelease',
            ],
            [
                'category' => 'bisnis-ekonomi',
                'title' => 'Kemenkeu Dorong Sinergi Ekosistem Usaha sebagai Kunci Pengembangan UMKM Nasional',
                'excerpt' => 'Kolaborasi pembiayaan, akses pasar digital, dan pendampingan manajerial menjadi fokus penguatan daya saing UMKM.',
                'summary' => 'Kementerian Keuangan menekankan bahwa sinergi antar-ekosistem usaha—mulai dari perbankan, lembaga penjaminan, agregator ekspor, hingga platform digital—merupakan kunci utama agar pelaku Usaha Mikro, Kecil, dan Menengah (UMKM) dapat naik kelas. Pemerintah terus mengarahkan instrumen pembiayaan produktif agar UMKM mampu masuk ke rantai pasok industri nasional maupun global.',
                'source_name' => 'Kementerian Keuangan RI',
                'source_url' => 'https://www.kemenkeu.go.id/',
            ],
            [
                'category' => 'bisnis-ekonomi',
                'title' => 'Sektor Manufaktur Nasional Hadapi Dinamika Biaya Produksi di Tengah Tren Pertumbuhan 2026',
                'excerpt' => 'Para ekonom menyoroti pentingnya efisiensi logistik dan stabilitas energi untuk menjaga daya saing industri manufaktur.',
                'summary' => 'Sejumlah ekonom mengingatkan bahwa kenaikan indeks harga produsen perlu diantisipasi oleh pelaku industri manufaktur melalui efisiensi rantai pasok dan pemanfaatan teknologi otomatisasi. Meski permintaan domestik tetap tumbuh positif, pengendalian biaya bahan baku impor dan ongkos logistik menjadi faktor penentu margin keuntungan perusahaan sepanjang akhir 2026.',
                'source_name' => 'InfoPublik',
                'source_url' => 'https://infopublik.id/kategori/nasional-ekonomi-bisnis/969024/ekonom-inflasi-produsen-naik-sektor-manufaktur-indonesia-hadapi-tantangan-baru',
            ],

            // ================= TEKNOLOGI (5) =================
            [
                'category' => 'teknologi',
                'title' => 'Laporan e-Conomy SEA Ungkap 162 Juta Pengguna AI Bulanan di Indonesia',
                'excerpt' => 'Tingginya adopsi kecerdasan buatan oleh masyarakat Indonesia menempatkan Tanah Air sebagai salah satu pasar digital paling dinamis di Asia Tenggara.',
                'summary' => 'Pemaparan terbaru dalam laporan e-Conomy SEA mengungkapkan bahwa terdapat sekitar 162 juta pengguna layanan kecerdasan buatan (AI) aktif bulanan di Indonesia. Lonjakan ini dipicu oleh integrasi fitur AI generatif di aplikasi produktivitas, pendidikan, perdagangan elektronik, serta layanan keuangan digital yang semakin mudah diakses melalui perangkat seluler.',
                'source_name' => 'Laporan e-Conomy SEA',
                'source_url' => 'https://www.komdigi.go.id/berita/siaran-pers/detail/pasar-digital-ri-capai-usd99-miliar-meutya-hafid-pemerintah-bangun-ekosistem-startup-bidik-pasar-global',
            ],
            [
                'category' => 'teknologi',
                'title' => 'Itera dan PT Len Industri Kenalkan Sistem Intelijen Kebencanaan AI-IMINT di KSTI 2026',
                'excerpt' => 'Inovasi geospasial berbasis kecerdasan buatan hasil kolaborasi perguruan tinggi dan industri nasional dipamerkan di Surabaya.',
                'summary' => 'Institut Teknologi Sumatera (Itera) bersama PT Len Industri, IABI, dan Universitas Indonesia memperkenalkan sistem AI-IMINT (AI-based Imagery Intelligence) pada ajang Konvensi Sains, Teknologi, dan Industri Indonesia (KSTI) 2026 di Surabaya. Teknologi ini memadukan citra satelit geospasial dan analitik AI untuk mempercepat pemetaan risiko serta respons tanggap darurat bencana alam di Indonesia.',
                'source_name' => 'Institut Teknologi Sumatera',
                'source_url' => 'https://www.itera.ac.id/itera-kenalkan-ai-imint-untuk-perkuat-mitigasi-bencana-nasional-di-ksti-2026/',
            ],
            [
                'category' => 'teknologi',
                'title' => 'Indonesia Siapkan Sovereign AI: Kemandirian Teknologi dari Model hingga Infrastruktur Data',
                'excerpt' => 'Kementerian Komunikasi dan Digital mendorong pembangunan infrastruktur komputasi dan model AI berdaulat untuk kepentingan nasional.',
                'summary' => 'Pemerintah melalui Kementerian Komunikasi dan Digital (Kemkomdigi) menegaskan komitmennya membangun ekosistem Sovereign AI atau kedaulatan kecerdasan buatan nasional. Upaya ini mencakup pengembangan pusat data berkapasitas tinggi, tata kelola data strategis dalam negeri, serta penyusunan model bahasa yang memahami konteks budaya dan bahasa daerah Indonesia.',
                'source_name' => 'Komdigi RI',
                'source_url' => 'https://portal.komdigi.go.id/kanal-publik/berita-kini/10415',
            ],
            [
                'category' => 'teknologi',
                'title' => 'Kemkomdigi Perkuat Talenta AI dan Keamanan Siber Nasional Lewat Forum AICX 2026',
                'excerpt' => 'Pengembangan sumber daya manusia di bidang keamanan siber menjadi fondasi utama kepercayaan ekosistem digital Indonesia.',
                'summary' => 'Dalam forum AI, Cybersecurity, and Innovation Exchange (AICX) 2026, Kemkomdigi menekankan bahwa akselerasi adopsi kecerdasan buatan harus berjalan beriringan dengan ketahanan siber. Pemerintah menggandeng industri teknologi dan perguruan tinggi untuk mencetak lebih banyak talenta keamanan siber guna melindungi infrastruktur digital vital serta data pribadi masyarakat.',
                'source_name' => 'Komdigi RI',
                'source_url' => 'https://www.komdigi.go.id/berita/siaran-pers/detail/bangun-ekosistem-tepercaya-indonesia-siapkan-talenta-ai-dan-keamanan-siber',
            ],
            [
                'category' => 'teknologi',
                'title' => 'Persaingan Kecerdasan Buatan Global Kini Ditentukan oleh Kesiapan Infrastruktur Komputasi',
                'excerpt' => 'Indonesia dinilai memiliki peluang besar masuk dalam rantai pasok infrastruktur komputasi dan energi hijau untuk pusat data AI.',
                'summary' => 'Persaingan pengembangan teknologi AI di tingkat global kini tidak lagi sekadar berlomba menciptakan aplikasi, melainkan ditentukan oleh kesiapan infrastruktur komputasi, semikonduktor, dan ketersediaan energi bersih. Indonesia memiliki potensi strategis untuk menarik investasi pusat data AI regional berkat ketersediaan lahan dan sumber daya energi terbarukan.',
                'source_name' => 'Komdigi RI',
                'source_url' => 'https://www.komdigi.go.id/berita/siaran-pers/detail/persaingan-ai-kini-ditentukan-infrastruktur-bukan-sekadar-aplikasi',
            ],

            // ================= PERTANIAN (5) =================
            [
                'category' => 'pertanian',
                'title' => 'INNOFOOD 2026 Buka Peluang Ekspansi Pasar bagi Industri Peternakan dan Pangan Nasional',
                'excerpt' => 'Kementerian Pertanian mendorong pemanfaatan teknologi pengolahan hasil dan efisiensi distribusi guna meningkatkan kesejahteraan peternak.',
                'summary' => 'Penyelenggaraan INNOFOOD 2026 diharapkan menjadi momentum penting bagi pelaku subsektor peternakan untuk memperluas jejaring kemitraan dan akses pasar. Kementerian Pertanian menyoroti pentingnya pemangkasan rantai distribusi yang terlalu panjang agar peternak memperoleh margin keuntungan yang layak sementara konsumen mendapatkan produk pangan bergizi dengan harga terjangkau.',
                'source_name' => 'Ditjen PKH Kementan RI',
                'source_url' => 'https://bpmsph.ditjenpkh.pertanian.go.id/berita/innofood-2026-buka-peluang-baru-bagi-industri-peternakan-nasional',
            ],
            [
                'category' => 'pertanian',
                'title' => 'Pakar Pertanian Rumuskan Rancangan Budidaya Berkelanjutan untuk Lindungi Petani Gurem di KSTI 2026',
                'excerpt' => 'Para akademisi dan peneliti menyusun policy brief sistem permakultur untuk menyelamatkan lahan kritis dan pendapatan petani kecil.',
                'summary' => 'Dalam forum Konvensi Sains, Teknologi, dan Industri Indonesia (KSTI) 2026, para pakar pertanian dari berbagai universitas merumuskan rancangan budidaya berkelanjutan guna melindungi belasan juta petani gurem. Evaluasi pembangunan pertanian ke depan didorong agar tidak hanya mengukur volume pupuk subsidi, melainkan juga stabilitas panen lintas musim dan peningkatan pendapatan bersih petani.',
                'source_name' => 'Kemdiktisaintek RI',
                'source_url' => 'https://kemdiktisaintek.go.id/news/article/di-ksti-2026-para-pakar-rumuskan-rancangan-budidaya-berkelanjutan-guna-lindungi-petani-gurem',
            ],
            [
                'category' => 'pertanian',
                'title' => 'Inovasi Budidaya Jenuh Air IPB University Optimalkan Produktivitas Lahan Pasang Surut',
                'excerpt' => 'Teknologi tata air skala mikro dan makro berhasil meningkatkan produktivitas tanaman pangan pada lahan marginal.',
                'summary' => 'Peneliti dari IPB University memaparkan keberhasilan penerapan teknologi Budidaya Jenuh Air (BJA) sebagai solusi pengelolaan lahan marginal dan rawa pasang surut di Indonesia. Dengan mengatur ketinggian muka air secara presisi pada skala mikro dan makro, metode ini mampu menjaga kestabilan pertumbuhan tanaman meski menghadapi perubahan iklim ekstrem.',
                'source_name' => 'Kemdiktisaintek / IPB University',
                'source_url' => 'https://kemdiktisaintek.go.id/news/article/di-ksti-2026-para-pakar-rumuskan-rancangan-budidaya-berkelanjutan-guna-lindungi-petani-gurem',
            ],
            [
                'category' => 'pertanian',
                'title' => 'BRIN Tegaskan Inovasi Pertanian Harus Adaptif Sesuai Karakteristik Lahan Wilayah',
                'excerpt' => 'Pendekatan pertanian yang seragam di seluruh daerah berisiko mengabaikan potensi spesifik tanah dan iklim lokal.',
                'summary' => 'Kepala Organisasi Riset Pertanian dan Pangan Badan Riset dan Inovasi Nasional (BRIN), Puji Lestari, menegaskan bahwa penerapan teknologi pertanian di Indonesia tidak bisa diseragamkan. Setiap wilayah memiliki karakteristik tanah, curah hujan, dan biologi lahan yang unik sehingga inovasi varietas maupun pola tanam wajib disesuaikan dengan kondisi spesifik daerah.',
                'source_name' => 'Kemdiktisaintek / BRIN',
                'source_url' => 'https://kemdiktisaintek.go.id/news/article/di-ksti-2026-para-pakar-rumuskan-rancangan-budidaya-berkelanjutan-guna-lindungi-petani-gurem',
            ],
            [
                'category' => 'pertanian',
                'title' => 'Kementan Perkuat Standar Vaksinasi dan Pengendalian Penyakit Mulut dan Kuku untuk Peternak',
                'excerpt' => 'Pengujian mutu produk hewan dan distribusi vaksin terstandar terus diperkuat untuk menjaga produktivitas ternak nasional.',
                'summary' => 'Balai Pengujian Mutu dan Sertifikasi Produk Hewan (BPMSPH) Kementerian Pertanian terus memperkuat standar pengendalian serta vaksinasi Penyakit Mulut dan Kuku (PMK). Langkah ini memberikan kepastian perlindungan bagi peternak rakyat maupun industri peternakan skala besar agar pasokan daging dan susu nasional tetap aman serta memenuhi standar kesehatan hewan.',
                'source_name' => 'BPMSPH Kementan RI',
                'source_url' => 'https://bpmsph.ditjenpkh.pertanian.go.id/berita/innofood-2026-buka-peluang-baru-bagi-industri-peternakan-nasional',
            ],

            // ================= PENDIDIKAN (5) =================
            [
                'category' => 'pendidikan',
                'title' => 'Konferensi Puncak Pendidikan Tinggi 2026 di Unair Bahas Etika dan Pemanfaatan AI di Kampus',
                'excerpt' => 'Perguruan tinggi didorong membangun ekosistem pembelajaran adaptif agar mahasiswa bijak memanfaatkan AI tanpa mengabaikan verifikasi.',
                'summary' => 'Konferensi Puncak Pendidikan Tinggi Indonesia (KPPTI) 2026 yang digelar di Universitas Airlangga (Unair) pada 11–13 November 2026 menyoroti peran kecerdasan buatan di ruang kuliah. Para pimpinan perguruan tinggi sepakat bahwa AI tidak semestinya dilarang, melainkan diarahkan sebagai alat bantu riset dan kreativitas yang tetap mengedepankan berpikir kritis dan verifikasi fakta.',
                'source_name' => 'Universitas Airlangga',
                'source_url' => 'https://unair.ac.id/ai-jadi-penggerak-transformasi-pendidikan-tinggi-di-era-digital/',
            ],
            [
                'category' => 'pendidikan',
                'title' => 'Mendikdasmen: Revitalisasi Sekolah Bukan Sekadar Fisik, Melainkan Investasi Pendidikan Jangka Panjang',
                'excerpt' => 'Program perbaikan sarana dan prasarana sekolah diarahkan untuk menciptakan lingkungan belajar yang aman dan bermutu.',
                'summary' => 'Menteri Pendidikan Dasar dan Menengah menegaskan bahwa program revitalisasi sekolah di seluruh Indonesia bukan hanya proyek perbaikan bangunan fisik semata, melainkan investasi jangka panjang bagi kualitas sumber daya manusia. Fasilitas belajar yang layak, aman, dan didukung akses teknologi diharapkan mampu menekan ketimpangan mutu pendidikan antarwilayah.',
                'source_name' => 'InfoPublik',
                'source_url' => 'https://infopublik.id/kategori/prioritas-nasional-2026/997185/mendikdasmen-revitalisasi-sekolah-bukan-sekadar-perbaikan-bangunan-tetapi-investasi-pendidikan',
            ],
            [
                'category' => 'pendidikan',
                'title' => 'Kemdiktisaintek Dorong Transformasi Digital dan Kolaborasi Kampus di Wilayah 3T',
                'excerpt' => 'Sinergi antarperguruan tinggi diharapkan mempercepat pemerataan akses pendidikan tinggi di daerah tertinggal, terdepan, dan terluar.',
                'summary' => 'Dalam rangkaian KPPTI 2026, Kementerian Pendidikan Tinggi, Sains, dan Teknologi (Kemdiktisaintek) mendorong kolaborasi aktif antarperguruan tinggi besar dengan kampus di wilayah 3T dan kepulauan. Melalui transformasi digital serta pertukaran praktik baik akademik, kesenjangan kualitas riset dan akses pembelajaran diharapkan semakin mengecil.',
                'source_name' => 'UNAIR News / Kemdiktisaintek',
                'source_url' => 'https://unair.ac.id/ai-jadi-penggerak-transformasi-pendidikan-tinggi-di-era-digital/',
            ],
            [
                'category' => 'pendidikan',
                'title' => 'Kajian Kebijakan Pendidikan Menuju Indonesia Emas 2045 Soroti Pemerataan Anggaran 4A',
                'excerpt' => 'Evaluasi anggaran pendidikan dinilai perlu melampaui sekadar pemenuhan alokasi 20 persen APBN.',
                'summary' => 'Lembaga riset The Indonesian Institute merilis kajian kebijakan pendidikan yang menekankan pentingnya evaluasi anggaran berbasis kerangka 4A: availability, accessibility, acceptability, dan adaptability. Selain memenuhi mandat anggaran 20 persen, pemerintah didorong memastikan distribusi dana berdampak langsung pada kesejahteraan guru, penurunan angka putus sekolah, dan peningkatan skor literasi.',
                'source_name' => 'The Indonesian Institute',
                'source_url' => 'https://www.theindonesianinstitute.com/pendidikan-indonesia-menuju-indonesia-emas-2045/',
            ],
            [
                'category' => 'pendidikan',
                'title' => 'Itenas Bandung Kembangkan Ekosistem Riset Mahasiswa Berbasis Cloud dan AI Lewat I-WILL',
                'excerpt' => 'Laboratorium kolaborasi lintas keilmuan terbukti mempercepat lahirnya produk inovasi mahasiswa di tingkat nasional.',
                'summary' => 'Institut Teknologi Nasional (Itenas) Bandung membagikan praktik baik pengembangan ruang kerja terpadu I-WILL dan komunitas teknologi ICT-CYBERLAB dalam forum KPPTI 2026. Dengan memanfaatkan komputasi awan dan perangkat AI untuk eksperimen lintas disiplin, mahasiswa berhasil melahirkan berbagai prototipe solusi nyata serta meraih sejumlah penghargaan inovasi.',
                'source_name' => 'UNAIR News / Itenas',
                'source_url' => 'https://unair.ac.id/ai-jadi-penggerak-transformasi-pendidikan-tinggi-di-era-digital/',
            ],

            // ================= HUKUM & KRIMINAL (5) =================
            [
                'category' => 'hukum-kriminal',
                'title' => 'Bareskrim Polri Tetapkan Tiga Tersangka dalam Kasus Dugaan Penyadapan Ilegal',
                'excerpt' => 'Penyidik Bareskrim menindaklanjuti laporan dugaan praktik penyadapan ilegal yang menyasar sejumlah pihak.',
                'summary' => 'Badan Reserse Kriminal (Bareskrim) Polri menetapkan tiga orang sebagai tersangka dalam perkara dugaan tindak pidana penyadapan ilegal yang menyita perhatian publik pada November 2026. Penyidik terus mendalami alat bukti digital serta motif di balik praktik intersepsi komunikasi tanpa hak yang melanggar undang-undang perlindungan data dan telekomunikasi.',
                'source_name' => 'Hukumonline',
                'source_url' => 'https://www.hukumonline.com/berita/a/geger-penyadapan-ilegal-pejabat-negara-hingga-pengusaha--bareskrim-tetapkan-3-tersangka-lt6aea19b3534f4/',
            ],
            [
                'category' => 'hukum-kriminal',
                'title' => 'Presiden Minta Proses Hukum Kasus Dugaan Penyadapan Berjalan Objektif dan Transparan',
                'excerpt' => 'Kepala negara menegaskan tidak boleh ada intervensi dalam penanganan perkara hukum demi menjaga kepercayaan publik.',
                'summary' => 'Menanggapi perkembangan kasus dugaan penyadapan ilegal yang sedang ditangani aparat penegak hukum, Presiden Prabowo Subianto meminta agar seluruh proses penyidikan berjalan secara objektif, profesional, dan transparan. Pemerintah menegaskan komitmennya terhadap supremasi hukum serta perlindungan privasi warga negara sesuai konstitusi.',
                'source_name' => 'Sekretariat Negara RI',
                'source_url' => 'https://www.setneg.go.id/listcontent/listberita/berita_presiden_dan_pemerintah/10',
            ],
            [
                'category' => 'hukum-kriminal',
                'title' => 'Evaluasi Efektivitas Checks and Balances Aparat Penegak Hukum Pasca Berlakunya KUHP Nasional',
                'excerpt' => 'Pengawasan antarlembaga penegak hukum dinilai krusial agar implementasi hukum pidana baru berjalan akuntabel.',
                'summary' => 'Kajian kebijakan hukum terbaru menyoroti pentingnya mekanisme pengawasan silang (checks and balances) antara kepolisian, kejaksaan, dan lembaga peradilan pasca pemberlakuan Kitab Undang-Undang Hukum Pidana (KUHP) Nasional. Transparansi dalam tahap penyidikan hingga penuntutan menjadi kunci untuk mencegah penyalahgunaan wewenang dan menjamin kepastian hukum.',
                'source_name' => 'The Indonesian Institute',
                'source_url' => 'https://www.theindonesianinstitute.com/pendidikan-indonesia-menuju-indonesia-emas-2045/',
            ],
            [
                'category' => 'hukum-kriminal',
                'title' => 'Memahami Jerat Pidana Penyadapan Ilegal dalam Regulasi Hukum Positif Indonesia',
                'excerpt' => 'Intersepsi informasi elektronik tanpa dasar hukum yang sah diancam sanksi pidana berat berdasarkan UU ITE dan KUHP.',
                'summary' => 'Dalam sistem hukum Indonesia, penyadapan atau intersepsi terhadap komunikasi pribadi maupun sistem elektronik hanya dapat dilakukan oleh aparat berwenang dalam kerangka penegakan hukum yang ketat. Pelanggaran terhadap ketentuan tersebut dijerat dengan ancaman pidana penjara dan denda sebagaimana diatur dalam Undang-Undang ITE serta pembaruan hukum pidana nasional.',
                'source_name' => 'Hukumonline',
                'source_url' => 'https://www.hukumonline.com/stories/article/lt6af08fd96d461/mengenal-tindak-pidana-penyadapan-ilegal/',
            ],
            [
                'category' => 'hukum-kriminal',
                'title' => 'Reformasi Hukum Pidana Nasional 2026: Tantangan Implementasi Tiga Aturan Pidana Baru',
                'excerpt' => 'Sosialisasi kepada aparat dan masyarakat terus digencarkan untuk menyelaraskan penerapan regulasi pidana yang baru berlaku.',
                'summary' => 'Tahun 2026 menandai babak baru reformasi hukum pidana di Indonesia dengan berlakunya aturan pidana nasional yang menggantikan warisan kolonial. Kementerian Hukum bersama praktisi dan akademisi terus mengevaluasi tantangan teknis di lapangan, termasuk penyesuaian pedoman pemidanaan alternatif dan penguatan keadilan restoratif.',
                'source_name' => 'Kemenkum RI / Hukumonline',
                'source_url' => 'https://www.hukumonline.com/berita/a/sejumlah-permasalahan-pada-hukum-pidana-nasional-yang-baru-lt6a605571cd464/',
            ],

            // ================= GAYA HIDUP (5) =================
            [
                'category' => 'gaya-hidup',
                'title' => 'Hari Kesehatan Nasional 2026: Komitmen Perluasan Layanan Kesehatan Merata untuk Semua',
                'excerpt' => 'Peringatan HKN November 2026 menjadi momentum memperkuat gaya hidup preventif dan akses fasilitas kesehatan primer.',
                'summary' => 'Peringatan Hari Kesehatan Nasional pada November 2026 menegaskan kembali komitmen bersama untuk mewujudkan akses layanan kesehatan yang merata bagi seluruh lapisan masyarakat. Para pemangku kepentingan kesehatan mendorong masyarakat menerapkan pola hidup aktif, menjaga asupan gizi seimbang, serta rutin melakukan deteksi dini penyakit tidak menular.',
                'source_name' => 'ProHealth.id',
                'source_url' => 'https://prohealth.id/hari-kesehatan-nasional-berjanji-wujudkan-sehat-untuk-semua/',
            ],
            [
                'category' => 'gaya-hidup',
                'title' => 'Festival Rangkarang di Probolinggo Dorong Kebangkitan Wisata Pesisir dan Ekonomi Kreatif Daerah',
                'excerpt' => 'Kolaborasi pemerintah daerah dan sektor swasta dalam Seven Beach Festival sukses menarik ribuan pengunjung.',
                'summary' => 'Gelaran Festival Rangkarang dalam rangkaian Seven Beach Festival 2026 di Paiton, Probolinggo, berhasil menggerakkan roda ekonomi kreatif dan pariwisata pesisir Jawa Timur. Acara budaya dan wisata bahari hasil kolaborasi pemerintah kabupaten dengan dunia usaha tersebut memberi ruang promosi langsung bagi pelaku UMKM kuliner, kerajinan lokal, dan destinasi pantai daerah.',
                'source_name' => 'TIMES Indonesia',
                'source_url' => 'https://timesindonesia.co.id/read/news/617523/festival-rangkarang-dongkrak-wisata-pesisir-dan-ekonomi-probolinggo',
            ],
            [
                'category' => 'gaya-hidup',
                'title' => 'Tren Wellness Tourism Makin Diminati Wisatawan Nusantara Sepanjang Akhir 2026',
                'excerpt' => 'Wisata kebugaran yang memadukan alam, relaksasi, dan kuliner sehat menggeser pola liburan konvensional.',
                'summary' => 'Tren pariwisata kebugaran atau wellness tourism semakin digemari oleh masyarakat urban Indonesia sepanjang 2026. Alih-alih sekadar mengunjungi pusat perbelanjaan, banyak wisatawan kini memilih destinasi alam yang menawarkan paket relaksasi, meditasi, olahraga luar ruang, serta pengalaman kuliner sehat berbasis rempah tradisional Nusantara.',
                'source_name' => 'Vonis.id',
                'source_url' => 'https://vonis.id/tren-wellness-tourism-gaya-hidup-2026/',
            ],
            [
                'category' => 'gaya-hidup',
                'title' => 'Pemprov Riau Siapkan Empat Langkah Strategis Pengembangan Medical Tourism Daerah',
                'excerpt' => 'Peningkatan kualitas rumah sakit dan konektivitas transportasi menjadi fokus untuk menarik wisatawan medis domestik.',
                'summary' => 'Pemerintah Provinsi Riau merumuskan empat langkah strategis guna membangun ekosistem wisata medis (medical tourism) yang kompetitif di Sumatra. Upaya tersebut meliputi standardisasi layanan rumah sakit unggulan, kolaborasi dengan pelaku industri perhotelan, promosi terpadu, serta peningkatan kenyamanan akses transportasi bagi pasien dan keluarga.',
                'source_name' => 'Media Center Riau',
                'source_url' => 'https://mediacenter.riau.go.id/read/sekdaprov-syahrial-abdi-beberkan-4-langkah-penting-bangun-medical-tourism-riau',
            ],
            [
                'category' => 'gaya-hidup',
                'title' => 'Dorong Gaya Hidup Sehat dan Literasi Finansial, OCBC Gelar Disney Run Jakarta 2026',
                'excerpt' => 'Ribuan pelari dari berbagai kalangan berpartisipasi dalam ajang lari keluarga yang memadukan olahraga dan edukasi keuangan.',
                'summary' => 'OCBC bersama mitra strategis menghadirkan ajang lari Disney Run Jakarta 2026 pada November ini sebagai bagian dari kampanye kesehatan fisik sekaligus kesehatan finansial masyarakat. Kegiatan olahraga massal tersebut dirancang ramah keluarga guna menumbuhkan kebiasaan hidup aktif di tengah rutinitas perkotaan.',
                'source_name' => 'PR Newswire Indonesia',
                'source_url' => 'https://www.prnewswire.com/id/rilis-berita/dukung-kesehatan-finansial-dan-gaya-hidup-sehat-ocbc-hadirkan-disney-run-jakarta-2026-302931907.html',
            ],

            // ================= PRESS RELEASE (5) =================
            [
                'category' => 'press-release',
                'title' => 'Pasar Digital Indonesia Capai USD 99 Miliar, Pemerintah Perkuat Ekosistem Startup Global',
                'excerpt' => 'Menteri Komunikasi dan Digital Meutya Hafid menegaskan dukungan pemerintah bagi startup lokal untuk menembus pasar internasional.',
                'summary' => 'Dalam gelaran Startup Challenge 2026 Pitch Day di Jakarta Selatan, Menteri Komunikasi dan Digital Meutya Hafid menyampaikan bahwa nilai ekonomi digital Indonesia telah mencapai USD 99 miliar. Pemerintah berkomitmen memperkuat ekosistem pendampingan, regulasi yang kondusif, serta konektivitas talenta agar startup karya anak bangsa mampu bersaing di pasar global.',
                'source_name' => 'Siaran Pers Komdigi RI',
                'source_url' => 'https://www.komdigi.go.id/berita/siaran-pers/detail/pasar-digital-ri-capai-usd99-miliar-meutya-hafid-pemerintah-bangun-ekosistem-startup-bidik-pasar-global',
            ],
            [
                'category' => 'press-release',
                'title' => 'Danantara Indonesia Gelar DTIIF 2026 untuk Perkuat Kolaborasi Investasi dan Teknologi Global',
                'excerpt' => 'Forum investasi internasional mempertemukan pengelola dana global dengan proyek strategis transisi teknologi di Indonesia.',
                'summary' => 'Danantara Indonesia resmi membuka Danantara Technology and Investment Innovation Forum (DTIIF) 2026 pada awal November sebagai wadah kolaborasi antara investor institusional global dan pelaku industri nasional. Forum ini berfokus pada pembiayaan jangka panjang di sektor infrastruktur digital, energi bersih, dan hilirisasi industri bernilai tambah tinggi.',
                'source_name' => 'Danantara Indonesia',
                'source_url' => 'https://www.danantaraindonesia.co.id/media-center/press-releases',
            ],
            [
                'category' => 'press-release',
                'title' => 'Ekosistem Startup Indonesia Tumbuh 8,3 Persen pada 2026 dengan Fokus Profitabilitas',
                'excerpt' => 'Laporan ekosistem startup mencatat pergeseran strategi pendiri usaha rintisan menuju model bisnis berkelanjutan.',
                'summary' => 'Berdasarkan laporan ekosistem startup terbaru pada November 2026, pertumbuhan ekosistem rintisan Indonesia tercatat naik 8,3 persen dengan Indonesia menempati posisi tiga besar di Asia Tenggara. Para pendiri startup kini lebih memprioritaskan pendapatan berulang dan profitabilitas jangka panjang, terutama di sektor teknologi iklim, kesehatan digital, dan kecerdasan buatan terapan.',
                'source_name' => 'StartupBlink / Startup Edition',
                'source_url' => 'https://blog.mean.ceo/startups-indonesia-news-november-2026/',
            ],
            [
                'category' => 'press-release',
                'title' => 'Bursa Efek Indonesia Terbitkan Pembaruan Kebijakan Transparansi Pasar Modal November 2026',
                'excerpt' => 'Langkah penguatan tata kelola dan keterbukaan informasi emiten ditujukan untuk melindungi investor ritel maupun institusi.',
                'summary' => 'Bursa Efek Indonesia (IDX) melalui siaran pers resmi November 2026 mengumumkan pembaruan mekanisme keterbukaan informasi bagi perusahaan tercatat. Kebijakan ini bertujuan meningkatkan kepercayaan investor di pasar modal domestik sekaligus mempercepat integrasi pelaporan kinerja keuangan emiten secara real-time.',
                'source_name' => 'Bursa Efek Indonesia (IDX)',
                'source_url' => 'https://www.idx.co.id/en/news/press-release/2590',
            ],
            [
                'category' => 'press-release',
                'title' => 'BeritaPraktis Resmi Diluncurkan sebagai Platform Berita & Press Release Gratis Indonesia',
                'excerpt' => 'Platform media terkurasi hadir untuk membantu UMKM, startup, instansi, dan jurnalis warga mempublikasikan berita berkualitas secara gratis.',
                'summary' => 'BeritaPraktis resmi hadir sebagai portal berita independen sekaligus platform publikasi press release gratis dan post berita gratis di Indonesia. Mengusung standar kurasi redaksi yang ketat—minimal 900 kata, maksimal 2 tautan referensi, wajib foto utama, serta bebas judi online dan pornografi—BeritaPraktis dirancang ramah SEO News dan indeksasi AI Search.',
                'source_name' => 'Redaksi BeritaPraktis',
                'source_url' => 'https://beritapraktis.com/press-release-gratis',
            ],
        ];

        // 40 foto fotografi nyata unik dari Pexels (5 foto berbeda per kategori, tanpa duplikasi)
        $pexelsPool = [
            'politik' => ['39502725', '10344433', '39420194', '35380038', '38004630'],
            'bisnis-ekonomi' => ['40441676', '37945815', '11045736', '7078373', '7580827'],
            'teknologi' => ['37605911', '207580', '16018144', '6804613', '1181341'],
            'pertanian' => ['33597069', '34204300', '36775904', '32954693', '34360581'],
            'pendidikan' => ['31367511', '31367512', '31367502', '31367499', '31367504'],
            'hukum-kriminal' => ['6077328', '37726687', '8693379', '6077326', '34817075'],
            'gaya-hidup' => ['16961403', '28735804', '29022599', '31498691', '38128917'],
            'press-release' => ['34774346', '29708261', '29708269', '29708265', '7413915'],
        ];
        $categoryPhotoCounter = [];

        foreach ($articles as $index => $item) {
            $catSlug = $item['category'];
            $categoryId = $categories[$catSlug] ?? $categories->first();
            $slug = Str::slug($item['title']);

            $catPos = $categoryPhotoCounter[$catSlug] ?? 0;
            $categoryPhotoCounter[$catSlug] = $catPos + 1;

            $photoId = $pexelsPool[$catSlug][$catPos] ?? null;
            $featuredImage = $photoId
                ? "https://images.pexels.com/photos/{$photoId}/pexels-photo-{$photoId}.jpeg?auto=compress&cs=tinysrgb&w=1200"
                : 'images/seed/' . $catSlug . '.jpg';

            $content = $this->buildEthicalEditorialBody(
                $item['title'],
                $item['summary'],
                $item['source_name'],
                $item['source_url']
            );

            $wordCount = $validator->countWords($content);
            $links = $validator->extractOutboundLinks($content);

            Article::updateOrCreate(
                ['slug' => $slug],
                [
                    'user_id' => $author->id,
                    'category_id' => $categoryId,
                    'title' => $item['title'],
                    'excerpt' => $item['excerpt'],
                    'content' => $content,
                    'featured_image' => $featuredImage,
                    'image_alt' => 'Foto editorial: ' . $item['title'],
                    'image_caption' => 'Foto: Pexels (Royalty-Free) | Sumber informasi: ' . $item['source_name'],
                    'word_count' => $wordCount,
                    'outbound_links_count' => count($links),
                    'outbound_links' => $links,
                    'status' => 'published',
                    'views_count' => rand(45, 480),
                    'published_at' => now()->subHours(($index + 1) * 2),
                    'reviewed_by' => $author->id,
                    'reviewed_at' => now()->subHours(($index + 1) * 2),
                ]
            );
        }
    }

    private function buildEthicalEditorialBody(string $title, string $summary, string $sourceName, string $sourceUrl): string
    {
        return <<<HTML
<p><strong>JAKARTA, BeritaPraktis</strong> — {$summary}</p>

<h2>Konteks dan Perkembangan Terkini</h2>
<p>Perkembangan isu mengenai <em>{$title}</em> menjadi salah satu perhatian utama publik serta pemangku kepentingan di Indonesia pada pertengahan November 2026. Di tengah dinamika sosial, ekonomi, dan transformasi digital yang berlangsung cepat, kejelasan informasi yang akurat serta berbasis data resmi sangat dibutuhkan oleh masyarakat luas.</p>
<p>Berbagai pihak menilai bahwa langkah-langkah strategis yang diambil dalam beberapa pekan terakhir mencerminkan upaya serius untuk menjawab tantangan jangka pendek sekaligus meletakkan fondasi jangka panjang. Sinergi antara pemerintah pusat, pemerintah daerah, pelaku industri, akademisi, serta masyarakat sipil menjadi faktor penentu keberhasilan implementasi di lapangan.</p>

<h2>Dampak bagi Masyarakat dan Pelaku Usaha</h2>
<p>Bagi masyarakat umum dan pelaku usaha nasional, kebijakan maupun inovasi yang disorot dalam perkembangan ini membawa implikasi langsung terhadap aktivitas sehari-hari. Kepastian regulasi, stabilitas pasokan, serta kemudahan akses terhadap informasi publik memungkinkan masyarakat mengambil keputusan yang lebih terukur, baik dalam konteks pekerjaan, pendidikan, maupun perencanaan usaha.</p>
<p>Selain itu, para pengamat mengingatkan pentingnya pengawasan berkelanjutan dan keterbukaan data agar setiap program yang dijalankan tetap tepat sasaran. Evaluasi berkala di tingkat daerah juga diperlukan supaya manfaat kebijakan dapat dirasakan merata, tidak hanya terpusat di kota-kota besar tetapi juga menjangkau wilayah penyangga dan daerah tertinggal.</p>

<h2>Prospek ke Depan</h2>
<p>Memasuki penghujung tahun 2026 dan persiapan menuju agenda pembangunan 2027, konsistensi pelaksanaan di lapangan akan menjadi ujian utama. Transparansi komunikasi publik dan partisipasi aktif seluruh elemen bangsa diharapkan terus terjaga demi mendorong kemajuan nasional yang inklusif dan berkelanjutan.</p>

<blockquote>
<p><strong>Sumber Saduran &amp; Referensi Resmi:</strong> Artikel ini disadur dan dirangkum secara etis sesuai kaidah jurnalistik dari laporan <a href="{$sourceUrl}" target="_blank" rel="noopener">{$sourceName}</a>.</p>
</blockquote>
HTML;
    }
}
