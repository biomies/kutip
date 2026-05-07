<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request, string $username)
    {
        $currentUser = $this->currentUser($request);
        $profile     = User::where('username', $username)->firstOrFail();

        $postCount     = $profile->posts()->count();
        $replyCount    = $profile->replies()->count();
        $subforumCount = $profile->subforums()->count();

        $recentPosts = $profile->posts()
            ->with(['forum', 'subforum'])
            ->latest()
            ->limit(10)
            ->get();

        return view('profile.show', compact(
            'currentUser',
            'profile',
            'postCount',
            'replyCount',
            'subforumCount',
            'recentPosts'
        ));
    }
}
