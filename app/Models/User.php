<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'browser_token',
        'username',
        'user_number',
        'last_active_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'last_active_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Reply::class);
    }

    public function subforums(): HasMany
    {
        return $this->hasMany(Subforum::class);
    }

    public function chatsAsUserOne(): HasMany
    {
        return $this->hasMany(Chat::class, 'user_one_id');
    }

    public function chatsAsUserTwo(): HasMany
    {
        return $this->hasMany(Chat::class, 'user_two_id');
    }

    public function chatMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function markActive(): void
    {
        $this->update([
            'last_active_at' => now(),
            'is_active' => true,
        ]);
    }

    public static function nextUserNumber(): int
    {
        return (static::max('user_number') ?? 0) + 1;
    }
}
