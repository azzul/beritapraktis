@extends('layouts.app')

@section('title', 'Masuk Kontributor dengan Google | BeritaPraktis')

@section('content')
<div class="mx-auto flex max-w-md flex-col items-center px-4 py-16 sm:px-6">
    <div class="w-full rounded-md border border-gray-200 bg-white p-8 text-center">
        <img src="{{ asset('images/logo.svg') }}" alt="BeritaPraktis" class="mx-auto h-9 w-auto">
        <h1 class="mt-5 font-serif text-2xl font-bold text-gray-900">Masuk Kontributor</h1>
        <p class="mt-2 text-sm leading-relaxed text-gray-600">
            Gunakan akun Google Anda untuk menulis dan mengirimkan berita atau press release gratis di BeritaPraktis.
        </p>

        <a href="{{ route('auth.google') }}"
           class="mt-6 flex w-full items-center justify-center gap-3 rounded-md border border-gray-300 bg-white px-4 py-3 text-sm font-semibold text-gray-800 hover:bg-gray-50 transition">
            <svg class="h-5 w-5" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.17Z"/>
                <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.11-6.72-4.96H1.29v3.14C3.26 21.3 7.31 24 12 24Z"/>
                <path fill="#FBBC05" d="M5.28 14.24c-.24-.72-.38-1.49-.38-2.24s.14-1.52.38-2.24V6.62H1.29C.47 8.24 0 10.06 0 12s.47 3.76 1.29 5.38l3.99-3.14Z"/>
                <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.31 0 3.26 2.7 1.29 6.62l3.99 3.14c.95-2.85 3.6-4.96 6.72-4.96Z"/>
            </svg>
            Lanjutkan dengan Google
        </a>

        <p class="mt-5 text-xs text-gray-500">
            Dengan masuk, Anda menyetujui <a href="{{ route('rules') }}" class="text-red-700 underline">Aturan Penulisan BeritaPraktis</a>.
        </p>
    </div>
</div>
@endsection
