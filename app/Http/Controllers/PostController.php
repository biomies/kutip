<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use App\Models\Post;
use App\Models\Reply;
use App\Models\Subforum;
use App\Services\Hashid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    public function show(Request $request, string $hash)
    {
        $user = $this->currentUser($request);
        $id   = Hashid::decode('post', $hash);
        $post = Post::with(['user', 'forum', 'subforum'])->findOrFail($id);

        $replies = Reply::with(['user', 'children.user', 'children.children.user'])
            ->where('post_id', $post->id)
            ->whereNull('parent_id')
            ->orderBy('created_at')
            ->get();

        return view('post.show', compact('user', 'post', 'replies'));
    }

    public function store(Request $request)
    {
        $user = $this->currentUser($request);

        $validated = $request->validate([
            'content'     => 'required|string|min:1|max:5000',
            'forum_id'    => 'required|exists:forums,id',
            'subforum_id' => 'nullable|exists:subforums,id',
        ]);

        $forum = Forum::findOrFail($validated['forum_id']);

        if ($forum->isNiche() && empty($validated['subforum_id'])) {
            return back()->withErrors(['subforum_id' => 'Pilih subforum untuk forum niche.']);
        }

        $post = DB::transaction(function () use ($validated, $forum, $user) {
            if (!empty($validated['subforum_id'])) {
                $subforum = Subforum::lockForUpdate()->findOrFail($validated['subforum_id']);
                if ($subforum->forum_id !== $forum->id) {
                    abort(422, 'Subforum tidak valid.');
                }
                $subforum->incrementPostCount();
            }

            return Post::create([
                'user_id'       => $user->id,
                'forum_id'      => $validated['forum_id'],
                'subforum_id'   => $validated['subforum_id'] ?? null,
                'content'       => $validated['content'],
                'last_reply_at' => now(),
            ]);
        });

        return redirect()->route('post.show', hid('post', $post->id));
    }

    public function update(Request $request, string $hash)
    {
        $user = $this->currentUser($request);
        $id   = Hashid::decode('post', $hash);
        $post = Post::findOrFail($id);

        if ($post->user_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'content' => 'required|string|min:1|max:5000',
        ]);

        $post->update(['content' => $validated['content']]);

        return redirect()->route('post.show', hid('post', $post->id))
            ->with('success', 'Post berhasil diperbarui.');
    }

    public function destroy(Request $request, string $hash)
    {
        $user = $this->currentUser($request);
        $id   = Hashid::decode('post', $hash);
        $post = Post::findOrFail($id);

        if ($post->user_id !== $user->id) {
            abort(403);
        }

        $forumSlug    = $post->forum->slug;
        $subforumSlug = $post->subforum?->slug;

        $post->delete();

        if ($subforumSlug) {
            return redirect()->route('subforum.show', [$forumSlug, $subforumSlug]);
        }

        return redirect()->route('forum.show', $forumSlug);
    }
}
