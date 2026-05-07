<?php

namespace App\Models;

use App\Models\Concerns\HasEmbeds;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reply extends Model
{
    use HasEmbeds;

    protected $fillable = ['post_id', 'user_id', 'parent_id', 'depth', 'content', 'reply_count'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function authorName(): string
    {
        return $this->user?->username ?? '[deleted]';
    }

    public function authorDeleted(): bool
    {
        return is_null($this->user_id) || $this->user?->trashed();
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Reply::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Reply::class, 'parent_id')->orderBy('created_at');
    }

    public function incrementReplyCount(): void
    {
        $this->increment('reply_count');
    }
}
