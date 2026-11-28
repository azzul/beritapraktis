@extends('layouts.app')

@section('title', 'Susunan Redaksi | BeritaPraktis')
@section('meta_description', 'Informasi struktur pengelolaan redaksi, kurasi konten, dan verifikasi berita di portal BeritaPraktis.')

@section('content')
<div class="mx-auto max-w-4xl px-4 py-10 sm:px-6">
    <header class="border-b border-gray-900 pb-5">
        <span class="text-xs font-bold uppercase tracking-wider text-red-700">Struktur Pengelola</span>
        <h1 class="mt-2 font-serif text-3xl font-bold text-gray-900 sm:text-4xl">Susunan Redaksi BeritaPraktis</h1>
        <p class="mt-3 text-sm leading-relaxed text-gray-600">
            Pengelolaan redaksi, moderasi siaran pers, dan pengawasan standar editorial di BeritaPraktis dijalankan oleh tim yang bertanggung jawab memastikan kepatuhan terhadap kode etik dan hukum yang berlaku.
        </p>
    </header>

    <div class="mt-8 space-y-6">
        <section class="rounded-md border border-gray-200 bg-white p-6">
            <h2 class="font-serif text-xl font-bold text-gray-900">Struktur Pengelolaan Media</h2>
            <dl class="mt-4 divide-y divide-gray-100 text-sm">
                <div class="grid grid-cols-1 gap-1 py-3 sm:grid-cols-3">
                    <dt class="font-semibold text-gray-900">Penerbit / Pengelola</dt>
                    <dd class="text-gray-700 sm:col-span-2">BeritaPraktis Digital Media (AzolaTekno)</dd>
                </div>
                <div class="grid grid-cols-1 gap-1 py-3 sm:grid-cols-3">
                    <dt class="font-semibold text-gray-900">Penanggung Jawab Redaksi</dt>
                    <dd class="text-gray-700 sm:col-span-2">Tim Redaksi BeritaPraktis</dd>
                </div>
                <div class="grid grid-cols-1 gap-1 py-3 sm:grid-cols-3">
                    <dt class="font-semibold text-gray-900">Desk Kurasi &amp; Moderasi</dt>
                    <dd class="text-gray-700 sm:col-span-2">Pemeriksaan kelayakan naskah kontributor, press release UMKM/korporasi, dan filter anti-spam/konten ilegal.</dd>
                </div>
                <div class="grid grid-cols-1 gap-1 py-3 sm:grid-cols-3">
                    <dt class="font-semibold text-gray-900">Pengembangan Teknis &amp; SEO</dt>
                    <dd class="text-gray-700 sm:col-span-2">Infrastruktur web, keamanan sistem, dan pemeliharaan indeks berita.</dd>
                </div>
                <div class="grid grid-cols-1 gap-1 py-3 sm:grid-cols-3">
                    <dt class="font-semibold text-gray-900">Email Resmi Redaksi</dt>
                    <dd class="text-gray-700 sm:col-span-2">
                        <a href="mailto:info@kabarpraktis.biz.id" class="font-semibold text-red-700 underline">info@kabarpraktis.biz.id</a>
                    </dd>
                </div>
            </dl>
        </section>

        <section class="rounded-md border border-gray-200 bg-white p-6">
            <h2 class="font-serif text-xl font-bold text-gray-900">Tanggung Jawab Konten &amp; Hak Jawab</h2>
            <p class="mt-3 text-sm leading-relaxed text-gray-700">
                Setiap kontributor yang mempublikasikan artikel atau siaran pers di BeritaPraktis wajib menggunakan identitas akun Google yang valid dan bertanggung jawab penuh atas kebenaran data, kutipan, serta hak cipta gambar yang dilampirkan.
            </p>
            <p class="mt-3 text-sm leading-relaxed text-gray-700">
                Redaksi berhak menyunting judul, memperbaiki ejaan, menandai artikel, atau mencabut tayangan apabila di kemudian hari ditemukan ketidaksesuaian fakta, sengketa hak cipta, maupun pelanggaran terhadap <a href="{{ route('rules') }}" class="font-semibold text-red-700 underline">Aturan Penulisan</a>.
            </p>
        </section>
    </div>
</div>
@endsection
