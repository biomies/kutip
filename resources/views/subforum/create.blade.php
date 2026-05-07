@extends('layouts.app')
@section('title', 'Buat Subforum')

@section('content')
<div style="max-width:36rem;margin:0 auto;">

    <nav class="breadcrumb" style="margin-bottom:1.5rem;">
        <a href="{{ route('forum.show', $forum->slug) }}">{{ $forum->name }}</a>
        <span class="breadcrumb-sep">/</span>
        <span style="color:var(--tx-2)">Buat Subforum</span>
    </nav>

    <h1 style="font-size:1.125rem;font-weight:700;color:var(--tx-1);margin-bottom:1.25rem;">Buat Subforum Baru</h1>

    <div class="widget" style="padding:1.5rem;">
        <form action="{{ route('subforum.store', $forum->slug) }}" method="POST" style="display:flex;flex-direction:column;gap:1rem;">
            @csrf

            <div>
                <label for="name" style="display:block;font-size:.75rem;font-weight:600;color:var(--tx-3);margin-bottom:.375rem;">Nama Subforum</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}"
                    placeholder="Contoh: Teknologi AI, Film Indie..."
                    style="width:100%;padding:.625rem .875rem;font-size:.875rem;"
                    required minlength="3" maxlength="100">
                @error('name')
                <p style="font-size:.75rem;color:var(--danger);margin-top:.375rem;">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" style="display:block;font-size:.75rem;font-weight:600;color:var(--tx-3);margin-bottom:.375rem;">
                    Deskripsi <span style="color:var(--tx-4);font-weight:400;">(opsional)</span>
                </label>
                <textarea id="description" name="description" rows="3"
                    placeholder="Tentang apa subforum ini?"
                    style="width:100%;padding:.625rem .875rem;font-size:.875rem;resize:none;"
                    maxlength="500">{{ old('description') }}</textarea>
                @error('description')
                <p style="font-size:.75rem;color:var(--danger);margin-top:.375rem;">{{ $message }}</p>
                @enderror
            </div>

            <div style="display:flex;gap:.75rem;padding-top:.5rem;">
                <button type="submit" class="btn-primary text-sm" style="padding:.625rem 1.5rem;">Buat Subforum</button>
                <a href="{{ route('forum.show', $forum->slug) }}" class="btn-ghost text-sm" style="padding:.625rem 1.5rem;">Batal</a>
            </div>
        </form>
    </div>

</div>
@endsection
