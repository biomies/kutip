@php
    $otherName    = $other?->username ?? '[deleted]';
    $otherDeleted = $other === null || $other->trashed();
    $otherInitial = strtoupper(substr($otherDeleted ? 'DE' : $otherName, 0, 2));
@endphp
@extends('layouts.app')
@section('title', 'Chat dengan ' . $otherName)

@section('content')
<div class="chat-height-mobile" style="max-width:36rem;margin:0 auto;display:flex;flex-direction:column;height:calc(100vh - 9rem);">

    {{-- Header --}}
    <div style="display:flex;align-items:center;gap:.875rem;padding-bottom:.875rem;border-bottom:1px solid var(--bd-1);margin-bottom:.875rem;flex-shrink:0;">
        <a href="{{ route('chat.index') }}"
            style="color:var(--tx-4);display:flex;align-items:center;transition:color 150ms;"
            onmouseover="this.style.color='var(--ac-light)'" onmouseout="this.style.color='var(--tx-4)'">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div class="avatar avatar-sm" style="{{ $otherDeleted ? 'opacity:.45;' : '' }}">{{ $otherInitial }}</div>
        <div>
            @if($otherDeleted)
                <span style="font-size:.875rem;font-weight:600;color:var(--tx-4);">{{ $otherName }}</span>
            @else
                <a href="{{ route('profile.show', $other->username) }}"
                    style="font-size:.875rem;font-weight:600;color:var(--tx-1);transition:color 150ms;"
                    onmouseover="this.style.color='var(--ac-light)'" onmouseout="this.style.color='var(--tx-1)'">
                    {{ $otherName }}
                </a>
            @endif
            <div style="font-size:.6875rem;color:var(--tx-4);margin-top:.0625rem;">
                @if($otherDeleted)
                    ○ Akun dihapus
                @else
                    {{ $other->is_active ? '● Aktif' : '○ Tidak aktif' }}
                @endif
            </div>
        </div>
    </div>

    {{-- Messages --}}
    <div id="messages-container" style="flex:1;overflow-y:auto;display:flex;flex-direction:column;gap:.625rem;padding-right:.25rem;margin-bottom:.875rem;">
        @forelse($messages as $msg)
        @php $isMe = $msg->user_id === $user->id; @endphp
        <div style="display:flex;{{ $isMe ? 'justify-content:flex-end' : 'justify-content:flex-start' }}">
            <div style="max-width:20rem;">
                @if(!$isMe)
                <div style="font-size:.6875rem;color:var(--tx-4);margin-bottom:.25rem;margin-left:.25rem;">
                    {{ $msg->user?->username ?? '[deleted]' }}
                </div>
                @endif
                <div class="{{ $isMe ? 'chat-bubble-me' : 'chat-bubble-them' }}">{{ $msg->content }}</div>
                <div class="chat-time" style="{{ $isMe ? 'text-align:right;margin-right:.25rem;' : 'margin-left:.25rem;' }}">
                    {{ $msg->created_at->format('H:i') }}
                </div>
            </div>
        </div>
        @empty
        <div class="empty-state" style="margin:auto;">Mulai percakapan! 👋</div>
        @endforelse
    </div>

    {{-- Input --}}
    <form action="{{ route('chat.send', hid('chat', $chat->id)) }}" method="POST"
        style="display:flex;gap:.625rem;flex-shrink:0;padding-top:.625rem;border-top:1px solid var(--bd-1);">
        @csrf
        <input type="text" name="content" placeholder="Ketik pesan..."
            autocomplete="off" required maxlength="2000" autofocus
            style="flex:1;padding:.625rem .875rem;font-size:.875rem;border-radius:var(--r-xl);">
        <button type="submit" class="btn-primary shrink-0" style="padding:.625rem .875rem;border-radius:var(--r-xl);">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
            </svg>
        </button>
    </form>

</div>

<script>
    const c = document.getElementById('messages-container');
    if (c) c.scrollTop = c.scrollHeight;
</script>
@endsection
