<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Forum extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'type', 'icon', 'order'];

    public function subforums(): HasMany
    {
        return $this->hasMany(Subforum::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function isGlobal(): bool
    {
        return $this->type === 'global';
    }

    public function isNiche(): bool
    {
        return $this->type === 'niche';
    }
}
