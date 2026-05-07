@extends('layouts.app')
@section('title', 'Semua Forum')

@section('content')
<div style="max-width:56rem;margin:0 auto;">

    {{-- Header --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1.5rem;margin-bottom:1.75rem;flex-wrap:wrap;">
        <div>
            <h1 style="font-size:1.25rem;font-weight:700;color:var(--tx-1);margin-bottom:.25rem;">Semua Forum</h1>
            <p style="font-size:.8125rem;color:var(--tx-4);">
                {{ $forums->total() }} forum tersedia
            </p>
        </div>

        {{-- Search --}}
        <form method="GET" action="{{ route('forums.all') }}"
            style="display:flex;gap:.5rem;align-items:center;">
            <div style="position:relative;">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                    style="position:absolute;left:.75rem;top:50%;transform:translateY(-50%);color:var(--tx-4);pointer-events:none;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                </svg>
                <input type="text" name="q" value="{{ $search }}"
                    placeholder="Cari forum..."
                    style="padding:.5rem .875rem .5rem 2.25rem;font-size:.8125rem;width:16rem;border-radius:var(--r-full);"
                    autocomplete="off">
            </div>
            @if($search)
            <a href="{{ route('forums.all') }}"
                style="font-size:.75rem;color:var(--tx-4);transition:color 150ms;white-space:nowrap;"
                onmouseover="this.style.color='var(--ac-light)'" onmouseout="this.style.color='var(--tx-4)'">
                Hapus filter
            </a>
            @endif
        </form>
    </div>

    {{-- Grid forum --}}
    @if($forums->count())
    <div class="forums-all-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(16rem,1fr));gap:.75rem;">
        @foreach($forums as $forum)
        <a href="{{ route('forum.show', $forum->slug) }}"
            style="display:flex;flex-direction:column;padding:1rem 1.125rem;background:var(--bg-1);border:1px solid var(--bd-1);border-radius:var(--r-md);text-decoration:none;transition:border-color 150ms,background 150ms,transform 120ms;"
            onmouseover="this.style.borderColor='rgba(139,92,246,.4)';this.style.background='var(--bg-2)';this.style.transform='translateY(-1px)'"
            onmouseout="this.style.borderColor='var(--bd-1)';this.style.background='var(--bg-1)';this.style.transform='translateY(0)'">

            {{-- Icon + name --}}
            <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:.5rem;">
                <span style="font-size:1.5rem;line-height:1;flex-shrink:0;">{{ $forum->icon ?? '💬' }}</span>
                <span style="font-size:.875rem;font-weight:600;color:var(--tx-1);line-height:1.3;">{{ $forum->name }}</span>
            </div>

            {{-- Description --}}
            @if($forum->description)
            <p style="font-size:.75rem;color:var(--tx-4);line-height:1.55;flex:1;margin-bottom:.625rem;
                display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                {{ $forum->description }}
            </p>
            @else
            <div style="flex:1;"></div>
            @endif

            {{-- Stats --}}
            <div style="display:flex;align-items:center;gap:.875rem;margin-top:auto;padding-top:.5rem;border-top:1px solid var(--bd-1);">
                <span style="display:flex;align-items:center;gap:.3rem;font-size:.6875rem;color:var(--tx-4);">
                    <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    {{ number_format($forum->subforums_count) }} subforum
                </span>
                <span style="display:flex;align-items:center;gap:.3rem;font-size:.6875rem;color:var(--tx-4);">
                    <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    {{ number_format($forum->posts_count) }} post
                </span>
            </div>
        </a>
        @endforeach
    </div>

    {{-- Pagination --}}
    {{ $forums->links('partials.pagination') }}

    @else
    <div class="empty-state" style="padding:4rem 1rem;">
        @if($search)
            <p style="font-size:2rem;margin-bottom:.75rem;">🔍</p>
            <p>Tidak ada forum yang cocok dengan "<strong style="color:var(--tx-2)">{{ $search }}</strong>"</p>
            <a href="{{ route('forums.all') }}" style="color:var(--ac-1);margin-top:.75rem;display:inline-block;">
                Lihat semua forum
            </a>
        @else
            <p>Belum ada forum niche.</p>
        @endif
    </div>
    @endif

</div>
@endsection
