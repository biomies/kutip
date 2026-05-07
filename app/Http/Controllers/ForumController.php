<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use App\Models\Post;
use Illuminate\Http\Request;

class ForumController extends Controller
{
    public function index(Request $request)
    {
        $user = $this->currentUser($request);

        $globalForums = Forum::where('type', 'global')->orderBy('order')->get();

        // Sidebar: top 10 niche forum berdasarkan total post terbanyak
        $nicheForums = Forum::where('type', 'niche')
            ->withCount(['subforums', 'posts'])
            ->orderByDesc('posts_count')
            ->orderBy('order')
            ->limit(10)
            ->get();

        $globalForumIds = $globalForums->pluck('id');

        $recentPosts = Post::with(['user', 'forum', 'subforum'])
            ->whereIn('forum_id', $globalForumIds)
            ->latest()
            ->limit(20)
            ->get();

        // Total niche untuk tahu apakah perlu tombol "lihat semua"
        $totalNiche = Forum::where('type', 'niche')->count();

        return view('forum.index', compact(
            'user', 'globalForums', 'nicheForums', 'recentPosts', 'totalNiche'
        ));
    }

    public function allForums(Request $request)
    {
        $user = $this->currentUser($request);

        $search = $request->query('q', '');

        $forums = Forum::where('type', 'niche')
            ->withCount(['subforums', 'posts'])
            ->when($search, fn($q) => $q->where('name', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%'))
            ->orderByDesc('posts_count')
            ->orderBy('name')
            ->paginate(24)
            ->withQueryString();

        return view('forum.all', compact('user', 'forums', 'search'));
    }

    public function show(Request $request, string $slug)
    {
        $user  = $this->currentUser($request);
        $forum = Forum::where('slug', $slug)->firstOrFail();

        if ($forum->isGlobal()) {
            $posts = Post::with(['user', 'subforum'])
                ->where('forum_id', $forum->id)
                ->latest('last_reply_at')
                ->paginate(20);

            return view('forum.global', compact('user', 'forum', 'posts'));
        }

        $subforums = $forum->subforums()
            ->with('creator')
            ->withCount('posts')
            ->latest()
            ->paginate(20);

        return view('forum.niche', compact('user', 'forum', 'subforums'));
    }
}
