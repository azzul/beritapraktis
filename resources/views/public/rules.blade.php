@extends('layouts.app')

@section('title', 'Aturan & Pedoman Penulisan Press Release | BeritaPraktis')
@section('meta_description', 'Pedoman resmi pengiriman berita dan press release gratis di BeritaPraktis: minimal 900 kata, maksimal 2 link, wajib foto, dan larangan judi online serta pornografi.')

@section('content')
<div class="mx-auto max-w-4xl px-4 py-10 sm:px-6">
    <header class="border-b border-gray-900 pb-5">
        <span class="text-xs font-bold uppercase tracking-wider text-red-700">Pedoman Redaksi</span>
        <h1 class="mt-2 font-serif text-3xl font-bold text-gray-900 sm:text-4xl">Aturan Artikel &amp; Press Release Gratis</h1>
        <p class="mt-3 text-sm leading-relaxed text-gray-600">
            Untuk menjaga kualitas informasi, keamanan pembaca, dan reputasi media di mesin pencari, setiap kontributor wajib mematuhi aturan berikut sebelum mengirimkan artikel.
        </p>
    </header>

    <div class="mt-8 space-y-6">
        <section class="rounded-md border border-gray-200 bg-white p-6">
            <h2 class="font-serif text-xl font-bold text-gray-900">1. Syarat Teknis Wajib (Terdeteksi Otomatis)</h2>
            <ul class="mt-3 list-disc space-y-2 pl-5 text-sm leading-relaxed text-gray-700">
                <li><strong>Minimal 900 Kata:</strong> Isi artikel wajib terdiri dari minimal 900 kata bersih (tidak termasuk tag HTML). Artikel singkat di bawah 900 kata tidak dapat dikirimkan.</li>
                <li><strong>Maksimal 2 Tautan Keluar (Link):</strong> Anda hanya diperbolehkan menyisipkan maksimal 2 tautan (baik berupa anchor link maupun URL teks) di dalam isi artikel.</li>
                <li><strong>Wajib Foto Utama:</strong> Setiap kiriman wajib menyertakan 1 foto/gambar utama yang relevan (format JPG, PNG, atau WebP, maksimal 2 MB) beserta deskripsi Alt Text yang jelas.</li>
            </ul>
        </section>

        <section class="rounded-md border border-red-200 bg-red-50/60 p-6">
            <h2 class="font-serif text-xl font-bold text-red-900">2. Konten yang Dilarang Keras (Zero Tolerance)</h2>
            <ul class="mt-3 list-disc space-y-2 pl-5 text-sm leading-relaxed text-red-900/90">
                <li><strong>Judi Online:</strong> Segala bentuk promosi, ulasan terselubung, tautan, atau kata kunci terkait slot, togel, casino online, taruhan bola, RTP, dan sejenisnya.</li>
                <li><strong>Game Online Ilegal / Promosi Top-Up Terlarang:</strong> Promosi situs game online terlarang, chip ilegal, atau tautan afiliasi judi berkedok game.</li>
                <li><strong>Pornografi &amp; Konten Dewasa:</strong> Konten vulgar, eksplisit seksual, prostitusi terselubung, maupun tautan ke situs dewasa.</li>
                <li><strong>Hoaks, SARA, &amp; Plagiarisme:</strong> Berita palsu tanpa dasar fakta, ujaran kebencian, serta artikel hasil salin-tempel utuh dari media lain tanpa izin.</li>
            </ul>
            <p class="mt-4 text-xs font-semibold text-red-800">
                Sanksi: Artikel akan langsung ditolak dan akun Google pengirim dapat diblokir permanen oleh Admin.
            </p>
        </section>

        <section class="rounded-md border border-gray-200 bg-white p-6">
            <h2 class="font-serif text-xl font-bold text-gray-900">3. Proses Kurasi &amp; Hak Redaksi</h2>
            <p class="mt-2 text-sm leading-relaxed text-gray-700">
                Setiap artikel yang lolos validasi sistem akan masuk ke antrean redaksi dan mengirimkan notifikasi ke Admin. Redaksi berhak menyetujui, menolak dengan alasan, atau menghapus artikel di kemudian hari apabila ditemukan pelanggaran kebijakan.
            </p>
        </section>
    </div>

    <div class="mt-8 flex flex-wrap gap-3">
        <a href="{{ route('dashboard.articles.create') }}" class="rounded-md bg-red-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-red-800">
            Saya Mengerti, Kirim Berita Sekarang
        </a>
        <a href="{{ route('home') }}" class="rounded-md border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
            Kembali ke Beranda
        </a>
    </div>
</div>
@endsection
