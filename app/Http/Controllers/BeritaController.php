<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class BeritaController extends Controller
{
    /**
     * Ambil daftar postingan berita dengan cache (Default: 10 menit / 600 detik)
     */
    public static function getCachedPosts(bool $forceRefresh = false, int $limit = 20): array
    {
        $cacheKey = 'berita_rsmd_posts';

        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, 600, function () use ($limit) {
            try {
                $response = Http::withoutVerifying()
                    ->timeout(10)
                    ->withHeaders([
                        'Accept' => 'application/json',
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0 Safari/537.36',
                    ])
                    ->get('https://ppid.rsmdsr.id/wp-json/wp/v2/posts', [
                        'categories' => 3,
                        '_embed' => 1,
                        'per_page' => $limit,
                    ]);

                if (!$response->successful()) {
                    return [];
                }

                $rawPosts = $response->json();

                if (!is_array($rawPosts)) {
                    return [];
                }

                return collect($rawPosts)->map(function ($post) {
                    $imageUrl = $post['_embedded']['wp:featuredmedia'][0]['source_url'] ?? null;

                    $formattedDate = isset($post['date'])
                        ? date('d M Y', strtotime($post['date']))
                        : '-';

                    $cleanExcerpt = isset($post['excerpt']['rendered'])
                        ? trim(strip_tags($post['excerpt']['rendered']))
                        : '';

                    return [
                        'id'        => $post['id'],
                        'title'     => html_entity_decode($post['title']['rendered'] ?? '', ENT_QUOTES, 'UTF-8'),
                        'slug'      => $post['slug'] ?? '',
                        'link'      => $post['link'] ?? '#',
                        'date'      => $formattedDate,
                        'excerpt'   => $cleanExcerpt,
                        'content'   => $post['content']['rendered'] ?? '',
                        'thumbnail' => $imageUrl,
                        'author'    => $post['_embedded']['author'][0]['name'] ?? 'Humas RSMD',
                    ];
                })->toArray();
            } catch (\Throwable $e) {
                return [];
            }
        });
    }

    public function index(Request $request)
    {
        // Mendukung refresh paksa jika parameter ?refresh=1 dikirim
        $forceRefresh = $request->boolean('refresh');
        $posts = self::getCachedPosts($forceRefresh);

        return Inertia::render('BeritaView', [
            'posts' => $posts,
        ]);
    }
}
