@extends('layouts.app')

@section('title', 'Tentang Kami | BeritaPraktis')
@section('meta_description', 'Profil BeritaPraktis: portal berita independen dan platform publikasi press release gratis Indonesia dengan kurasi redaksi ketat dan standar jurnalisme siber.')

@section('content')
<div class="mx-auto max-w-4xl px-4 py-10 sm:px-6">
    <header class="border-b border-gray-900 pb-5">
        <span class="text-xs font-bold uppercase tracking-wider text-red-700">Profil Media</span>
        <h1 class="mt-2 font-serif text-3xl font-bold text-gray-900 sm:text-4xl">Tentang BeritaPraktis</h1>
        <p class="mt-3 text-sm leading-relaxed text-gray-600">
            Portal berita independen dan platform publikasi siaran pers (press release) terbuka yang mengutamakan akurasi, keterbacaan, dan kebersihan ekosistem informasi di Indonesia.
        </p>
    </header>

    <div class="mt-8 space-y-6">
        <section class="rounded-md border border-gray-200 bg-white p-6">
            <h2 class="font-serif text-xl font-bold text-gray-900">Visi &amp; Misi</h2>
            <p class="mt-3 text-sm leading-relaxed text-gray-700">
                <strong>BeritaPraktis</strong> (<code>beritapraktis.biz.id</code>) hadir untuk menjembatani kebutuhan masyarakat akan berita aktual yang ringkas sekaligus memberi ruang publikasi yang adil bagi UMKM, startup, komunitas, lembaga pendidikan, dan korporasi di Indonesia tanpa hambatan biaya tayang.
            </p>
            <ul class="mt-3 list-disc space-y-2 pl-5 text-sm leading-relaxed text-gray-700">
                <li>Menyajikan informasi lintas topik (Nasional, Bisnis &amp; Ekonomi, Teknologi, Hukum, Pendidikan, Kesehatan, Pertanian, dan Gaya Hidup) secara lugas dan berimbang.</li>
                <li>Menyediakan kanal <strong>Press Release Gratis</strong> dan <strong>Post Berita Gratis</strong> dengan proses moderasi redaksi yang transparan.</li>
                <li>Menjaga ruang digital Indonesia dari penyebaran hoaks, ujaran kebencian, promosi judi online, game online ilegal, dan pornografi.</li>
            </ul>
        </section>

        <section class="rounded-md border border-gray-200 bg-white p-6">
            <h2 class="font-serif text-xl font-bold text-gray-900">Standar Kurasi &amp; Verifikasi</h2>
            <p class="mt-3 text-sm leading-relaxed text-gray-700">
                Setiap tulisan yang masuk—baik dari kontributor independen, humas instansi, maupun kurasi berita—harus melewati pemeriksaan teknis dan editorial sebelum terbit:
            </p>
            <ul class="mt-3 list-disc space-y-2 pl-5 text-sm leading-relaxed text-gray-700">
                <li>Kedalaman tulisan minimal 900 kata agar pembaca memperoleh konteks yang utuh, bukan sekadar potongan informasi singkat.</li>
                <li>Penyaringan otomatis dan manual terhadap kata kunci terlarang, tautan spam, serta materi yang melanggar hukum di Indonesia.</li>
                <li>Pencantuman sumber rujukan yang jelas pada berita saduran atau rilis resmi.</li>
            </ul>
        </section>

        <section class="rounded-md border border-gray-200 bg-white p-6">
            <h2 class="font-serif text-xl font-bold text-gray-900">Transparansi &amp; Tanggung Jawab Publik</h2>
            <p class="mt-3 text-sm leading-relaxed text-gray-700">
                BeritaPraktis tunduk pada <a href="{{ route('pages.cyber-guidelines') }}" class="font-semibold text-red-700 underline">Pedoman Media Siber</a> serta membuka pintu seluas-luasnya bagi masyarakat untuk menyampaikan koreksi, hak jawab, maupun permohonan penghapusan materi melalui halaman <a href="{{ route('pages.contact') }}" class="font-semibold text-red-700 underline">Kontak Redaksi</a>.
            </p>
        </section>
    </div>
</div>
@endsection
