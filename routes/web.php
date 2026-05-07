<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\ImageProxyController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReplyController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SubforumController;
use Illuminate\Support\Facades\Route;

// Image proxy — publik tapi domain di-whitelist ketat di controller
Route::get('/img-proxy', [ImageProxyController::class, 'show'])
    ->middleware('throttle:60,1')   // 60 req/menit per IP
    ->name('img.proxy');

Route::middleware(\App\Http\Middleware\IdentifyAnonymousUser::class)->group(function () {

    // Home & Forums — baca saja, throttle longgar
    Route::get('/', [ForumController::class, 'index'])->name('home');
    Route::get('/forums', [ForumController::class, 'allForums'])->name('forums.all');
    Route::get('/f/{slug}', [ForumController::class, 'show'])->name('forum.show');
    Route::get('/f/{forumSlug}/{subforumSlug}', [SubforumController::class, 'show'])->name('subforum.show');
    Route::get('/post/{hash}', [PostController::class, 'show'])->name('post.show');
    Route::get('/u/{username}', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{hash}', [ChatController::class, 'show'])->name('chat.show');
    Route::get('/settings', [SettingsController::class, 'show'])->name('settings.show');

    // Subforum create form
    Route::get('/f/{forumSlug}/create-subforum', [SubforumController::class, 'create'])->name('subforum.create');

    // --- Write endpoints — throttle ketat ---

    // Membuat subforum: 5 per 10 menit
    Route::post('/f/{forumSlug}/create-subforum', [SubforumController::class, 'store'])
        ->middleware('throttle:5,10')
        ->name('subforum.store');

    // Membuat post: 10 per menit
    Route::post('/post', [PostController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('post.store');

    // Edit/hapus post: 20 per menit
    Route::put('/post/{hash}', [PostController::class, 'update'])
        ->middleware('throttle:20,1')
        ->name('post.update');
    Route::delete('/post/{hash}', [PostController::class, 'destroy'])
        ->middleware('throttle:20,1')
        ->name('post.destroy');

    // Reply: 15 per menit
    Route::post('/post/{postHash}/reply', [ReplyController::class, 'store'])
        ->middleware('throttle:15,1')
        ->name('reply.store');
    Route::put('/reply/{hash}', [ReplyController::class, 'update'])
        ->middleware('throttle:20,1')
        ->name('reply.update');
    Route::delete('/reply/{hash}', [ReplyController::class, 'destroy'])
        ->middleware('throttle:20,1')
        ->name('reply.destroy');

    // Chat: mulai chat 10/menit, kirim pesan 30/menit
    Route::post('/chat/start', [ChatController::class, 'startOrRedirect'])
        ->middleware('throttle:10,1')
        ->name('chat.start');
    Route::post('/chat/{hash}/message', [ChatController::class, 'sendMessage'])
        ->middleware('throttle:30,1')
        ->name('chat.send');

    // Settings: ganti username 5/jam, hapus akun 3/jam
    Route::put('/settings/username', [SettingsController::class, 'updateUsername'])
        ->middleware('throttle:5,60')
        ->name('settings.updateUsername');
    Route::delete('/settings/account', [SettingsController::class, 'destroy'])
        ->middleware('throttle:3,60')
        ->name('settings.destroy');

});
