@extends('layouts.app')

@section('title', "Review: {$article->title} | Admin BeritaPraktis")

@section('content')
<div class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
    <div class="mb-4">
        <a href="{{ route('admin.dashboard') }}" class="text-xs font-semibold text-gray-600 hover:text-red-700">&larr; Kembali ke Dashboard Redaksi</a>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
        <!-- Article Preview -->
        <div class="lg:col-span-8 rounded-md border border-gray-200 bg-white p-6">
            <div class="flex items-center gap-2 text-xs font-bold uppercase text-red-700">
                <span>{{ $article->category->name }}</span>
                <span>&bull;</span>
                <span>Status: {{ $article->status }}</span>
            </div>
            <h1 class="mt-2 font-serif text-3xl font-bold text-gray-900">{{ $article->title }}</h1>
            <p class="mt-2 text-xs text-gray-500">
                Penulis: <strong>{{ $article->user->name }}</strong> ({{ $article->user->email }}) &bull;
                {{ $article->word_count }} kata &bull; {{ $article->outbound_links_count }} outbound links
            </p>

            <div class="mt-5">
                <img src="{{ $article->image_url }}" alt="{{ $article->image_alt }}" class="max-h-96 w-full rounded border border-gray-200 object-cover">
                <p class="mt-1.5 text-xs text-gray-500">Alt: {{ $article->image_alt }}</p>
            </div>

            <div class="prose-editorial mt-6 border-t border-gray-100 pt-6">
                {!! $article->content !!}
            </div>
        </div>

        <!-- Moderation Actions -->
        <aside class="lg:col-span-4 space-y-5">
            <div class="rounded-md border border-gray-200 bg-white p-5 space-y-4">
                <h2 class="font-serif text-lg font-bold text-gray-900">Keputusan Redaksi</h2>

                @if($article->status !== 'published')
                    <form action="{{ route('admin.articles.approve', $article) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full rounded-md bg-emerald-700 px-4 py-2.5 text-sm font-bold text-white hover:bg-emerald-800 cursor-pointer">
                            ✅ Approve &amp; Publish Sekarang
                        </button>
                    </form>
                @else
                    <a href="{{ route('articles.show', $article->slug) }}" target="_blank" class="block w-full rounded-md bg-gray-900 px-4 py-2.5 text-center text-sm font-bold text-white hover:bg-gray-800">
                        Lihat di Halaman Publik &rarr;
                    </a>
                @endif

                <form action="{{ route('admin.articles.reject', $article) }}" method="POST" class="space-y-2 border-t border-gray-100 pt-4">
                    @csrf
                    <label class="block text-xs font-semibold text-gray-700">Tolak Artikel (Wajib isi alasan):</label>
                    <textarea name="rejection_reason" rows="3" required placeholder="Misal: Mengandung promosi terselubung / link tidak relevan..."
                              class="w-full rounded-md border border-gray-300 p-2.5 text-xs focus:border-red-700 focus:outline-none">{{ old('rejection_reason', $article->rejection_reason) }}</textarea>
                    <button type="submit" class="w-full rounded-md bg-red-700 px-4 py-2 text-xs font-bold text-white hover:bg-red-800 cursor-pointer">
                        ❌ Reject Artikel
                    </button>
                </form>
            </div>

            <!-- Outbound Links Audit -->
            <div class="rounded-md border border-gray-200 bg-white p-5">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-900">Audit Outbound Links ({{ $article->outbound_links_count }}/2)</h3>
                @if(!empty($article->outbound_links))
                    <ul class="mt-2.5 space-y-1.5 text-xs">
                        @foreach($article->outbound_links as $link)
                            <li class="break-all">
                                <a href="{{ $link }}" target="_blank" rel="noopener noreferrer nofollow" class="text-red-700 underline">{{ $link }}</a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="mt-2 text-xs text-gray-500">Tidak ada link keluar.</p>
                @endif
            </div>

            <!-- Ban Author -->
            @if(!$article->user->isAdmin())
                <div class="rounded-md border border-gray-200 bg-white p-5">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-900">Status Akun Penulis</h3>
                    <p class="mt-1 text-xs text-gray-600">{{ $article->user->email }}</p>
                    <form action="{{ route('admin.users.toggle-ban', $article->user) }}" method="POST" class="mt-3">
                        @csrf
                        <button type="submit" class="w-full rounded border {{ $article->user->is_banned ? 'border-emerald-300 bg-emerald-50 text-emerald-800' : 'border-red-300 bg-red-50 text-red-800' }} px-3 py-2 text-xs font-semibold cursor-pointer">
                            {{ $article->user->is_banned ? 'Cabut Blokir Penulis' : 'Blokir Permanen Penulis Ini' }}
                        </button>
                    </form>
                </div>
            @endif
        </aside>
    </div>
</div>
@endsection
