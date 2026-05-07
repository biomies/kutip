@extends('layouts.app')
@section('title', $profile->username)

@section('content')
<div style="max-width:42rem;margin:0 auto;" class="space-y-5">

    {{-- Profile card --}}
    <div class="widget" style="padding:1.5rem;">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;">
            <div style="display:flex;align-items:center;gap:1.125rem;">
                <div class="avatar avatar-xl">
                    {{ strtoupper(substr($profile->username, 0, 2)) }}
                </div>
                <div>
                    <h1 style="font-size:1.125rem;font-weight:700;color:var(--tx-1);">{{ $profile->username }}</h1>
                    <p style="font-size:.8125rem;color:var(--tx-4);margin-top:.25rem;">
                        User ke-{{ number_format($profile->user_number) }}
                        &middot;
                        Bergabung {{ $profile->created_at->diffForHumans() }}
                    </p>
                    @if(!$profile->is_active)
                    <span style="display:inline-block;margin-top:.5rem;font-size:.6875rem;font-weight:600;color:#f59e0b;background:rgba(120,53,15,.35);border:1px solid rgba(146,64,14,.4);padding:.2rem .625rem;border-radius:var(--r-full);">
                        Tidak Aktif
                    </span>
                    @endif
                </div>
            </div>

            {{-- Tombol pengaturan (hanya profil sendiri) --}}
            @if(isset($currentUser) && $currentUser->id === $profile->id)
            <a href="{{ route('settings.show') }}"
                style="display:flex;align-items:center;gap:.375rem;font-size:.75rem;font-weight:500;color:var(--tx-3);background:var(--bg-3);border:1px solid var(--bd-1);border-radius:var(--r-sm);padding:.375rem .75rem;text-decoration:none;white-space:nowrap;transition:all 150ms;flex-shrink:0;"
                onmouseover="this.style.borderColor='rgba(139,92,246,.4)';this.style.color='var(--ac-light)';this.style.background='var(--ac-dim)'"
                onmouseout="this.style.borderColor='var(--bd-1)';this.style.color='var(--tx-3)';this.style.background='var(--bg-3)'">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Pengaturan
            </a>
            @endif
        </div>

        {{-- Stats --}}
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1px;background:var(--bd-1);border-radius:var(--r-sm);overflow:hidden;margin-top:1.25rem;">
            <div class="stat-item" style="background:var(--bg-base);padding:.875rem;">
                <div class="stat-value">{{ number_format($postCount) }}</div>
                <div class="stat-label">Post</div>
            </div>
            <div class="stat-item" style="background:var(--bg-base);padding:.875rem;">
                <div class="stat-value">{{ number_format($replyCount) }}</div>
                <div class="stat-label">Balasan</div>
            </div>
            <div class="stat-item" style="background:var(--bg-base);padding:.875rem;">
                <div class="stat-value">{{ number_format($subforumCount) }}</div>
                <div class="stat-label">Subforum</div>
            </div>
        </div>
    </div>

    {{-- Chat button (profil orang lain) --}}
    @if(isset($currentUser) && $currentUser->id !== $profile->id)
    <form action="{{ route('chat.start') }}" method="POST">
        @csrf
        <input type="hidden" name="username" value="{{ $profile->username }}">
        <button type="submit" class="btn-ghost text-sm" style="width:100%;padding:.625rem;">
            Mulai Chat dengan {{ $profile->username }}
        </button>
    </form>
    @endif

    {{-- Recent posts --}}
    @if($recentPosts->count())
    <div>
        <p class="section-label mb-3">Post Terbaru</p>
        <div class="space-y-2">
            @foreach($recentPosts as $post)
                @include('partials.post-card', ['post' => $post, 'user' => $currentUser])
            @endforeach
        </div>
    </div>
    @else
    <div class="empty-state">Belum ada post.</div>
    @endif

</div>
@endsection
