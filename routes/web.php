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

// Image proxy — tanpa middleware user (publik, tapi domain di-whitelist di controller)
Route::get('/img-proxy', [ImageProxyController::class, 'show'])->name('img.proxy');

Route::middleware(\App\Http\Middleware\IdentifyAnonymousUser::class)->group(function () {

    // Home
    Route::get('/', [ForumController::class, 'index'])->name('home');

    // Forums
    Route::get('/forums', [ForumController::class, 'allForums'])->name('forums.all');
    Route::get('/f/{slug}', [ForumController::class, 'show'])->name('forum.show');

    // Subforums
    Route::get('/f/{forumSlug}/create-subforum', [SubforumController::class, 'create'])->name('subforum.create');
    Route::post('/f/{forumSlug}/create-subforum', [SubforumController::class, 'store'])->name('subforum.store');
    Route::get('/f/{forumSlug}/{subforumSlug}', [SubforumController::class, 'show'])->name('subforum.show');

    // Posts
    Route::get('/post/{hash}', [PostController::class, 'show'])->name('post.show');
    Route::post('/post', [PostController::class, 'store'])->name('post.store');
    Route::put('/post/{hash}', [PostController::class, 'update'])->name('post.update');
    Route::delete('/post/{hash}', [PostController::class, 'destroy'])->name('post.destroy');

    // Replies
    Route::post('/post/{postHash}/reply', [ReplyController::class, 'store'])->name('reply.store');
    Route::put('/reply/{hash}', [ReplyController::class, 'update'])->name('reply.update');
    Route::delete('/reply/{hash}', [ReplyController::class, 'destroy'])->name('reply.destroy');

    // Chat
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/start', [ChatController::class, 'startOrRedirect'])->name('chat.start');
    Route::get('/chat/{hash}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{hash}/message', [ChatController::class, 'sendMessage'])->name('chat.send');

    // Profile
    Route::get('/u/{username}', [ProfileController::class, 'show'])->name('profile.show');

    // Settings
    Route::get('/settings', [SettingsController::class, 'show'])->name('settings.show');
    Route::put('/settings/username', [SettingsController::class, 'updateUsername'])->name('settings.updateUsername');
    Route::delete('/settings/account', [SettingsController::class, 'destroy'])->name('settings.destroy');

});
