@extends('layouts.app')

@section('title', 'Pedoman Media Siber | BeritaPraktis')
@section('meta_description', 'Pedoman Pemberitaan Media Siber BeritaPraktis mencakup verifikasi berita, konten buatan pengguna (UGC), ralat, hak jawab, pencabutan berita, dan hak cipta.')

@section('content')
<div class="mx-auto max-w-4xl px-4 py-10 sm:px-6">
    <header class="border-b border-gray-900 pb-5">
        <span class="text-xs font-bold uppercase tracking-wider text-red-700">Etika Jurnalistik</span>
        <h1 class="mt-2 font-serif text-3xl font-bold text-gray-900 sm:text-4xl">Pedoman Media Siber</h1>
        <p class="mt-3 text-sm leading-relaxed text-gray-600">
            BeritaPraktis mengadopsi prinsip-prinsip Pedoman Pemberitaan Media Siber yang berlaku di Indonesia untuk menjamin kebebasan berekspresi yang bertanggung jawab.
        </p>
    </header>

    <div class="mt-8 space-y-6 text-sm leading-relaxed text-gray-700">
        <section class="rounded-md border border-gray-200 bg-white p-6">
            <h2 class="font-serif text-xl font-bold text-gray-900">1. Ruang Lingkup</h2>
            <p class="mt-2">
                Pedoman ini berlaku bagi seluruh konten berita, siaran pers, opini, dan materi multimedia yang dipublikasikan melalui domain <code>beritapraktis.biz.id</code>, baik yang disusun oleh tim redaksi maupun dikirimkan oleh kontributor terdaftar.
            </p>
        </section>

        <section class="rounded-md border border-gray-200 bg-white p-6">
            <h2 class="font-serif text-xl font-bold text-gray-900">2. Verifikasi dan Keberimbangan Berita</h2>
            <p class="mt-2">
                Pada prinsipnya setiap berita wajib melalui proses verifikasi. Berita yang dapat merugikan pihak lain memerlukan verifikasi pada berita yang sama untuk memenuhi prinsip akurasi dan keberimbangan. Untuk berita saduran atau rangkuman dari sumber resmi, redaksi wajib mencantumkan rujukan sumber secara eksplisit.
            </p>
        </section>

        <section class="rounded-md border border-gray-200 bg-white p-6">
            <h2 class="font-serif text-xl font-bold text-gray-900">3. Isi Buatan Pengguna (User Generated Content &amp; Press Release)</h2>
            <p class="mt-2">
                BeritaPraktis membuka ruang partisipasi publik melalui pengiriman berita dan press release gratis. Kontributor wajib menyetujui bahwa materi yang dikirim tidak mengandung unsur hoaks, fitnah, SARA, pornografi, perjudian online, maupun pelanggaran hak cipta. Redaksi berhak menyunting atau menolak naskah yang tidak memenuhi syarat.
            </p>
        </section>

        <section class="rounded-md border border-gray-200 bg-white p-6">
            <h2 class="font-serif text-xl font-bold text-gray-900">4. Ralat, Koreksi, dan Hak Jawab</h2>
            <p class="mt-2">
                Ralat, koreksi, dan hak jawab mengacu pada Undang-Undang Pers dan Kode Etik Jurnalistik. Apabila terdapat kekeliruan fakta atau keberatan dari pihak yang diberitakan, pembaca dapat mengajukan permohonan resmi melalui halaman <a href="{{ route('pages.contact') }}" class="font-semibold text-red-700 underline">Kontak Redaksi</a>.
            </p>
        </section>

        <section class="rounded-md border border-gray-200 bg-white p-6">
            <h2 class="font-serif text-xl font-bold text-gray-900">5. Pencabutan Berita</h2>
            <p class="mt-2">
                Berita yang sudah dipublikasikan tidak dapat dicabut karena alasan penyensoran dari pihak luar redaksi, kecuali terkait masalah SARA, kesusilaan, masa depan anak, pengalaman traumatik korban, sengketa hak cipta yang terbukti sah, atau berdasarkan pertimbangan khusus redaksi demi kepentingan publik.
            </p>
        </section>

        <section class="rounded-md border border-gray-200 bg-white p-6">
            <h2 class="font-serif text-xl font-bold text-gray-900">6. Hak Cipta</h2>
            <p class="mt-2">
                BeritaPraktis menghormati hak kekayaan intelektual. Pemilik hak cipta yang merasa karyanya (teks maupun foto) dimuat tanpa izin yang sah dapat mengirimkan pemberitahuan penghapusan (takedown) ke <a href="mailto:info@kabarpraktis.biz.id" class="font-semibold text-red-700 underline">info@kabarpraktis.biz.id</a>.
            </p>
        </section>
    </div>
</div>
@endsection
