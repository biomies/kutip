<?php

namespace App\Http\Middleware;

use App\Models\Chat;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class IdentifyAnonymousUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->cookie('koar_token');

        if (!$token) {
            $token = Str::random(64);
        }

        $user = Cache::remember('user_token_' . $token, 300, function () use ($token) {
            return User::whereNotNull('browser_token')->where('browser_token', $token)->first();
        });

        if (!$user) {
            // Hapus cache token yang tidak valid
            Cache::forget('user_token_' . $token);

            $userNumber = User::nextUserNumber();
            $user = User::create([
                'uuid'           => (string) Str::uuid(),
                'browser_token'  => $token,
                'username'       => 'user-' . $userNumber,
                'user_number'    => $userNumber,
                'last_active_at' => now(),
                'is_active'      => true,
            ]);

            // Cache user baru
            Cache::put('user_token_' . $token, $user, 300);
        } else {
            // Update last_active hanya sekali per jam, bukan tiap request
            $activityKey = 'user_active_' . $user->id;
            if (!Cache::has($activityKey)) {
                $needsUpdate = !$user->is_active
                    || !$user->last_active_at
                    || $user->last_active_at->diffInDays(now()) >= 1;

                if ($needsUpdate) {
                    $user->update([
                        'last_active_at' => now(),
                        'is_active'      => true,
                    ]);
                    // Invalidate user cache agar data fresh
                    Cache::forget('user_token_' . $token);
                }

                // Tandai sudah cek activity — skip selama 1 jam
                Cache::put($activityKey, 1, 3600);
            }
        }

        $request->attributes->set('koar_user', $user);

        // Unread count di-cache per user selama 60 detik
        // Diinvalidate saat ada pesan baru atau pesan dibaca (di ChatController)
        $unreadCount = Cache::remember('unread_' . $user->id, 60, function () use ($user) {
            return Chat::where(function ($q) use ($user) {
                    $q->where('user_one_id', $user->id)
                      ->orWhere('user_two_id', $user->id);
                })
                ->join('chat_messages', 'chats.id', '=', 'chat_messages.chat_id')
                ->where('chat_messages.user_id', '!=', $user->id)
                ->where('chat_messages.is_read', false)
                ->count();
        });

        View::share('unreadCount', $unreadCount);

        $response = $next($request);

        // $secure = true di production (HTTPS), false hanya di local dev
        $secure = app()->isProduction() || $request->isSecure();
        $response->cookie('koar_token', $token, 60 * 24 * 365, '/', null, $secure, true, false, 'Lax');

        return $response;
    }
}
