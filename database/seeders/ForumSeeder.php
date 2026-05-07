<?php

namespace Database\Seeders;

use App\Models\Forum;
use Illuminate\Database\Seeder;

class ForumSeeder extends Seeder
{
    public function run(): void
    {
        $forums = [
            // Global forums
            [
                'name'        => 'Umum',
                'slug'        => 'umum',
                'description' => 'Obrolan umum, bebas topik',
                'type'        => 'global',
                'icon'        => '🌐',
                'order'       => 1,
            ],
            [
                'name'        => 'Random',
                'slug'        => 'random',
                'description' => 'Apapun yang ada di pikiranmu',
                'type'        => 'global',
                'icon'        => '🎲',
                'order'       => 2,
            ],

            // Niche forums
            [
                'name'        => 'Teknologi',
                'slug'        => 'teknologi',
                'description' => 'Diskusi seputar teknologi, programming, dan gadget',
                'type'        => 'niche',
                'icon'        => '💻',
                'order'       => 3,
            ],
            [
                'name'        => 'Hiburan',
                'slug'        => 'hiburan',
                'description' => 'Film, musik, game, dan konten hiburan lainnya',
                'type'        => 'niche',
                'icon'        => '🎬',
                'order'       => 4,
            ],
            [
                'name'        => 'Sains & Pendidikan',
                'slug'        => 'sains-pendidikan',
                'description' => 'Ilmu pengetahuan, riset, dan diskusi edukasi',
                'type'        => 'niche',
                'icon'        => '🔬',
                'order'       => 5,
            ],
            [
                'name'        => 'Olahraga',
                'slug'        => 'olahraga',
                'description' => 'Sepakbola, basket, dan semua olahraga',
                'type'        => 'niche',
                'icon'        => '⚽',
                'order'       => 6,
            ],
            [
                'name'        => 'Kreativitas',
                'slug'        => 'kreativitas',
                'description' => 'Seni, desain, menulis, dan karya kreatif',
                'type'        => 'niche',
                'icon'        => '🎨',
                'order'       => 7,
            ],
        ];

        foreach ($forums as $data) {
            Forum::firstOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
