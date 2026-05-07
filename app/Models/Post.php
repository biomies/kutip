<?php

namespace App\Models;

use App\Models\Concerns\HasEmbeds;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasEmbeds;

    protected $fillable = [
        'user_id',
        'forum_id',
        'subforum_id',
        'content',
        'reply_count',
        'last_reply_at',
    ];

    protected function casts(): array
    {
        return [
            'last_reply_at' => 'datetime',
        ];
    }

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

    public function forum(): BelongsTo
    {
        return $this->belongsTo(Forum::class);
    }

    public function subforum(): BelongsTo
    {
        return $this->belongsTo(Subforum::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Reply::class)->whereNull('parent_id')->orderBy('created_at');
    }

    public function allReplies(): HasMany
    {
        return $this->hasMany(Reply::class)->orderBy('created_at');
    }

    public function incrementReplyCount(): void
    {
        $this->update([
            'reply_count' => $this->reply_count + 1,
            'last_reply_at' => now(),
        ]);
    }
}
