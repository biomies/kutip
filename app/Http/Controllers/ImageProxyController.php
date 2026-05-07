<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ImageProxyController extends Controller
{
    private const ALLOWED_HOSTS = [
        'drive.google.com',
        'drive.usercontent.google.com',  // Redirect target dari Drive uc?export=view
        'lh3.googleusercontent.com',
        'i.imgur.com',
        'imgur.com',
    ];

    private const ALLOWED_SCHEMES  = ['https'];
    private const CACHE_SERVER_MIN  = 360;
    private const CACHE_BROWSER_S   = 3600;
    private const MAX_BYTES         = 10 * 1024 * 1024;
    private const MAX_REDIRECTS     = 3;

    private const ALLOWED_TYPES = [
        'image/jpeg', 'image/png', 'image/gif',
        'image/webp', 'image/svg+xml', 'image/avif',
    ];

    public function show(Request $request)
    {
        $url = $request->query('url');

        if (!$url) {
            abort(400);
        }

        if (!$this->isAllowedUrl($url)) {
            abort(403);
        }

        $cacheKey = 'imgp_' . md5($url);
        $cached   = Cache::get($cacheKey);

        if ($cached) {
            return response($cached['body'])
                ->header('Content-Type', $cached['type'])
                ->header('Cache-Control', 'public, max-age=' . self::CACHE_BROWSER_S . ', immutable')
                ->header('X-Content-Type-Options', 'nosniff')
                ->header('X-Cache', 'HIT');
        }

        try {
            [$body, $contentType] = $this->fetchWithRedirect($url);

            Cache::put($cacheKey, ['body' => $body, 'type' => $contentType], now()->addMinutes(self::CACHE_SERVER_MIN));

            return response($body)
                ->header('Content-Type', $contentType)
                ->header('Cache-Control', 'public, max-age=' . self::CACHE_BROWSER_S . ', immutable')
                ->header('X-Content-Type-Options', 'nosniff')
                ->header('Content-Security-Policy', "default-src 'none'")
                ->header('X-Cache', 'MISS');

        } catch (\Exception) {
            abort(502);
        }
    }

    /**
     * Fetch URL, follow redirect manual hanya ke host yang di-whitelist.
     * Return [body, contentType].
     */
    private function fetchWithRedirect(string $url, int $depth = 0): array
    {
        if ($depth >= self::MAX_REDIRECTS) {
            abort(502);
        }

        $response = Http::timeout(15)
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Referer'    => 'https://drive.google.com/',
                'Accept'     => 'image/webp,image/apng,image/*,*/*;q=0.8',
            ])
            ->withoutRedirecting()
            ->get($url);

        // Ikuti redirect hanya kalau tujuannya di-whitelist
        if (\in_array($response->status(), [301, 302, 303, 307, 308], true)) {
            $location = $response->header('Location') ?? '';

            if (!$location) {
                abort(502);
            }

            // Handle relative redirect
            if (str_starts_with($location, '/')) {
                $parts    = parse_url($url);
                $location = $parts['scheme'] . '://' . $parts['host'] . $location;
            }

            if (!$this->isAllowedUrl($location)) {
                abort(403);
            }

            return $this->fetchWithRedirect($location, $depth + 1);
        }

        if (!$response->successful()) {
            abort(502);
        }

        $rawType     = $response->header('Content-Type') ?? '';
        $contentType = strtolower(trim(explode(';', $rawType)[0]));

        if (!\in_array($contentType, self::ALLOWED_TYPES, true)) {
            abort(415);
        }

        $body = $response->body();

        if (\strlen($body) > self::MAX_BYTES) {
            abort(413);
        }

        return [$body, $contentType];
    }

    /**
     * Validasi URL: scheme https, host di whitelist, bukan IP/localhost.
     */
    private function isAllowedUrl(string $url): bool
    {
        $parsed = parse_url($url);

        if (!isset($parsed['scheme'], $parsed['host'])) {
            return false;
        }

        if (!in_array(strtolower($parsed['scheme']), self::ALLOWED_SCHEMES, true)) {
            return false;
        }

        $host = strtolower($parsed['host']);

        if ($host === '') {
            return false;
        }

        // Blokir IP address dan localhost
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return false;
        }

        if (\in_array($host, ['localhost', '127.0.0.1', '::1', '0.0.0.0'], true)) {
            return false;
        }

        // Exact match whitelist
        return \in_array($host, self::ALLOWED_HOSTS, true);
    }
}
