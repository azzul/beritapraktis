<div class="mb-6 flex flex-wrap items-center justify-between gap-3 rounded-md border border-gray-200 bg-white px-4 py-3">
    <div class="flex flex-wrap items-center gap-2 text-xs font-semibold">
        <a href="{{ route('admin.dashboard') }}"
           class="rounded px-3 py-2 transition {{ request()->routeIs('admin.dashboard') ? 'bg-gray-900 text-white' : 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50' }}">
            Dashboard
        </a>
        <a href="{{ route('admin.articles.index') }}"
           class="rounded px-3 py-2 transition {{ request()->routeIs('admin.articles.*') ? 'bg-gray-900 text-white' : 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50' }}">
            Moderasi Artikel
        </a>
        <a href="{{ route('admin.sadur.index') }}"
           class="rounded px-3 py-2 transition {{ request()->routeIs('admin.sadur.*') ? 'bg-gray-900 text-white' : 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50' }}">
            Auto-Sadur RSS
        </a>
        <a href="{{ route('admin.users.index') }}"
           class="rounded px-3 py-2 transition {{ request()->routeIs('admin.users.*') ? 'bg-gray-900 text-white' : 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50' }}">
            Manage User
        </a>
        <a href="{{ route('admin.seo.index') }}"
           class="rounded px-3 py-2 transition {{ request()->routeIs('admin.seo.*') ? 'bg-gray-900 text-white' : 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50' }}">
            SEO &amp; Sitemap
        </a>
        <a href="{{ route('admin.categories.index') }}"
           class="rounded px-3 py-2 transition {{ request()->routeIs('admin.categories.*') ? 'bg-gray-900 text-white' : 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50' }}">
            Kategori
        </a>
        <a href="{{ route('admin.keywords.index') }}"
           class="rounded px-3 py-2 transition {{ request()->routeIs('admin.keywords.*') ? 'bg-gray-900 text-white' : 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50' }}">
            Kata Terlarang
        </a>
    </div>
    <a href="{{ route('dashboard.articles.create') }}"
       class="rounded bg-red-700 px-3.5 py-2 text-xs font-bold text-white hover:bg-red-800 transition">
        + Tulis Artikel
    </a>
</div>
