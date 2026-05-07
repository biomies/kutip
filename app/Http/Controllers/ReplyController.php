<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Reply;
use App\Services\Hashid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReplyController extends Controller
{
    public function store(Request $request, string $postHash)
    {
        $user   = $this->currentUser($request);
        $postId = Hashid::decode('post', $postHash);

        $validated = $request->validate([
            'content'   => 'required|string|min:1|max:2000',
            'parent_id' => 'nullable|exists:replies,id',
        ]);

        DB::transaction(function () use ($validated, $postId, $user) {
            $post  = Post::lockForUpdate()->findOrFail($postId);
            $depth = 0;

            if (!empty($validated['parent_id'])) {
                $parent = Reply::lockForUpdate()->findOrFail($validated['parent_id']);

                if ($parent->post_id !== $post->id) {
                    abort(422, 'Reply parent tidak valid.');
                }

                $depth = min($parent->depth + 1, 5);
                $parent->incrementReplyCount();
            }

            Reply::create([
                'post_id'   => $post->id,
                'user_id'   => $user->id,
                'parent_id' => $validated['parent_id'] ?? null,
                'depth'     => $depth,
                'content'   => $validated['content'],
            ]);

            $post->incrementReplyCount();
        });

        return redirect()->route('post.show', $postHash)->withFragment('replies');
    }

    public function update(Request $request, string $hash)
    {
        $user  = $this->currentUser($request);
        $id    = Hashid::decode('reply', $hash);
        $reply = Reply::findOrFail($id);

        if ($reply->user_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'content' => 'required|string|min:1|max:2000',
        ]);

        $reply->update(['content' => $validated['content']]);

        return redirect()
            ->route('post.show', hid('post', $reply->post_id))
            ->withFragment('replies')
            ->with('success', 'Balasan berhasil diperbarui.');
    }

    public function destroy(Request $request, string $hash)
    {
        $user  = $this->currentUser($request);
        $id    = Hashid::decode('reply', $hash);
        $reply = Reply::findOrFail($id);

        if ($reply->user_id !== $user->id) {
            abort(403);
        }

        $postId = $reply->post_id;
        $reply->delete();

        return redirect()
            ->route('post.show', hid('post', $postId))
            ->withFragment('replies');
    }
}
