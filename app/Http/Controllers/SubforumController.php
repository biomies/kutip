<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use App\Models\Post;
use App\Models\Subforum;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubforumController extends Controller
{
    public function show(Request $request, string $forumSlug, string $subforumSlug)
    {
        $user     = $this->currentUser($request);
        $forum    = Forum::where('slug', $forumSlug)->firstOrFail();
        $subforum = Subforum::where('forum_id', $forum->id)
            ->where('slug', $subforumSlug)
            ->with('creator')
            ->firstOrFail();

        $posts = Post::with(['user'])
            ->where('subforum_id', $subforum->id)
            ->latest('last_reply_at')
            ->paginate(20);

        return view('subforum.show', compact('user', 'forum', 'subforum', 'posts'));
    }

    public function create(Request $request, string $forumSlug)
    {
        $user  = $this->currentUser($request);
        $forum = Forum::where('slug', $forumSlug)->where('type', 'niche')->firstOrFail();

        return view('subforum.create', compact('user', 'forum'));
    }

    public function store(Request $request, string $forumSlug)
    {
        $user  = $this->currentUser($request);
        $forum = Forum::where('slug', $forumSlug)->where('type', 'niche')->firstOrFail();

        $validated = $request->validate([
            'name'        => 'required|string|min:3|max:100',
            'description' => 'nullable|string|max:500',
        ]);

        $slug = Str::slug($validated['name']);
        $base = $slug;
        $i    = 1;

        while (Subforum::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        $subforum = Subforum::create([
            'forum_id'    => $forum->id,
            'user_id'     => $user->id,
            'name'        => $validated['name'],
            'slug'        => $slug,
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('subforum.show', [$forum->slug, $subforum->slug])
            ->with('success', 'Subforum berhasil dibuat.');
    }
}
