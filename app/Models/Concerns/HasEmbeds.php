<?php

namespace App\Models\Concerns;

trait HasEmbeds
{
    /**
     * Ekstrak semua URL http/https dari konten, maks $limit URL.
     */
    public function extractUrls(int $limit = 4): array
    {
        // Batasi panjang konten sebelum regex — cegah ReDoS pada input sangat panjang
        $content = \mb_substr($this->content, 0, 5000);

        // Regex sederhana tanpa lookbehind kompleks — lebih aman dari catastrophic backtracking
        // Karakter set [^\s<>"'] sudah cukup untuk URL praktis
        preg_match_all('/\bhttps?:\/\/[^\s<>"\']{1,2000}/i', $content, $matches);

        $urls = \array_unique($matches[0]);
        $urls = \array_map(fn($u) => \rtrim($u, '.,;:!?)>'), $urls);

        return \array_slice(\array_values($urls), 0, $limit);
    }

    /**
     * Konversi URL dari platform tertentu ke URL yang bisa di-embed.
     */
    public static function resolveImageUrl(string $url): string
    {
        // --- Google Drive ---
        if (str_contains($url, 'drive.google.com')) {
            $fileId = null;

            if (preg_match('/\/file\/d\/([a-zA-Z0-9_-]+)/i', $url, $m)) {
                $fileId = $m[1];
            } elseif (preg_match('/[?&]id=([a-zA-Z0-9_-]+)/i', $url, $m)) {
                $fileId = $m[1];
            }

            if ($fileId) {
                $driveUrl = 'https://drive.google.com/uc?export=view&id=' . $fileId;
                return route('img.proxy', ['url' => $driveUrl]);
            }
        }

        // --- lh3.googleusercontent.com ---
        if (str_contains($url, 'lh3.googleusercontent.com')) {
            return route('img.proxy', ['url' => $url]);
        }

        // --- Imgur: imgur.com/ID → i.imgur.com/ID.jpg ---
        if (preg_match('/^https?:\/\/(?:www\.)?imgur\.com\/([a-zA-Z0-9]+)$/i', $url, $m)) {
            return 'https://i.imgur.com/' . $m[1] . '.jpg';
        }

        return $url;
    }

    /**
     * Render konten: setiap URL di-wrap <span data-embed-url="...">
     */
    public function renderContent(): string
    {
        // Escape seluruh konten — ini jadi landasan aman sebelum wrap URL
        $escaped = e($this->content);

        return preg_replace_callback(
            '/https?:\/\/[^\s\]\)\>"\'&]+(?:&amp;[^\s\]\)\>"\']*)?/i',
            function ($m) {
                $rawUrl = html_entity_decode($m[0], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $rawUrl = rtrim($rawUrl, '.,;:!?)>');

                // Pastikan URL hanya http/https — blokir javascript:, data:, dll
                $scheme = strtolower(parse_url($rawUrl, PHP_URL_SCHEME) ?? '');
                if (!\in_array($scheme, ['http', 'https'], true)) {
                    // Kembalikan sebagai teks biasa yang sudah di-escape
                    return htmlspecialchars($rawUrl, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                }

                $attrUrl    = htmlspecialchars($rawUrl, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $displayUrl = htmlspecialchars($rawUrl, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                return '<span class="content-url" data-embed-url="' . $attrUrl . '">' . $displayUrl . '</span>';
            },
            $escaped
        );
    }
}
