<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chat extends Model
{
    protected $fillable = ['user_one_id', 'user_two_id', 'last_message_at'];

    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
        ];
    }

    public function userOne(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_one_id')->withTrashed();
    }

    public function userTwo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_two_id')->withTrashed();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class)->orderBy('created_at');
    }

    public function latestMessage(): HasMany
    {
        return $this->hasMany(ChatMessage::class)->latest()->limit(1);
    }

    public function getOtherUser(User $currentUser): ?User
    {
        return $this->user_one_id === $currentUser->id ? $this->userTwo : $this->userOne;
    }

    public static function findOrCreateBetween(int $userAId, int $userBId): self
    {
        // Always store smaller id as user_one for consistent uniqueness
        [$one, $two] = $userAId < $userBId ? [$userAId, $userBId] : [$userBId, $userAId];

        return static::firstOrCreate(
            ['user_one_id' => $one, 'user_two_id' => $two],
        );
    }
}
