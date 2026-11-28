@extends('layouts.app')

@section('title', 'Kebijakan Privasi | BeritaPraktis')
@section('meta_description', 'Kebijakan Privasi BeritaPraktis mengenai pengumpulan data akun Google kontributor, cookie sesi, keamanan informasi, dan hak pengguna.')

@section('content')
<div class="mx-auto max-w-4xl px-4 py-10 sm:px-6">
    <header class="border-b border-gray-900 pb-5">
        <span class="text-xs font-bold uppercase tracking-wider text-red-700">Legal &amp; Privasi</span>
        <h1 class="mt-2 font-serif text-3xl font-bold text-gray-900 sm:text-4xl">Kebijakan Privasi</h1>
        <p class="mt-3 text-sm leading-relaxed text-gray-600">
            Pembaruan terakhir: {{ date('d F Y') }}. Kebijakan ini menjelaskan bagaimana BeritaPraktis mengumpulkan, menggunakan, dan melindungi data pribadi pengunjung serta kontributor.
        </p>
    </header>

    <div class="mt-8 space-y-6 text-sm leading-relaxed text-gray-700">
        <section class="rounded-md border border-gray-200 bg-white p-6">
            <h2 class="font-serif text-xl font-bold text-gray-900">1. Informasi yang Kami Kumpulkan</h2>
            <ul class="mt-2 list-disc space-y-2 pl-5">
                <li><strong>Data Akun Kontributor (Google OAuth):</strong> Saat Anda masuk sebagai kontributor menggunakan akun Google, kami menyimpan nama, alamat email, dan foto profil publik untuk keperluan atribusi penulis dan moderasi naskah.</li>
                <li><strong>Data Teknis &amp; Log Server:</strong> Informasi standar seperti alamat IP, jenis peramban, halaman yang diakses, dan waktu akses untuk menjaga keamanan server serta mencegah penyalahgunaan.</li>
            </ul>
        </section>

        <section class="rounded-md border border-gray-200 bg-white p-6">
            <h2 class="font-serif text-xl font-bold text-gray-900">2. Penggunaan Cookie</h2>
            <p class="mt-2">
                BeritaPraktis menggunakan cookie esensial untuk mengelola sesi login (<code>beritapraktis-session</code>) dan perlindungan keamanan formulir (<code>XSRF-TOKEN</code>). Kami tidak menjual data pribadi pengguna kepada pihak ketiga mana pun.
            </p>
        </section>

        <section class="rounded-md border border-gray-200 bg-white p-6">
            <h2 class="font-serif text-xl font-bold text-gray-900">3. Penggunaan Informasi</h2>
            <p class="mt-2">
                Data yang terkumpul digunakan untuk menampilkan nama penulis pada artikel yang disetujui, memproses antrean moderasi redaksi, mengirim notifikasi status akun, serta menjaga kepatuhan terhadap aturan anti-spam.
            </p>
        </section>

        <section class="rounded-md border border-gray-200 bg-white p-6">
            <h2 class="font-serif text-xl font-bold text-gray-900">4. Tautan ke Situs Eksternal</h2>
            <p class="mt-2">
                Artikel atau press release yang tayang dapat memuat tautan referensi menuju situs pihak ketiga. BeritaPraktis tidak bertanggung jawab atas kebijakan privasi atau konten di luar domain <code>beritapraktis.biz.id</code>.
            </p>
        </section>

        <section class="rounded-md border border-gray-200 bg-white p-6">
            <h2 class="font-serif text-xl font-bold text-gray-900">5. Hak Pengguna &amp; Penghapusan Data</h2>
            <p class="mt-2">
                Anda berhak meminta pembaruan atau penghapusan data akun kontributor Anda kapan saja dengan menghubungi kami melalui email <a href="mailto:info@kabarpraktis.biz.id" class="font-semibold text-red-700 underline">info@kabarpraktis.biz.id</a>.
            </p>
        </section>
    </div>
</div>
@endsection
