@php
    $isMine    = isset($user) && $reply->user_id === $user->id;
    $deleted   = $reply->authorDeleted();
    $author    = $reply->authorName();
    $replyHash = hid('reply', $reply->id);
    $postHash  = hid('post', $post->id);
@endphp

<div class="reply-item{{ $reply->depth > 0 ? ' border-l-2' : '' }}"
    style="{{ $reply->depth > 0 ? 'margin-left:'.min($reply->depth * 1.25, 3).'rem;padding-left:1rem;' : '' }}">

    <div class="reply-card" style="background:var(--bg-1);border:1px solid var(--bd-1);border-radius:var(--r-md);padding:.875rem;margin-bottom:.5rem;transition:border-color 150ms;">

        {{-- Header --}}
        <div class="reply-header" style="display:flex;align-items:center;justify-content:space-between;gap:.5rem;margin-bottom:.5rem;">
            <div style="display:flex;align-items:center;gap:.375rem;font-size:.75rem;color:var(--tx-4);">
                @if($deleted)
                    <span style="font-weight:600;color:var(--tx-4);">{{ $author }}</span>
                @else
                    <a href="{{ route('profile.show', $reply->user->username) }}"
                        style="font-weight:600;color:var(--ac-1);transition:color 150ms;"
                        onmouseover="this.style.color='var(--ac-light)'" onmouseout="this.style.color='var(--ac-1)'">
                        {{ $author }}
                    </a>
                @endif
                <span>·</span>
                <time>{{ $reply->created_at->diffForHumans() }}</time>
            </div>

            @if($isMine)
            <div class="dropdown-wrap shrink-0">
                <button onclick="openMenu('reply-menu-{{ $reply->id }}', this)" class="more-btn" title="Opsi">
                    <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24">
                        <circle cx="5" cy="12" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="19" cy="12" r="1.5"/>
                    </svg>
                </button>
                <div id="reply-menu-{{ $reply->id }}" class="dropdown-menu">
                    <button type="button"
                        onclick="openEditForm('reply-content-{{ $reply->id }}','reply-edit-{{ $reply->id }}')"
                        class="dropdown-item">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit balasan
                    </button>
                    <div class="dropdown-divider"></div>
                    <button type="button"
                        onclick="confirmDelete('{{ route('reply.destroy', $replyHash) }}','Hapus balasan?','Balasan ini akan dihapus permanen.')"
                        class="dropdown-item dropdown-item--danger">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Hapus balasan
                    </button>
                </div>
            </div>
            @endif
        </div>

        {{-- Content --}}
        <div id="reply-content-{{ $reply->id }}">
            <p class="content-body" style="font-size:.875rem;color:var(--tx-1);line-height:1.65;word-break:break-word;overflow-wrap:break-word;">{!! $reply->renderContent() !!}</p>
            @include('partials.image-embed', ['model' => $reply])
        </div>

        @if($isMine)
        <div id="reply-edit-{{ $reply->id }}" class="edit-form-wrap">
            <form action="{{ route('reply.update', $replyHash) }}" method="POST">
                @csrf @method('PUT')
                <textarea name="content" rows="2" required maxlength="2000">{{ $reply->content }}</textarea>
                <div class="edit-form-actions">
                    <button type="button" onclick="closeEditForm('reply-content-{{ $reply->id }}','reply-edit-{{ $reply->id }}')" class="btn-ghost text-xs" style="padding:.4rem .875rem;">Batal</button>
                    <button type="submit" class="btn-primary text-xs" style="padding:.4rem .875rem;">Simpan</button>
                </div>
            </form>
        </div>
        @endif

        {{-- Action --}}
        <div style="margin-top:.5rem;padding-top:.5rem;border-top:1px solid var(--bd-1);">
            <button onclick="toggleReplyForm('reply-form-{{ $reply->id }}')"
                style="display:inline-flex;align-items:center;gap:.3rem;font-size:.75rem;color:var(--tx-4);background:none;border:none;cursor:pointer;padding:0;transition:color 150ms;"
                onmouseover="this.style.color='var(--ac-light)'" onmouseout="this.style.color='var(--tx-4)'">
                <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                </svg>
                Balas
            </button>
        </div>
    </div>

    {{-- Nested reply form --}}
    <div id="reply-form-{{ $reply->id }}" class="hidden" style="margin-bottom:.625rem;margin-left:.5rem;">
        <div style="background:var(--bg-2);border:1px solid var(--bd-1);border-radius:var(--r-md);padding:.75rem;">
            <form action="{{ route('reply.store', $postHash) }}" method="POST">
                @csrf
                <input type="hidden" name="parent_id" value="{{ $reply->id }}">
                <textarea name="content" rows="2" placeholder="Tulis balasan..."
                    style="width:100%;background:var(--bg-3);border:1px solid var(--bd-1);border-radius:.375rem;padding:.5rem .75rem;font-size:.8125rem;color:var(--tx-1);resize:none;outline:none;transition:border-color 150ms,box-shadow 150ms;"
                    onfocus="this.style.borderColor='var(--ac-2)';this.style.boxShadow='0 0 0 3px var(--ac-dim)'"
                    onblur="this.style.borderColor='var(--bd-1)';this.style.boxShadow='none'"
                    required></textarea>
                <div style="display:flex;gap:.5rem;margin-top:.5rem;">
                    <button type="submit" class="btn-primary text-xs" style="padding:.375rem .875rem;">Kirim</button>
                    <button type="button" onclick="toggleReplyForm('reply-form-{{ $reply->id }}')" class="btn-ghost text-xs" style="padding:.375rem .875rem;">Batal</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Recursive children --}}
    @if($reply->children?->count())
        @foreach($reply->children as $child)
            @include('partials.reply-thread', ['reply' => $child, 'post' => $post, 'user' => $user])
        @endforeach
    @endif
</div>
