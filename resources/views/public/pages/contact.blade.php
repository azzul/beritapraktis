@extends('layouts.app')

@section('title', 'Kontak Redaksi & Takedown | BeritaPraktis')
@section('meta_description', 'Hubungi tim redaksi BeritaPraktis untuk hak jawab, koreksi berita, kerja sama press release, atau pengajuan penghapusan konten (takedown).')

@section('content')
<div class="mx-auto max-w-4xl px-4 py-10 sm:px-6">
    <header class="border-b border-gray-900 pb-5">
        <span class="text-xs font-bold uppercase tracking-wider text-red-700">Layanan Pembaca</span>
        <h1 class="mt-2 font-serif text-3xl font-bold text-gray-900 sm:text-4xl">Kontak Redaksi</h1>
        <p class="mt-3 text-sm leading-relaxed text-gray-600">
            Kami terbuka terhadap masukan, permintaan koreksi berita, hak jawab, maupun laporan pelanggaran hak cipta dan konten.
        </p>
    </header>

    <div class="mt-8 space-y-6">
        <section class="rounded-md border border-gray-200 bg-white p-6">
            <h2 class="font-serif text-xl font-bold text-gray-900">Saluran Komunikasi Resmi</h2>
            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="rounded-md border border-gray-200 bg-gray-50 p-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500">Email Redaksi &amp; Takedown</h3>
                    <p class="mt-1.5 text-sm font-semibold text-gray-900">
                        <a href="mailto:info@kabarpraktis.biz.id" class="text-red-700 underline">info@kabarpraktis.biz.id</a>
                    </p>
                    <p class="mt-1 text-xs text-gray-600">Untuk hak jawab, koreksi data, klaim hak cipta/DMCA, dan pengaduan konten.</p>
                </div>
                <div class="rounded-md border border-gray-200 bg-gray-50 p-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500">Jam Operasional Moderasi</h3>
                    <p class="mt-1.5 text-sm font-semibold text-gray-900">Senin – Minggu, 08.00 – 21.00 WIB</p>
                    <p class="mt-1 text-xs text-gray-600">Pengiriman press release melalui dashboard diproses maksimal 1x24 jam.</p>
                </div>
            </div>
        </section>

        <section class="rounded-md border border-gray-200 bg-white p-6">
            <h2 class="font-serif text-xl font-bold text-gray-900">Prosedur Pengajuan Hak Jawab &amp; Takedown</h2>
            <p class="mt-3 text-sm leading-relaxed text-gray-700">
                Agar laporan Anda dapat segera ditindaklanjuti oleh tim redaksi, mohon sertakan informasi berikut saat mengirim email:
            </p>
            <ul class="mt-3 list-disc space-y-2 pl-5 text-sm leading-relaxed text-gray-700">
                <li><strong>URL lengkap artikel</strong> yang dimaksud di <code>beritapraktis.biz.id</code>.</li>
                <li><strong>Identitas pelapor/instansi</strong> serta kontak yang dapat dihubungi untuk verifikasi.</li>
                <li><strong>Uraian keberatan, data pembanding, atau bukti kepemilikan hak cipta</strong> (untuk permohonan takedown foto/materi).</li>
            </ul>
            <p class="mt-4 text-xs text-gray-600">
                Setiap pengajuan hak jawab atau klaim hak cipta yang valid akan kami proses dalam waktu maksimal 2x24 jam kerja.
            </p>
        </section>
    </div>
</div>
@endsection
