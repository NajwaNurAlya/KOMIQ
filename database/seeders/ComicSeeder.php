<?php

namespace Database\Seeders;

use App\Models\Comic;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ComicSeeder extends Seeder
{
    public function run(): void
    {
        $kreator = User::where('email', 'kreator@komiq.com')->firstOrFail();
        $admin = User::where('email', 'admin@komiq.com')->firstOrFail();
        $data = json_decode(File::get(database_path('seeders/data/mangadex.json')), true);

        foreach ($data as $i => $item) {
            $cover = null;
            if ($item['cover']) {
                $cover = 'covers/' . $item['cover'];
                Storage::disk('public')->put($cover, File::get(database_path('seeders/data/covers/' . $item['cover'])));
            }

            $komik = Comic::updateOrCreate(['slug' => $item['slug']], [
                'user_id' => $kreator->id,
                'title' => $item['title'],
                'synopsis' => $item['synopsis'],
                'cover_image' => $cover,
                'status' => $item['status'],
            ]);

            $komik->genres()->sync(array_map(
                fn ($nama) => Genre::firstOrCreate(['slug' => Str::slug($nama)], ['name' => $nama])->id,
                $item['genres']
            ));

            for ($no = 1; $no <= 6; $no++) {
                $terbit = now()->subDays($i + (6 - $no) * 7);

                $halaman = Storage::disk('public')->files("chapters/{$item['slug']}/$no");
                sort($halaman);

                $komik->chapters()->updateOrCreate(['chapter_number' => $no], [
                    'title' => $item['chapters'][$no - 1]['title'] ?? "Chapter $no",
                    'pages' => $halaman,
                    'is_premium' => $no >= 5,
                    'coin_price' => $no >= 5 ? 15 : 0,
                    'status' => 'approved',
                    'reviewed_by' => $admin->id,
                    'reviewed_at' => $terbit,
                    'published_at' => $terbit,
                ]);
            }
        }
    }
}
