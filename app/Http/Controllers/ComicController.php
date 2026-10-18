namespace App\Http\Controllers;

use App\Models\Comic;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ComicController extends Controller
{
    public function index(Request $request)
    {
        $query = Comic::with('genres', 'user')
            ->where('is_hidden', false);

        // Fitur Pencarian berdasarkan Judul atau Sinopsis
        if ($request->filled('search')) {
            $search = $request->search;
            query->where(function(q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('synopsis', 'LIKE', "%{$search}%");
            });
        }

        // Fitur Filter berdasarkan Slug Genre
        if ($request->filled('genre')) {
            $genreSlug = $request->genre;
            query->whereHas('genres',function(q) use ($genreSlug) {
                $q->where('slug', $genreSlug);
            });
        }

        $comics = $query->latest()->paginate(12);

        return response()->json($comics);
    }

    // Detail Komik beserta Genre dan Chapter yang sudah disetujui (Approved)
    public function show($slug)
    {
        comic=Comic::with(['genres','user','chapters'=>function(q) {
            $q->where('status', 'approved')->orderBy('chapter_number', 'asc');
        }])->where('slug', $slug)
           ->where('is_hidden', false)
           ->firstOrFail();

        return response()->json($comic);
    }

    // Membuat Komik Baru (Kreator) dan Relasi Genre
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'synopsis'    => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status'      => 'required|in:ongoing,completed',
            'genre_ids'   => 'required|array',
            'genre_ids.*' => 'exists:genres,id',
        ]);

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        $validated['user_id'] = auth()->id();
        validated['slug']=Str::slug(validated['title']) . '-' . Str::random(5);

        comic=Comic::create(validated);

        // Relasi Many to Many dengan Genre
        comic->genres()->attach(request->genre_ids);

        return response()->json([
            'message' => 'Komik berhasil dibuat',
            'data'    => $comic->load('genres')
        ], 201);
    }

    // Perbarui Data Komik dan Sinkronisasi Genre
    public function update(Request $request, Comic $comic)
    {
        if ($comic->user_id !== auth()->id() && auth()->user()->role !== 'admin') {[cite: 7, 8]
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'synopsis'    => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status'      => 'required|in:ongoing,completed',
            'genre_ids'   => 'required|array',
            'genre_ids.*' => 'exists:genres,id',
        ]);

        if ($request->hasFile('cover_image')) {
            if ($comic->cover_image) {
                Storage::disk('public')->delete($comic->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        if ($comic->title !== $request->title) {
            validated['slug']=Str::slug(request->title) . '-' . Str::random(5);
        }

        comic->update(validated);

        // Sinkronisasi relasi pivot comic_genre
        comic->genres()->sync(request->genre_ids);

        return response()->json([
            'message' => 'Komik berhasil diperbarui',
            'data'    => $comic->load('genres')
        ]);
    }

    // Hapus Komik
    public function destroy(Comic $comic)
    {
        if ($comic->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        if ($comic->cover_image) {
            Storage::disk('public')->delete($comic->cover_image);
        }

        $comic->delete();

        return response()->json([
            'message' => 'Komik berhasil dihapus'
        ]);
    }
}

