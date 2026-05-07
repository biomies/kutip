@extends('layouts.app')
@section('title', 'Beranda')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Sidebar kiri --}}
    <aside class="lg:col-span-1 space-y-6 sidebar-mobile-hidden">

        {{-- Global Forums --}}
        <div>
            <p class="section-label mb-3">Global</p>
            <div class="space-y-1">
                @foreach($globalForums as $forum)
                <a href="{{ route('forum.show', $forum->slug) }}" class="forum-item">
                    <span class="forum-icon">{{ $forum->icon ?? '🌐' }}</span>
                    <div class="min-w-0">
                        <div class="forum-item-name">{{ $forum->name }}</div>
                        @if($forum->description)
                        <div class="forum-item-desc truncate">{{ Str::limit($forum->description, 48) }}</div>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
        </div>

        {{-- Niche Forums --}}
        <div>
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem;">
                <p class="section-label" style="margin:0;">Forum Populer</p>
                <a href="{{ route('forums.all') }}"
                    style="font-size:.6875rem;font-weight:500;color:var(--ac-1);transition:color 150ms;"
                    onmouseover="this.style.color='var(--ac-light)'" onmouseout="this.style.color='var(--ac-1)'">
                    Lihat semua →
                </a>
            </div>
            <div class="space-y-1">
                @foreach($nicheForums as $forum)
                <a href="{{ route('forum.show', $forum->slug) }}" class="forum-item" style="justify-content:space-between;">
                    <div style="display:flex;align-items:center;gap:.875rem;min-width:0;">
                        <span class="forum-icon">{{ $forum->icon ?? '💬' }}</span>
                        <div class="min-w-0">
                            <div class="forum-item-name">{{ $forum->name }}</div>
                            @if($forum->description)
                            <div class="forum-item-desc truncate">{{ Str::limit($forum->description, 38) }}</div>
                            @endif
                        </div>
                    </div>
                    <span class="forum-item-meta shrink-0">{{ $forum->posts_count }}</span>
                </a>
                @endforeach
            </div>

            {{-- Tombol lihat semua kalau ada lebih dari 10 --}}
            @if($totalNiche > 10)
            <a href="{{ route('forums.all') }}"
                style="display:flex;align-items:center;justify-content:center;gap:.375rem;width:100%;margin-top:.625rem;padding:.5rem;font-size:.75rem;font-weight:500;color:var(--tx-3);background:var(--bg-2);border:1px solid var(--bd-1);border-radius:var(--r-sm);text-decoration:none;transition:all 150ms;"
                onmouseover="this.style.borderColor='rgba(139,92,246,.35)';this.style.color='var(--ac-light)'"
                onmouseout="this.style.borderColor='var(--bd-1)';this.style.color='var(--tx-3)'">
                Lihat {{ number_format($totalNiche - 10) }} forum lainnya
                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
            @endif
        </div>

        {{-- Panduan foto --}}
        <div class="widget" style="padding:1rem;">
            <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.75rem;">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color:var(--ac-light);flex-shrink:0;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="section-label" style="margin:0;">Cara Kirim Foto</p>
            </div>

            <div style="display:flex;flex-direction:column;gap:.875rem;">

                {{-- Cara 1: Copy image address --}}
                <div>
                    <p style="font-size:.75rem;font-weight:600;color:var(--tx-2);margin-bottom:.375rem;">
                        1. Copy Address Gambar
                    </p>
                    <p style="font-size:.6875rem;color:var(--tx-4);line-height:1.6;">
                        Klik kanan gambar di browser → pilih
                        <span style="background:var(--bg-3);border:1px solid var(--bd-1);border-radius:.25rem;padding:.05rem .35rem;font-size:.625rem;color:var(--tx-2);white-space:nowrap;">Copy image address</span>
                        → paste langsung di kolom post.
                    </p>
                    <div style="margin-top:.5rem;background:var(--bg-3);border-radius:.375rem;padding:.5rem .625rem;font-size:.625rem;color:var(--tx-4);font-family:ui-monospace,monospace;word-break:break-all;border:1px solid var(--bd-1);">
                        https://example.com/foto.jpg
                    </div>
                </div>

                <div style="height:1px;background:var(--bd-1);"></div>

                {{-- Cara 2: Google Drive --}}
                <div>
                    <p style="font-size:.75rem;font-weight:600;color:var(--tx-2);margin-bottom:.375rem;">
                        2. Via Google Drive
                    </p>
                    <ol style="font-size:.6875rem;color:var(--tx-4);line-height:1.7;padding-left:1rem;display:flex;flex-direction:column;gap:.1rem;">
                        <li>Upload foto ke Google Drive</li>
                        <li>Klik kanan file → <span style="background:var(--bg-3);border:1px solid var(--bd-1);border-radius:.25rem;padding:.05rem .35rem;font-size:.625rem;color:var(--tx-2);white-space:nowrap;">Share</span></li>
                        <li>Set akses ke <span style="color:var(--ac-light);font-weight:500;">Anyone with the link</span></li>
                        <li>Copy link → paste di kolom post</li>
                    </ol>
                    <div style="margin-top:.5rem;background:var(--bg-3);border-radius:.375rem;padding:.5rem .625rem;font-size:.625rem;color:var(--tx-4);font-family:ui-monospace,monospace;word-break:break-all;border:1px solid var(--bd-1);">
                        https://drive.google.com/file/d/<span style="color:var(--ac-light);">FILE_ID</span>/view?usp=sharing
                    </div>
                </div>

                <div style="height:1px;background:var(--bd-1);"></div>

                {{-- Info --}}
                <p style="font-size:.625rem;color:var(--tx-4);line-height:1.55;">
                    ✦ Gambar muncul otomatis setelah post dikirim.<br>
                    ✦ Maks 4 gambar per post.<br>
                    ✦ Format: JPG, PNG, GIF, WEBP.
                </p>

            </div>
        </div>

    </aside>

    {{-- Konten utama --}}
    <div class="lg:col-span-2 space-y-4">

        {{-- Compose box --}}
        @php $defaultGlobal = $globalForums->first(); @endphp
        @if($defaultGlobal)
        <div class="compose-box">
            <p class="compose-label">Posting di <span style="color:var(--ac-light)">{{ $defaultGlobal->name }}</span></p>
            <form action="{{ route('post.store') }}" method="POST">
                @csrf
                <input type="hidden" name="forum_id" value="{{ $defaultGlobal->id }}">
                <textarea name="content" rows="3"
                    placeholder="Apa yang ingin kamu kutip hari ini?"
                    required maxlength="5000"></textarea>
                <div class="compose-footer">
                    <button type="submit" class="btn-primary text-sm" style="padding:.5rem 1.25rem;">Kutip</button>
                </div>
            </form>
        </div>
        @endif

        {{-- Recent posts --}}
        <div>
            <p class="section-label mb-3">Terbaru</p>
            <div class="space-y-2">
                @forelse($recentPosts as $post)
                    @include('partials.post-card', ['post' => $post, 'user' => $user])
                @empty
                <div class="empty-state">Belum ada post. Jadilah yang pertama!</div>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection
