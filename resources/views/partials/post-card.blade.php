@php
    $isMine   = isset($user) && $post->user_id === $user->id;
    $deleted  = $post->authorDeleted();
    $author   = $post->authorName();
    $postHash = hid('post', $post->id);
@endphp

<article class="post-card-wrap card" style="padding:1rem;">

    {{-- Meta + more button --}}
    <div style="display:flex;align-items:center;justify-content:space-between;gap:.5rem;margin-bottom:.625rem;">
        <div style="display:flex;align-items:center;gap:.375rem;font-size:.75rem;color:var(--tx-4);min-width:0;flex-wrap:wrap;">
            @if($deleted)
                <span style="font-weight:600;color:var(--tx-4);white-space:nowrap;">{{ $author }}</span>
            @else
                <a href="{{ route('profile.show', $post->user->username) }}"
                    style="font-weight:600;color:var(--ac-1);white-space:nowrap;transition:color 150ms;"
                    onmouseover="this.style.color='var(--ac-light)'" onmouseout="this.style.color='var(--ac-1)'">
                    {{ $author }}
                </a>
            @endif
            <span>·</span>
            <time style="white-space:nowrap;">{{ $post->created_at->diffForHumans() }}</time>

            @if($post->subforum)
            <span>·</span>
            <a href="{{ route('subforum.show', [$post->forum->slug, $post->subforum->slug]) }}"
                style="color:var(--tx-3);transition:color 150ms;"
                onmouseover="this.style.color='var(--tx-2)'" onmouseout="this.style.color='var(--tx-3)'">
                {{ $post->forum->name }} / {{ $post->subforum->name }}
            </a>
            @elseif($post->forum)
            <span>·</span>
            <a href="{{ route('forum.show', $post->forum->slug) }}"
                style="color:var(--tx-3);transition:color 150ms;"
                onmouseover="this.style.color='var(--tx-2)'" onmouseout="this.style.color='var(--tx-3)'">
                {{ $post->forum->name }}
            </a>
            @endif
        </div>

        @if($isMine)
        <div class="dropdown-wrap shrink-0 post-card-actions">
            <button onclick="openMenu('post-menu-{{ $post->id }}', this)" class="more-btn" title="Opsi">
                <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24">
                    <circle cx="5" cy="12" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="19" cy="12" r="1.5"/>
                </svg>
            </button>
            <div id="post-menu-{{ $post->id }}" class="dropdown-menu drop-up">
                <button type="button"
                    onclick="openEditForm('post-content-{{ $post->id }}','post-edit-{{ $post->id }}')"
                    class="dropdown-item">
                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit post
                </button>
                <div class="dropdown-divider"></div>
                <button type="button"
                    onclick="confirmDelete('{{ route('post.destroy', $postHash) }}','Hapus post?','Post ini akan dihapus permanen dan tidak bisa dikembalikan.')"
                    class="dropdown-item dropdown-item--danger">
                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Hapus post
                </button>
            </div>
        </div>
        @endif
    </div>

    {{-- Content --}}
    <div id="post-content-{{ $post->id }}">
        <a href="{{ route('post.show', $postHash) }}" style="display:block;text-decoration:none;">
            <p class="content-body" style="color:var(--tx-1);font-size:.875rem;line-height:1.65;display:-webkit-box;-webkit-line-clamp:4;-webkit-box-orient:vertical;overflow:hidden;word-break:break-word;overflow-wrap:break-word;transition:color 150ms;"
                onmouseover="this.style.color='#fff'" onmouseout="this.style.color='var(--tx-1)'">{!! $post->renderContent() !!}</p>
        </a>
        @include('partials.image-embed', ['model' => $post])
    </div>

    {{-- Edit form --}}
    @if($isMine)
    <div id="post-edit-{{ $post->id }}" class="edit-form-wrap">
        <form action="{{ route('post.update', $postHash) }}" method="POST">
            @csrf @method('PUT')
            <textarea name="content" rows="3" required maxlength="5000">{{ $post->content }}</textarea>
            <div class="edit-form-actions">
                <button type="button" onclick="closeEditForm('post-content-{{ $post->id }}','post-edit-{{ $post->id }}')" class="btn-ghost text-xs" style="padding:.4rem .875rem;">Batal</button>
                <button type="submit" class="btn-primary text-xs" style="padding:.4rem .875rem;">Simpan</button>
            </div>
        </form>
    </div>
    @endif

    {{-- Footer --}}
    <div style="margin-top:.75rem;padding-top:.625rem;border-top:1px solid var(--bd-1);">
        <a href="{{ route('post.show', $postHash) }}#replies"
            style="display:inline-flex;align-items:center;gap:.375rem;font-size:.75rem;color:var(--tx-4);transition:color 150ms;"
            onmouseover="this.style.color='var(--ac-light)'" onmouseout="this.style.color='var(--tx-4)'">
            <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
            </svg>
            {{ $post->reply_count }} balasan
        </a>
    </div>

</article>
