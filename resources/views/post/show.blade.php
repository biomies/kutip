@extends('layouts.app')
@section('title', 'Post')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="lg:col-span-2 space-y-4">

        {{-- Breadcrumb --}}
        <nav class="breadcrumb">
            @if($post->subforum)
                <a href="{{ route('forum.show', $post->forum->slug) }}">{{ $post->forum->name }}</a>
                <span class="breadcrumb-sep">/</span>
                <a href="{{ route('subforum.show', [$post->forum->slug, $post->subforum->slug]) }}">{{ $post->subforum->name }}</a>
            @else
                <a href="{{ route('forum.show', $post->forum->slug) }}">{{ $post->forum->name }}</a>
            @endif
            <span class="breadcrumb-sep">/</span>
            <span style="color:var(--tx-3)">Post</span>
        </nav>

        {{-- Post --}}
        <article class="widget" style="padding:1.25rem;">

            {{-- Header --}}
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;margin-bottom:1rem;">
                <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;font-size:.8125rem;color:var(--tx-4);">
                    @if($post->authorDeleted())
                        <span style="font-weight:600;font-size:.875rem;color:var(--tx-4);">{{ $post->authorName() }}</span>
                    @else
                        <a href="{{ route('profile.show', $post->user->username) }}"
                            style="font-weight:600;font-size:.875rem;color:var(--ac-1);transition:color 150ms;"
                            onmouseover="this.style.color='var(--ac-light)'" onmouseout="this.style.color='var(--ac-1)'">
                            {{ $post->authorName() }}
                        </a>
                    @endif
                    <span>·</span>
                    <time>{{ $post->created_at->diffForHumans() }}</time>
                    <span>·</span>
                    <span>{{ $post->reply_count }} balasan</span>
                </div>

                @if(isset($user) && $post->user_id === $user->id)
                <div class="dropdown-wrap shrink-0">
                    <button onclick="openMenu('post-detail-menu', this)" class="more-btn active-always" title="Opsi">
                        <svg width="15" height="15" fill="currentColor" viewBox="0 0 24 24">
                            <circle cx="5" cy="12" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="19" cy="12" r="1.5"/>
                        </svg>
                    </button>
                    <div id="post-detail-menu" class="dropdown-menu">
                        <button type="button" onclick="openEditForm('post-detail-content','post-detail-edit')" class="dropdown-item">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit post
                        </button>
                        <div class="dropdown-divider"></div>
                        <button type="button"
                            onclick="confirmDelete('{{ route('post.destroy', hid('post', $post->id)) }}','Hapus post?','Post ini beserta semua balasannya akan dihapus permanen.')"
                            class="dropdown-item dropdown-item--danger">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Hapus post
                        </button>
                    </div>
                </div>
                @endif
            </div>

            {{-- Content --}}
            <div id="post-detail-content">
                <p class="content-body" style="color:var(--tx-1);line-height:1.75;white-space:pre-line;word-break:break-word;overflow-wrap:break-word;font-size:.9375rem;">{!! $post->renderContent() !!}</p>
                @include('partials.image-embed', ['model' => $post])
            </div>

            @if(isset($user) && $post->user_id === $user->id)
            <div id="post-detail-edit" class="edit-form-wrap">
                <form action="{{ route('post.update', hid('post', $post->id)) }}" method="POST">
                    @csrf @method('PUT')
                    <textarea name="content" rows="5" required maxlength="5000">{{ $post->content }}</textarea>
                    <div class="edit-form-actions">
                        <button type="button" onclick="closeEditForm('post-detail-content','post-detail-edit')" class="btn-ghost text-sm" style="padding:.5rem 1rem;">Batal</button>
                        <button type="submit" class="btn-primary text-sm" style="padding:.5rem 1rem;">Simpan</button>
                    </div>
                </form>
            </div>
            @endif

        </article>

        {{-- Reply compose --}}
        <div id="replies" class="compose-box">
            <p class="compose-label">Tulis Balasan</p>
            <form action="{{ route('reply.store', hid('post', $post->id)) }}" method="POST">
                @csrf
                <textarea name="content" rows="3" placeholder="Tulis balasanmu..."
                    required maxlength="2000"></textarea>
                <div class="compose-footer">
                    <button type="submit" class="btn-primary text-sm" style="padding:.5rem 1.25rem;">Balas</button>
                </div>
            </form>
        </div>

        {{-- Replies --}}
        @if($replies->count())
        <div>
            <p class="section-label mb-3">{{ $replies->count() }} Balasan</p>
            <div class="space-y-2">
                @foreach($replies as $reply)
                    @include('partials.reply-thread', ['reply' => $reply, 'post' => $post, 'user' => $user])
                @endforeach
            </div>
        </div>
        @endif

    </div>

    {{-- Sidebar --}}
    <aside class="space-y-3 post-sidebar-mobile-hidden">
        <div class="widget">
            <p class="widget-label">Diposting oleh</p>
            @if($post->authorDeleted())
                <span style="font-size:.875rem;font-weight:600;color:var(--tx-4);">{{ $post->authorName() }}</span>
            @else
                <a href="{{ route('profile.show', $post->user->username) }}"
                    style="font-size:.875rem;font-weight:600;color:var(--ac-1);transition:color 150ms;"
                    onmouseover="this.style.color='var(--ac-light)'" onmouseout="this.style.color='var(--ac-1)'">
                    {{ $post->authorName() }}
                </a>
            @endif
            <p style="font-size:.75rem;color:var(--tx-4);margin-top:.375rem;">{{ $post->created_at->diffForHumans() }}</p>
        </div>

        @if(!$post->authorDeleted())
        <div class="widget">
            <p class="widget-label" style="margin-bottom:.625rem;">Chat user ini</p>
            <form action="{{ route('chat.start') }}" method="POST">
                @csrf
                <input type="hidden" name="username" value="{{ $post->user->username }}">
                <button type="submit" class="btn-ghost text-sm" style="width:100%;padding:.5rem;">Mulai Chat</button>
            </form>
        </div>
        @endif
    </aside>

</div>
@endsection
