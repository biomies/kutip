<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subforum extends Model
{
    protected $fillable = ['forum_id', 'user_id', 'name', 'slug', 'description', 'post_count'];

    public function forum(): BelongsTo
    {
        return $this->belongsTo(Forum::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function incrementPostCount(): void
    {
        $this->increment('post_count');
    }
}
