@extends('layouts.app')

@section('title', 'SEO, Sitemap & llms.txt — Admin | BeritaPraktis')

@section('content')
<div class="mx-auto max-w-5xl px-4 py-8 sm:px-6">
    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-900 pb-4">
        <div>
            <h1 class="font-serif text-2xl font-bold text-gray-900">Generate &amp; Status SEO Berita</h1>
            <p class="mt-1 text-xs text-gray-600">
                Seluruh Sitemap &amp; llms.txt otomatis terupdate setiap kali ada berita baru yang di-approve (via Web maupun Telegram).
            </p>
        </div>
        <div class="flex items-center gap-3">
            <form action="{{ route('admin.seo.regenerate') }}" method="POST">
                @csrf
                <button type="submit" class="rounded-md bg-red-700 px-4 py-2 text-xs font-bold text-white hover:bg-red-800 cursor-pointer">
                    🔄 Regenerate / Refresh Cache SEO Sekarang
                </button>
            </form>
            <a href="{{ route('admin.dashboard') }}" class="text-xs font-semibold text-gray-600 hover:text-red-700">&larr; Dashboard</a>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
        @foreach($endpoints as $ep)
            <div class="rounded-md border border-gray-200 bg-white p-4 flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-sm font-bold text-gray-900">{{ $ep['name'] }}</h2>
                    <p class="mt-0.5 text-xs text-gray-600">{{ $ep['desc'] }}</p>
                    <code class="mt-1.5 block text-[11px] text-gray-500 break-all">{{ $ep['url'] }}</code>
                </div>
                <a href="{{ $ep['url'] }}" target="_blank" class="shrink-0 rounded border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-800 hover:bg-gray-50">
                    Buka &rarr;
                </a>
            </div>
        @endforeach
    </div>
</div>
@endsection
