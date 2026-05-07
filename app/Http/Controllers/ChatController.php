<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\User;
use App\Services\Hashid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $user = $this->currentUser($request);

        $chats = Chat::where('user_one_id', $user->id)
            ->orWhere('user_two_id', $user->id)
            ->with(['userOne', 'userTwo'])
            ->withCount(['messages as unread_count' => function ($q) use ($user) {
                $q->where('user_id', '!=', $user->id)->where('is_read', false);
            }])
            ->latest('last_message_at')
            ->get();

        return view('chat.index', compact('user', 'chats'));
    }

    public function show(Request $request, string $hash)
    {
        $user   = $this->currentUser($request);
        $chatId = Hashid::decode('chat', $hash);
        $chat   = Chat::findOrFail($chatId);

        if ($chat->user_one_id !== $user->id && $chat->user_two_id !== $user->id) {
            abort(403);
        }

        ChatMessage::where('chat_id', $chat->id)
            ->where('user_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Invalidate unread cache setelah pesan dibaca
        Cache::forget('unread_' . $user->id);

        $messages = $chat->messages()->with(['user' => fn($q) => $q->withTrashed()])->get();
        $other    = $chat->getOtherUser($user);

        return view('chat.show', compact('user', 'chat', 'messages', 'other'));
    }

    public function startOrRedirect(Request $request)
    {
        $currentUser = $this->currentUser($request);

        $validated = $request->validate([
            'username' => 'required|string',
        ]);

        // Jangan sertakan soft-deleted user
        $target = User::whereNull('deleted_at')
            ->where('username', $validated['username'])
            ->first();

        if (!$target) {
            // Pesan generik — tidak bocorkan apakah user ada tapi dihapus
            return back()->withErrors(['username' => 'User tidak ditemukan.']);
        }

        if ($target->id === $currentUser->id) {
            return back()->withErrors(['username' => 'Tidak bisa chat dengan diri sendiri.']);
        }

        $chat = Chat::findOrCreateBetween($currentUser->id, $target->id);

        return redirect()->route('chat.show', hid('chat', $chat->id));
    }

    public function sendMessage(Request $request, string $hash)
    {
        $user   = $this->currentUser($request);
        $chatId = Hashid::decode('chat', $hash);
        $chat   = Chat::findOrFail($chatId);

        if ($chat->user_one_id !== $user->id && $chat->user_two_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'content' => 'required|string|min:1|max:2000',
        ]);

        ChatMessage::create([
            'chat_id' => $chat->id,
            'user_id' => $user->id,
            'content' => $validated['content'],
        ]);

        $chat->update(['last_message_at' => now()]);

        // Invalidate unread cache penerima agar badge langsung update
        $otherId = $chat->user_one_id === $user->id ? $chat->user_two_id : $chat->user_one_id;
        Cache::forget('unread_' . $otherId);

        return redirect()->route('chat.show', hid('chat', $chat->id));
    }
}
