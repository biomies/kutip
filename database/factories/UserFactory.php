<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<User> */
class UserFactory extends Factory
{
    public function definition(): array
    {
        $n = fake()->unique()->numberBetween(1, 99999);
        return [
            'uuid'           => (string) Str::uuid(),
            'browser_token'  => Str::random(64),
            'username'       => 'user-' . $n,
            'user_number'    => $n,
            'last_active_at' => now(),
            'is_active'      => true,
        ];
    }
}
