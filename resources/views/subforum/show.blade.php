@extends('layouts.app')
@section('title', $subforum->name)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="lg:col-span-2 space-y-4">

        {{-- Breadcrumb --}}
        <nav class="breadcrumb">
            <a href="{{ route('forum.show', $forum->slug) }}">{{ $forum->name }}</a>
            <span class="breadcrumb-sep">/</span>
            <span style="color:var(--tx-2)">{{ $subforum->name }}</span>
        </nav>

        {{-- Header --}}
        <div>
            <h1 style="font-size:1.125rem;font-weight:700;color:var(--tx-1);">{{ $subforum->name }}</h1>
            @if($subforum->description)
            <p style="font-size:.8125rem;color:var(--tx-3);margin-top:.25rem;line-height:1.6;">{{ $subforum->description }}</p>
            @endif
            <p style="font-size:.75rem;color:var(--tx-4);margin-top:.375rem;">
                Dibuat oleh
                <a href="{{ route('profile.show', $subforum->creator->username) }}"
                    style="color:var(--ac-1);transition:color 150ms;"
                    onmouseover="this.style.color='var(--ac-light)'"
                    onmouseout="this.style.color='var(--ac-1)'">{{ $subforum->creator->username }}</a>
                &middot; {{ $subforum->created_at->diffForHumans() }}
            </p>
        </div>

        {{-- Compose --}}
        <div class="compose-box">
            <p class="compose-label">Posting di <span style="color:var(--ac-light)">{{ $subforum->name }}</span></p>
            <form action="{{ route('post.store') }}" method="POST">
                @csrf
                <input type="hidden" name="forum_id" value="{{ $forum->id }}">
                <input type="hidden" name="subforum_id" value="{{ $subforum->id }}">
                <textarea name="content" rows="3"
                    placeholder="Tulis di {{ $subforum->name }}..."
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
            <div class="empty-state">Belum ada post di subforum ini.</div>
            @endforelse
        </div>

        {{ $posts->links('partials.pagination') }}

    </div>

    {{-- Sidebar --}}
    <aside class="space-y-3 sidebar-mobile-hidden">
        <div class="widget">
            <p class="widget-label">Subforum</p>
            <p style="font-size:.875rem;font-weight:600;color:var(--tx-1);margin-bottom:.25rem;">{{ $subforum->name }}</p>
            @if($subforum->description)
            <p style="font-size:.8125rem;color:var(--tx-3);line-height:1.55;">{{ $subforum->description }}</p>
            @endif
        </div>
        <div class="widget">
            <p class="widget-label">Info</p>
            <div style="font-size:.8125rem;color:var(--tx-3);display:flex;flex-direction:column;gap:.375rem;">
                <span>📋 {{ number_format($subforum->post_count) }} post</span>
                <span>🕐 {{ $subforum->created_at->diffForHumans() }}</span>
            </div>
        </div>
        <a href="{{ route('forum.show', $forum->slug) }}" style="display:flex;align-items:center;gap:.375rem;font-size:.75rem;color:var(--tx-4);padding:.25rem 0;transition:color 150ms;" onmouseover="this.style.color='var(--ac-light)'" onmouseout="this.style.color='var(--tx-4)'">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            {{ $forum->name }}
        </a>
    </aside>

</div>
@endsection
