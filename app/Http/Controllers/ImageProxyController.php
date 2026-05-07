<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ImageProxyController extends Controller
{
    private const ALLOWED_HOSTS    = ['drive.google.com', 'lh3.googleusercontent.com', 'i.imgur.com', 'imgur.com'];
    private const CACHE_SERVER_MIN = 360;    // menit — cache Laravel
    private const CACHE_BROWSER_S  = 3600;  // detik — Cache-Control header
    private const MAX_BYTES        = 10 * 1024 * 1024;

    public function show(Request $request)
    {
        $url = $request->query('url');

        if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
            abort(400);
        }

        $host = strtolower(parse_url($url, PHP_URL_HOST) ?? '');

        $allowed = collect(self::ALLOWED_HOSTS)->contains(
            fn($h) => $host === $h || str_ends_with($host, ".{$h}")
        );

        if (!$allowed) {
            abort(403);
        }

        $cacheKey = 'imgp_' . md5($url);
        $cached   = Cache::get($cacheKey);

        if ($cached) {
            return response($cached['body'])
                ->header('Content-Type', $cached['type'])
                ->header('Cache-Control', 'public, max-age=' . self::CACHE_BROWSER_S . ', immutable')
                ->header('X-Cache', 'HIT');
        }

        try {
            $response = Http::timeout(12)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (compatible; Koar/1.0)',
                    'Referer'    => 'https://drive.google.com/',
                ])
                ->get($url);

            if (!$response->successful()) {
                abort(502);
            }

            $contentType = $response->header('Content-Type') ?? '';

            if (!str_starts_with($contentType, 'image/')) {
                abort(415);
            }

            $body = $response->body();

            if (\strlen($body) > self::MAX_BYTES) {
                abort(413);
            }

            Cache::put($cacheKey, ['body' => $body, 'type' => $contentType], now()->addMinutes(self::CACHE_SERVER_MIN));

            return response($body)
                ->header('Content-Type', $contentType)
                ->header('Cache-Control', 'public, max-age=' . self::CACHE_BROWSER_S . ', immutable')
                ->header('X-Cache', 'MISS');

        } catch (\Exception) {
            abort(502);
        }
    }
}
