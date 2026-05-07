<?php

namespace App\Services;

use Hashids\Hashids;
use InvalidArgumentException;

/**
 * Obfuscates numeric IDs in URLs using Hashids.
 *
 * Each entity type uses a different salt so IDs are not cross-guessable:
 *   post  → /post/k3Zx9m
 *   chat  → /chat/Wq7Lnp
 *
 * Min length = 8 keeps short URLs while making sequential IDs unguessable.
 */
class Hashid
{
    private static array $instances = [];

    private static function make(string $type): Hashids
    {
        if (!isset(self::$instances[$type])) {
            $base    = config('app.key');          // APP_KEY as base salt
            $salt    = hash('sha256', $base . ':' . $type);
            $minLen  = 8;
            $alpha   = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

            self::$instances[$type] = new Hashids($salt, $minLen, $alpha);
        }

        return self::$instances[$type];
    }

    public static function encode(string $type, int $id): string
    {
        return self::make($type)->encode($id);
    }

    public static function decode(string $type, string $hash): int
    {
        $decoded = self::make($type)->decode($hash);

        if (empty($decoded)) {
            abort(404);
        }

        return (int) $decoded[0];
    }
}
