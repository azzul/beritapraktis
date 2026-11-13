@extends('layouts.app')

@section('title', 'Tulis Berita / Press Release Gratis | BeritaPraktis')

@push('head')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
<style>
    .ql-container { font-family: var(--font-sans); font-size: 16px; min-height: 380px; background: #fff; border-bottom-left-radius: 6px; border-bottom-right-radius: 6px; }
    .ql-toolbar { background: #f9fafb; border-top-left-radius: 6px; border-top-right-radius: 6px; }
</style>
@endpush

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6">
    <div class="flex flex-wrap items-end justify-between gap-3 border-b border-gray-900 pb-4">
        <div>
            <h1 class="font-serif text-2xl font-bold text-gray-900 sm:text-3xl">Kirim Berita / Press Release Gratis</h1>
            <p class="mt-1 text-sm text-gray-600">
                Pastikan seluruh syarat wajib pada panel sebelah kanan berstatus <strong class="text-emerald-700">Hijau (Terpenuhi)</strong> sebelum mengirim.
            </p>
        </div>
        @if(!auth()->user()->isAdmin())
            <div class="rounded-md border {{ $monthlyUsed >= $monthlyLimit ? 'border-red-300 bg-red-50 text-red-800' : 'border-gray-200 bg-white text-gray-700' }} px-3.5 py-2 text-xs font-semibold">
                Kuota Bulan Ini: <span class="font-bold">{{ $monthlyUsed }} / {{ $monthlyLimit }}</span> artikel
            </div>
        @else
            <div class="rounded-md border border-emerald-200 bg-emerald-50 px-3.5 py-2 text-xs font-semibold text-emerald-800">
                Mode Admin Redaksi (Kuota Unlimited)
            </div>
        @endif
    </div>

    @if($errors->any())
        <div class="mt-6 rounded-md border border-red-300 bg-red-50 p-4 text-sm text-red-900">
            <strong class="font-bold">Gagal mengirim artikel. Periksa kesalahan berikut:</strong>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Live Pre-Submit Notification Banner (when user tries to submit invalid form) -->
    <div id="preSubmitAlert" class="hidden mt-6 rounded-md border border-red-300 bg-red-50 p-4 text-sm text-red-900">
        <strong class="font-bold">Artikel belum dapat dikirim! Lengkapi syarat wajib berikut:</strong>
        <ul id="preSubmitAlertList" class="mt-2 list-disc space-y-1 pl-5"></ul>
    </div>

    <form id="articleForm" action="{{ route('dashboard.articles.store') }}" method="POST" enctype="multipart/form-data" class="mt-6 grid grid-cols-1 gap-8 lg:grid-cols-12">
        @csrf

        <!-- Left Column: Editor Form -->
        <div class="space-y-6 lg:col-span-8">
            <div class="rounded-md border border-gray-200 bg-white p-6 space-y-5">
                <div>
                    <label for="title" class="block text-sm font-semibold text-gray-900">Judul Berita / Press Release <span class="text-red-700">*</span></label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" required minlength="15" maxlength="180"
                           placeholder="Contoh: Startup Agritech Lokal Luncurkan Inovasi Irigasi Cerdas untuk Petani Jawa Tengah"
                           class="mt-1.5 w-full rounded-md border border-gray-300 px-3.5 py-2.5 text-sm focus:border-gray-900 focus:outline-none">
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="category_id" class="block text-sm font-semibold text-gray-900">Kategori Berita <span class="text-red-700">*</span></label>
                        <select id="category_id" name="category_id" required class="mt-1.5 w-full rounded-md border border-gray-300 bg-white px-3.5 py-2.5 text-sm focus:border-gray-900 focus:outline-none">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="excerpt" class="block text-sm font-semibold text-gray-900">Ringkasan Singkat (Opsional)</label>
                        <input type="text" id="excerpt" name="excerpt" value="{{ old('excerpt') }}" maxlength="320"
                               placeholder="Otomatis diambil dari paragraf awal jika dikosongkan"
                               class="mt-1.5 w-full rounded-md border border-gray-300 px-3.5 py-2.5 text-sm focus:border-gray-900 focus:outline-none">
                    </div>
                </div>

                <!-- Featured Image Upload -->
                <div class="rounded-md border border-dashed border-gray-300 bg-gray-50 p-4">
                    <label for="featured_image" class="block text-sm font-semibold text-gray-900">Foto / Gambar Utama (Wajib, Maks 500 KB: PNG, JPEG, JPG, WebP, JFIF, SVG) <span class="text-red-700">*</span></label>
                    <input type="file" id="featured_image" name="featured_image" accept=".png,.jpeg,.jpg,.webp,.jfif,.svg,image/png,image/jpeg,image/webp,image/svg+xml" required
                           class="mt-2 block w-full text-sm text-gray-700 file:mr-4 file:rounded-md file:border-0 file:bg-gray-900 file:px-3.5 file:py-2 file:text-xs file:font-semibold file:text-white hover:file:bg-red-700">
                    <div id="imagePreviewBox" class="hidden mt-3">
                        <img id="imagePreview" src="#" alt="Preview Foto" class="max-h-56 rounded border border-gray-200 object-cover">
                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <label for="image_alt" class="block text-xs font-semibold text-gray-800">Alt Text Foto (Wajib untuk SEO) <span class="text-red-700">*</span></label>
                            <input type="text" id="image_alt" name="image_alt" value="{{ old('image_alt') }}" required maxlength="160"
                                   placeholder="Deskripsi singkat isi foto"
                                   class="mt-1 w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-xs focus:border-gray-900 focus:outline-none">
                        </div>
                        <div>
                            <label for="image_caption" class="block text-xs font-semibold text-gray-800">Keterangan / Sumber Foto (Opsional)</label>
                            <input type="text" id="image_caption" name="image_caption" value="{{ old('image_caption') }}" maxlength="220"
                                   placeholder="Contoh: Dokumentasi Tim / Ilustrasi"
                                   class="mt-1 w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-xs focus:border-gray-900 focus:outline-none">
                        </div>
                    </div>
                </div>

                <!-- Rich Text Content Editor -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-1.5">
                        Isi Berita / Press Release (Min. 900 Kata, {{ auth()->user()->isAdmin() ? 'Mode Admin: Link Bebas' : 'Maks. 2 Link' }}) <span class="text-red-700">*</span>
                    </label>
                    <div id="quillEditor">{!! old('content') !!}</div>
                    <textarea id="contentInput" name="content" class="hidden">{{ old('content') }}</textarea>
                </div>

                <!-- Agreement Checkbox -->
                <div class="rounded-md border border-gray-200 bg-gray-50 p-4 space-y-3">
                    <label class="flex items-start gap-2.5 text-sm text-gray-700 cursor-pointer">
                        <input type="checkbox" id="agree_rules" name="agree_rules" value="1" @checked(old('agree_rules')) required class="mt-0.5 h-4 w-4 rounded border-gray-300 text-red-700 focus:ring-red-700">
                        <span>
                            Saya menyatakan artikel ini tidak mengandung <strong>Judi Online, Game Online ilegal, Pornografi, maupun Hoaks</strong>, dan saya menyetujui
                            <a href="{{ route('rules') }}" target="_blank" class="font-semibold text-red-700 underline">Aturan Penulisan BeritaPraktis</a>.
                        </span>
                    </label>
                    @if(auth()->user()->isAdmin())
                        <label class="flex items-center gap-2.5 border-t border-gray-200 pt-3 text-sm font-semibold text-emerald-800 cursor-pointer">
                            <input type="checkbox" name="publish_now" value="1" checked class="h-4 w-4 rounded border-emerald-400 text-emerald-700 focus:ring-emerald-700">
                            <span>Terbitkan Langsung (Mode Admin, tanpa antrean review)</span>
                        </label>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Sticky Live Pre-Submit Validator -->
        <aside class="lg:col-span-4">
            <div class="sticky top-28 rounded-md border border-gray-200 bg-white p-5 space-y-4">
                <div class="border-b border-gray-200 pb-3">
                    <h2 class="font-serif text-lg font-bold text-gray-900">Pemeriksaan Wajib</h2>
                    <p class="text-xs text-gray-500">Terdeteksi otomatis secara real-time sebelum kirim.</p>
                </div>

                <!-- 1. Word Count Check -->
                <div id="wordCheckCard" class="rounded-md border border-red-200 bg-red-50 p-3.5">
                    <div class="flex items-center justify-between text-xs font-bold">
                        <span id="wordStatusTitle" class="text-red-800">1. Minimum 900 Kata</span>
                        <span id="wordCounterBadge" class="text-red-800">0 / 900</span>
                    </div>
                    <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-red-200">
                        <div id="wordProgressBar" class="h-full bg-red-600 transition-all duration-200" style="width: 0%"></div>
                    </div>
                    <p id="wordStatusDesc" class="mt-1.5 text-xs text-red-700">Kurang 900 kata lagi.</p>
                </div>

                <!-- 2. Outbound Link Check -->
                <div id="linkCheckCard" class="rounded-md border border-emerald-200 bg-emerald-50 p-3.5">
                    <div class="flex items-center justify-between text-xs font-bold">
                        <span id="linkStatusTitle" class="text-emerald-800">2. Maksimal 2 Link</span>
                        <span id="linkCounterBadge" class="text-emerald-800">0 / 2</span>
                    </div>
                    <p id="linkStatusDesc" class="mt-1 text-xs text-emerald-700">Jumlah tautan aman (0 dari maksimal 2 link).</p>
                </div>

                <!-- 3. Featured Image Check -->
                <div id="imageCheckCard" class="rounded-md border border-red-200 bg-red-50 p-3.5">
                    <div class="flex items-center justify-between text-xs font-bold">
                        <span id="imageStatusTitle" class="text-red-800">3. Wajib Foto Utama</span>
                        <span id="imageStatusBadge" class="text-red-800">Belum Ada</span>
                    </div>
                    <p id="imageStatusDesc" class="mt-1 text-xs text-red-700">Unggah 1 foto utama (JPG/PNG/WebP).</p>
                </div>

                <!-- 4. Banned Keywords Check -->
                <div id="safetyCheckCard" class="rounded-md border border-emerald-200 bg-emerald-50 p-3.5">
                    <div class="flex items-center justify-between text-xs font-bold">
                        <span id="safetyStatusTitle" class="text-emerald-800">4. Filter Konten Terlarang</span>
                        <span id="safetyStatusBadge" class="text-emerald-800">Bersih</span>
                    </div>
                    <p id="safetyStatusDesc" class="mt-1 text-xs text-emerald-700">Bebas kata kunci judi online, game online, &amp; pornografi.</p>
                </div>

                <button type="submit" id="submitBtn"
                        class="w-full rounded-md bg-red-700 px-4 py-3 text-sm font-bold text-white hover:bg-red-800 transition cursor-pointer">
                    Kirim Artikel untuk Review
                </button>
                <p class="text-[11px] text-center text-gray-500">
                    Notifikasi langsung dikirim ke Telegram Admin Redaksi.
                </p>
            </div>
        </aside>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const MIN_WORDS = 900;
    const MAX_LINKS = 2;
    const IS_ADMIN = @json(auth()->user()->isAdmin());
    const BANNED_KEYWORDS = @json($bannedKeywords);

    const quill = new Quill('#quillEditor', {
        theme: 'snow',
        placeholder: 'Tulis berita atau press release lengkap di sini (minimal 900 kata)...',
        modules: {
            toolbar: [
                [{ header: [2, 3, false] }],
                ['bold', 'italic', 'underline', 'blockquote'],
                [{ list: 'ordered' }, { list: 'bullet' }],
                ['link', 'clean']
            ]
        }
    });

    const titleInput = document.getElementById('title');
    const contentInput = document.getElementById('contentInput');
    const imageInput = document.getElementById('featured_image');
    const imagePreviewBox = document.getElementById('imagePreviewBox');
    const imagePreview = document.getElementById('imagePreview');
    const agreeCheckbox = document.getElementById('agree_rules');
    const form = document.getElementById('articleForm');
    const preSubmitAlert = document.getElementById('preSubmitAlert');
    const preSubmitAlertList = document.getElementById('preSubmitAlertList');

    let state = {
        wordCount: 0,
        linkCount: 0,
        hasImage: false,
        bannedFound: [],
    };

    function countWords(text) {
        const clean = text.replace(/\s+/g, ' ').trim();
        if (!clean) return 0;
        const matches = clean.match(/[\p{L}\p{N}]+(?:['-][\p{L}\p{N}]+)*/gu);
        return matches ? matches.length : 0;
    }

    function extractLinks(html, plainText) {
        const urls = new Set();
        const doc = new DOMParser().parseFromString(html, 'text/html');
        doc.querySelectorAll('a[href]').forEach(a => {
            const href = (a.getAttribute('href') || '').trim();
            if (href && !href.startsWith('#') && !href.startsWith('/') && !href.startsWith('mailto:')) {
                urls.add(href);
            }
        });

        // Also detect raw URLs typed directly in text
        const textWithoutAnchors = Array.from(doc.body.childNodes)
            .map(n => n.nodeName === 'A' ? '' : n.textContent)
            .join(' ');
        const rawMatches = textWithoutAnchors.match(/\b(?:https?:\/\/|www\.)[^\s<>"'()]+/gi) || [];
        rawMatches.forEach(u => urls.add(u));

        return Array.from(urls);
    }

    function checkBannedKeywords(title, plainText, links) {
        const haystack = `${title} ${plainText} ${links.join(' ')}`.toLowerCase();
        const collapsed = haystack.replace(/[^\p{L}\p{N}\s]/gu, '');
        const found = [];

        for (const item of BANNED_KEYWORDS) {
            const kw = item.keyword.toLowerCase().trim();
            if (!kw) continue;
            const escaped = kw.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            const regex = new RegExp(`\\b${escaped}\\b`, 'iu');
            if (regex.test(haystack) || regex.test(collapsed)) {
                found.push(item.keyword);
            }
        }
        return [...new Set(found)];
    }

    function setCardState(cardId, titleId, badgeId, descId, isOk, titleText, badgeText, descText) {
        const card = document.getElementById(cardId);
        const titleEl = document.getElementById(titleId);
        const badgeEl = document.getElementById(badgeId);
        const descEl = document.getElementById(descId);

        card.className = isOk
            ? 'rounded-md border border-emerald-200 bg-emerald-50 p-3.5'
            : 'rounded-md border border-red-200 bg-red-50 p-3.5';

        titleEl.className = isOk ? 'text-emerald-800' : 'text-red-800';
        badgeEl.className = isOk ? 'text-emerald-800' : 'text-red-800';
        descEl.className = isOk ? 'mt-1.5 text-xs text-emerald-700' : 'mt-1.5 text-xs text-red-700';

        titleEl.textContent = titleText;
        badgeEl.textContent = badgeText;
        descEl.textContent = descText;
    }

    function validateAll() {
        const html = quill.root.innerHTML;
        const plainText = quill.getText();
        contentInput.value = html === '<p><br></p>' ? '' : html;

        // 1. Words
        state.wordCount = countWords(plainText);
        const wordOk = state.wordCount >= MIN_WORDS;
        const pct = Math.min(100, Math.round((state.wordCount / MIN_WORDS) * 100));
        const bar = document.getElementById('wordProgressBar');
        bar.style.width = pct + '%';
        bar.className = wordOk ? 'h-full bg-emerald-600 transition-all duration-200' : 'h-full bg-red-600 transition-all duration-200';

        setCardState(
            'wordCheckCard', 'wordStatusTitle', 'wordCounterBadge', 'wordStatusDesc',
            wordOk,
            '1. Minimum 900 Kata',
            `${state.wordCount} / ${MIN_WORDS}`,
            wordOk ? `Memenuhi syarat (${state.wordCount} kata).` : `Kurang ${MIN_WORDS - state.wordCount} kata lagi.`
        );

        // 2. Links
        const links = extractLinks(html, plainText);
        state.linkCount = links.length;
        const linkOk = IS_ADMIN || state.linkCount <= MAX_LINKS;
        setCardState(
            'linkCheckCard', 'linkStatusTitle', 'linkCounterBadge', 'linkStatusDesc',
            linkOk,
            IS_ADMIN ? '2. Link Keluar (Admin: Bebas)' : '2. Maksimal 2 Link',
            IS_ADMIN ? `${state.linkCount} / Bebas` : `${state.linkCount} / ${MAX_LINKS}`,
            IS_ADMIN
                ? `Mode Admin aktif: terdeteksi ${state.linkCount} link keluar (bebas tanpa batas).`
                : (linkOk
                    ? `Jumlah tautan aman (${state.linkCount} dari maks ${MAX_LINKS} link).`
                    : `Terlalu banyak link! Hapus ${state.linkCount - MAX_LINKS} link lagi.`)
        );

        // 3. Image (Format & Max 500 KB)
        const allowedExtensions = ['png', 'jpeg', 'jpg', 'webp', 'jfif', 'svg'];
        const maxBytes = 500 * 1024;
        const selectedFile = imageInput.files && imageInput.files[0] ? imageInput.files[0] : null;
        let imageError = '';

        if (!selectedFile) {
            state.hasImage = false;
            imageError = 'Unggah 1 foto utama (Maks 500 KB: PNG, JPEG, JPG, WebP, JFIF, SVG).';
        } else {
            const ext = (selectedFile.name.split('.').pop() || '').toLowerCase();
            if (!allowedExtensions.includes(ext)) {
                state.hasImage = false;
                imageError = `Format .${ext || '?'} ditolak! Hanya boleh PNG, JPEG, JPG, WebP, JFIF, atau SVG.`;
            } else if (selectedFile.size > maxBytes) {
                state.hasImage = false;
                const kb = Math.round(selectedFile.size / 1024);
                imageError = `Ukuran file terlalu besar (${kb} KB). Maksimal 500 KB.`;
            } else {
                state.hasImage = true;
            }
        }
        state.imageError = imageError;

        setCardState(
            'imageCheckCard', 'imageStatusTitle', 'imageStatusBadge', 'imageStatusDesc',
            state.hasImage,
            '3. Wajib Foto Utama (Maks 500 KB)',
            state.hasImage ? 'Siap' : (selectedFile ? 'Ditolak' : 'Belum Ada'),
            state.hasImage
                ? `File: ${selectedFile.name} (${Math.round(selectedFile.size / 1024)} KB)`
                : imageError
        );

        // 4. Banned Keywords
        state.bannedFound = checkBannedKeywords(titleInput.value || '', plainText, links);
        const safetyOk = state.bannedFound.length === 0;
        setCardState(
            'safetyCheckCard', 'safetyStatusTitle', 'safetyStatusBadge', 'safetyStatusDesc',
            safetyOk,
            '4. Filter Konten Terlarang',
            safetyOk ? 'Bersih' : 'Ditolak!',
            safetyOk
                ? 'Bebas kata kunci judi online, game online, & pornografi.'
                : `Terdeteksi kata terlarang: ${state.bannedFound.slice(0, 3).join(', ')}.`
        );
    }

    quill.on('text-change', validateAll);
    titleInput.addEventListener('input', validateAll);
    imageInput.addEventListener('change', () => {
        if (imageInput.files && imageInput.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                imagePreview.src = e.target.result;
                imagePreviewBox.classList.remove('hidden');
            };
            reader.readAsDataURL(imageInput.files[0]);
        } else {
            imagePreviewBox.classList.add('hidden');
        }
        validateAll();
    });

    form.addEventListener('submit', (e) => {
        validateAll();
        const problems = [];

        if (state.wordCount < MIN_WORDS) {
            problems.push(`Jumlah kata baru ${state.wordCount} kata (wajib minimal ${MIN_WORDS} kata).`);
        }
        if (!IS_ADMIN && state.linkCount > MAX_LINKS) {
            problems.push(`Terdeteksi ${state.linkCount} link keluar (maksimal hanya ${MAX_LINKS} link).`);
        }
        if (!state.hasImage) {
            problems.push(state.imageError || 'Foto utama artikel belum valid atau melebihi 500 KB.');
        }
        if (state.bannedFound.length > 0) {
            problems.push(`Konten terindikasi melanggar kebijakan (Judi Online/Game Online/Pornografi): ${state.bannedFound.join(', ')}.`);
        }
        if (!agreeCheckbox.checked) {
            problems.push('Centang persetujuan Aturan Penulisan BeritaPraktis.');
        }

        if (problems.length > 0) {
            e.preventDefault();
            preSubmitAlertList.innerHTML = problems.map(p => `<li>${p}</li>`).join('');
            preSubmitAlert.classList.remove('hidden');
            preSubmitAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

    validateAll();
});
</script>
@endpush
