@extends('layouts.app')
@section('title', $forum->name)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="lg:col-span-2 space-y-4">

        {{-- Header --}}
        <div style="display:flex;align-items:center;gap:.875rem;">
            <span style="font-size:1.75rem;line-height:1;">{{ $forum->icon ?? '🌐' }}</span>
            <div>
                <h1 style="font-size:1.125rem;font-weight:700;color:var(--tx-1);">{{ $forum->name }}</h1>
                @if($forum->description)
                <p style="font-size:.8125rem;color:var(--tx-4);margin-top:.125rem;">{{ $forum->description }}</p>
                @endif
            </div>
        </div>

        {{-- Compose --}}
        <div class="compose-box">
            <p class="compose-label">Tulis di <span style="color:var(--ac-light)">{{ $forum->name }}</span></p>
            <form action="{{ route('post.store') }}" method="POST">
                @csrf
                <input type="hidden" name="forum_id" value="{{ $forum->id }}">
                <textarea name="content" rows="3"
                    placeholder="Tulis sesuatu di {{ $forum->name }}..."
                    required maxlength="5000"></textarea>
                <div class="compose-footer">
                    <button type="submit" class="btn-primary text-sm" style="padding:.5rem 1.25rem;">Kutip</button>
                </div>
            </form>
        </div>

        {{-- Posts --}}
        <div class="space-y-2">
            @forelse($posts as $post)
                @include('partials.post-card', ['post' => $post, 'user' => $user])
            @empty
            <div class="empty-state">Belum ada post di sini.</div>
            @endforelse
        </div>

        {{ $posts->links('partials.pagination') }}

    </div>

    {{-- Sidebar --}}
    <aside class="space-y-3 sidebar-mobile-hidden">
        <div class="widget">
            <p class="widget-label">Tentang</p>
            <p style="font-size:.8125rem;color:var(--tx-3);line-height:1.6;">
                {{ $forum->description ?? 'Forum global — semua orang bisa langsung posting tanpa subforum.' }}
            </p>
        </div>
        <a href="{{ route('home') }}" style="display:flex;align-items:center;gap:.375rem;font-size:.75rem;color:var(--tx-4);padding:.25rem 0;transition:color 150ms;" onmouseover="this.style.color='var(--ac-light)'" onmouseout="this.style.color='var(--tx-4)'">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Beranda
        </a>
    </aside>

</div>
@endsection
