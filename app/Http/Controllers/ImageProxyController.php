<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ImageProxyController extends Controller
{
    // Exact domain whitelist — hanya domain ini yang boleh, tidak ada subdomain trick
    private const ALLOWED_HOSTS = [
        'drive.google.com',
        'lh3.googleusercontent.com',
        'i.imgur.com',
        'imgur.com',
    ];

    // Skema yang diizinkan — blokir file://, ftp://, internal protocol
    private const ALLOWED_SCHEMES = ['https'];

    private const CACHE_SERVER_MIN = 360;
    private const CACHE_BROWSER_S  = 3600;
    private const MAX_BYTES        = 10 * 1024 * 1024;

    // Content-type yang benar-benar gambar
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

        // Validasi URL terstruktur
        $parsed = parse_url($url);

        // Harus punya scheme dan host yang valid
        if (!isset($parsed['scheme'], $parsed['host'])) {
            abort(400);
        }

        // Hanya izinkan HTTPS — blokir HTTP, file://, ftp://, dll
        if (!in_array(strtolower($parsed['scheme']), self::ALLOWED_SCHEMES, true)) {
            abort(403);
        }

        $host = strtolower($parsed['host']);

        // Blokir empty host
        if ($host === '') {
            abort(403);
        }

        // Blokir IP address langsung (cegah SSRF ke internal network)
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            abort(403);
        }

        // Blokir localhost dan internal hostnames
        if (in_array($host, ['localhost', '127.0.0.1', '::1', '0.0.0.0'], true)) {
            abort(403);
        }

        // Exact match — TIDAK pakai str_ends_with agar tidak bisa bypass dengan subdomain
        if (!in_array($host, self::ALLOWED_HOSTS, true)) {
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
            $response = Http::timeout(12)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (compatible; Koar/1.0)',
                    'Referer'    => 'https://drive.google.com/',
                ])
                ->withoutRedirecting()  // Jangan ikuti redirect — cegah open redirect SSRF
                ->get($url);

            // Izinkan redirect hanya ke host yang sama (301/302 ke domain lain = skip)
            if (in_array($response->status(), [301, 302, 303, 307, 308], true)) {
                $location = $response->header('Location') ?? '';
                $locHost  = strtolower(parse_url($location, PHP_URL_HOST) ?? '');
                if (!in_array($locHost, self::ALLOWED_HOSTS, true)) {
                    abort(403);
                }
                // Ikuti redirect yang aman
                $response = Http::timeout(12)
                    ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; Koar/1.0)'])
                    ->get($location);
            }

            if (!$response->successful()) {
                abort(502);
            }

            // Ambil content-type dan strip parameter (misal: "image/jpeg; charset=utf-8")
            $rawType     = $response->header('Content-Type') ?? '';
            $contentType = strtolower(trim(explode(';', $rawType)[0]));

            // Hanya izinkan mime type gambar yang ada di whitelist
            if (!in_array($contentType, self::ALLOWED_TYPES, true)) {
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
                ->header('X-Content-Type-Options', 'nosniff')  // Cegah MIME sniffing
                ->header('Content-Security-Policy', "default-src 'none'") // Gambar saja
                ->header('X-Cache', 'MISS');

        } catch (\Exception) {
            abort(502);
        }
    }
}
