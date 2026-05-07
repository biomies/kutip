@extends('layouts.app')
@section('title', 'Chat')

@section('content')
<div style="max-width:36rem;margin:0 auto;" class="space-y-4">

    <h1 style="font-size:1.125rem;font-weight:700;color:var(--tx-1);">Chat</h1>

    {{-- Start chat --}}
    <div class="widget">
        <p class="widget-label">Mulai percakapan baru</p>
        <form action="{{ route('chat.start') }}" method="POST" style="display:flex;gap:.625rem;">
            @csrf
            <input type="text" name="username" placeholder="Masukkan username..."
                style="flex:1;padding:.625rem .875rem;font-size:.875rem;" required>
            <button type="submit" class="btn-primary text-sm shrink-0" style="padding:.625rem 1rem;">Chat</button>
        </form>
        @error('username')
        <p style="font-size:.75rem;color:var(--danger);margin-top:.375rem;">{{ $message }}</p>
        @enderror
    </div>

    {{-- Chat list --}}
    <div class="space-y-2">
        @forelse($chats as $chat)
        @php
            $other      = $chat->getOtherUser($user);
            $otherName  = $other?->username ?? '[deleted]';
            $otherInitial = strtoupper(substr($otherName === '[deleted]' ? 'DE' : $otherName, 0, 2));
        @endphp
        <a href="{{ route('chat.show', hid('chat', $chat->id)) }}"
            style="display:flex;align-items:center;justify-content:space-between;gap:.875rem;padding:.875rem 1rem;background:var(--bg-1);border:1px solid var(--bd-1);border-radius:var(--r-md);text-decoration:none;transition:border-color 150ms,background 150ms;"
            onmouseover="this.style.borderColor='rgba(139,92,246,.35)';this.style.background='var(--bg-2)'"
            onmouseout="this.style.borderColor='var(--bd-1)';this.style.background='var(--bg-1)'">
            <div style="display:flex;align-items:center;gap:.75rem;">
                <div class="avatar avatar-md" style="{{ $other === null || $other->trashed() ? 'opacity:.45;' : '' }}">
                    {{ $otherInitial }}
                </div>
                <div>
                    <div style="font-size:.875rem;font-weight:500;color:{{ $other === null || $other->trashed() ? 'var(--tx-4)' : 'var(--tx-1)' }};">
                        {{ $otherName }}
                    </div>
                    @if($chat->last_message_at)
                    <div style="font-size:.75rem;color:var(--tx-4);margin-top:.125rem;">{{ $chat->last_message_at->diffForHumans() }}</div>
                    @endif
                </div>
            </div>
            @if(isset($chat->unread_count) && $chat->unread_count > 0)
            <span style="background:var(--ac-1);color:#fff;font-size:.6875rem;font-weight:700;border-radius:var(--r-full);padding:.125rem .5rem;min-width:1.25rem;text-align:center;">
                {{ $chat->unread_count }}
            </span>
            @endif
        </a>
        @empty
        <div class="empty-state">Belum ada percakapan.</div>
        @endforelse
    </div>

</div>
@endsection
