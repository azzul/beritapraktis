@extends('layouts.app')

@section('title', 'Auto-Sadur Berita RSS — Admin | BeritaPraktis')

@section('content')
<div class="mx-auto max-w-5xl px-4 py-8 sm:px-6">
    @include('admin.partials.nav')

    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-900 pb-4">
        <div>
            <h1 class="font-serif text-2xl font-bold text-gray-900">Auto-Sadur Berita (RSS Feed Importer)</h1>
            <p class="mt-1 text-xs text-gray-600">
                Ambil berita terbaru dari RSS resmi, saring kata kunci terlarang secara otomatis, dan sertakan tautan sumber saduran sesuai etika jurnalistik.
            </p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="text-xs font-semibold text-gray-600 hover:text-red-700">&larr; Dashboard Admin</a>
    </div>

    <!-- Preset Quick Fill -->
    <div class="mt-6 rounded-md border border-gray-200 bg-white p-5">
        <h2 class="text-xs font-bold uppercase tracking-wider text-gray-900">Preset RSS Resmi (Klik untuk isi otomatis)</h2>
        <div class="mt-3 flex flex-wrap gap-2">
            @foreach($presets as $p)
                <button type="button"
                        onclick="fillPreset(@js($p['name']), @js($p['url']), @js($p['category_slug']))"
                        class="rounded border border-gray-300 bg-gray-50 px-3 py-1.5 text-xs font-semibold text-gray-800 hover:bg-gray-100 cursor-pointer">
                    {{ $p['name'] }}
                </button>
            @endforeach
        </div>
    </div>

    <form action="{{ route('admin.sadur.import') }}" method="POST" class="mt-6 rounded-md border border-gray-200 bg-white p-6 space-y-4">
        @csrf
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-xs font-semibold text-gray-800">Nama Media Sumber (Untuk Atribusi)</label>
                <input type="text" id="source_name" name="source_name" required placeholder="Contoh: ANTARA News"
                       class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-800">URL RSS Feed</label>
                <input type="url" id="feed_url" name="feed_url" required placeholder="https://www.antaranews.com/rss/terkini.xml"
                       class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm">
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-xs font-semibold text-gray-800">Kategori Tujuan</label>
                <select id="category_id" name="category_id" required class="mt-1 w-full rounded border border-gray-300 bg-white px-3 py-2 text-sm">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" data-slug="{{ $cat->slug }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-800">Jumlah Berita Diambil (Maks 15)</label>
                <input type="number" name="limit" value="5" min="1" max="15" required
                       class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm">
            </div>
        </div>

        <label class="flex items-center gap-2 text-sm font-medium text-gray-800 cursor-pointer">
            <input type="checkbox" name="publish_now" value="1" class="h-4 w-4 rounded border-gray-300 text-red-700">
            <span>Langsung terbitkan (Jika tidak dicentang, masuk ke antrean Review Admin dulu)</span>
        </label>

        <button type="submit" class="rounded-md bg-red-700 px-5 py-2.5 text-sm font-bold text-white hover:bg-red-800 cursor-pointer">
            Ambil &amp; Sadur Berita Sekarang
        </button>
    </form>

    <div class="mt-6 rounded-md border border-gray-200 bg-gray-50 p-4 text-xs text-gray-600 space-y-2">
        <p>
            <strong>Otomatisasi Harian (Cron / Task Scheduler):</strong> Anda juga dapat menjadwalkan perintah
            <code>php artisan news:auto-sadur --limit=3 --publish</code> di server hosting/VPS agar portal terisi berita terupdate setiap hari secara otomatis.
        </p>
        <p>
            <strong>Foto Otomatis Unik (Opsional):</strong> Isi <code>PIXABAY_API_KEY</code> pada file <code>.env</code> (gratis &amp; instan di pixabay.com/api/docs) agar setiap berita saduran otomatis mendapat foto fotografi nyata yang sesuai judul dan tidak kembar. Jika kosong, sistem memakai rotasi foto stok per kategori.
        </p>
    </div>
</div>

<script>
function fillPreset(name, url, catSlug) {
    document.getElementById('source_name').value = name;
    document.getElementById('feed_url').value = url;
    const select = document.getElementById('category_id');
    for (const opt of select.options) {
        if (opt.dataset.slug === catSlug) {
            opt.selected = true;
            break;
        }
    }
}
</script>
@endsection
