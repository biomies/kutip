@extends('layouts.app')
@section('title', $forum->name)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="lg:col-span-2 space-y-4">

        {{-- Header --}}
        <div style="display:flex;align-items:center;justify-content:space-between;gap:1rem;">
            <div style="display:flex;align-items:center;gap:.875rem;">
                <span style="font-size:1.75rem;line-height:1;">{{ $forum->icon ?? '💬' }}</span>
                <div>
                    <h1 style="font-size:1.125rem;font-weight:700;color:var(--tx-1);">{{ $forum->name }}</h1>
                    @if($forum->description)
                    <p style="font-size:.8125rem;color:var(--tx-4);margin-top:.125rem;">{{ $forum->description }}</p>
                    @endif
                </div>
            </div>
            <a href="{{ route('subforum.create', $forum->slug) }}" class="btn-primary text-sm shrink-0" style="padding:.5rem 1rem;">
                + Buat Subforum
            </a>
        </div>

        {{-- Subforum list --}}
        <div class="space-y-2">
            @forelse($subforums as $subforum)
            <a href="{{ route('subforum.show', [$forum->slug, $subforum->slug]) }}" class="subforum-item">
                <div class="subforum-item-name">{{ $subforum->name }}</div>
                @if($subforum->description)
                <div class="subforum-item-meta" style="margin-top:.2rem;">{{ Str::limit($subforum->description, 80) }}</div>
                @endif
                <div class="subforum-item-meta">
                    dibuat oleh <span style="color:var(--ac-1)">{{ $subforum->creator->username }}</span>
                    &middot; {{ $subforum->created_at->diffForHumans() }}
                </div>
            </a>
            @empty
            <div class="empty-state" style="border:1px dashed var(--bd-2);border-radius:var(--r-md);">
                Belum ada subforum.
                <a href="{{ route('subforum.create', $forum->slug) }}">Buat yang pertama →</a>
            </div>
            @endforelse
        </div>

        {{ $subforums->links('partials.pagination') }}

    </div>

    {{-- Sidebar --}}
    <aside class="space-y-3 sidebar-mobile-hidden">
        <div class="widget">
            <p class="widget-label">Tentang</p>
            <p style="font-size:.8125rem;color:var(--tx-3);line-height:1.6;">
                {{ $forum->description ?? 'Forum niche — buat subforum untuk mulai berdiskusi.' }}
            </p>
        </div>
        <a href="{{ route('home') }}" style="display:flex;align-items:center;gap:.375rem;font-size:.75rem;color:var(--tx-4);padding:.25rem 0;transition:color 150ms;" onmouseover="this.style.color='var(--ac-light)'" onmouseout="this.style.color='var(--tx-4)'">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Beranda
        </a>
    </aside>

</div>
@endsection
