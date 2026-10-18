namespace App\Http\Controllers;

use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GenreController extends Controller
{
    public function index()
    {
        $genres = Genre::latest()->get();
        return response()->json($genres);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:genres,name',
        ]);

        validated['slug']=Str::slug(validated['name']);

        genre=Genre::create(validated);

        return response()->json([
            'message' => 'Genre berhasil ditambahkan',
            'data' => $genre
        ], 201);
    }

    public function update(Request $request, Genre $genre)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:genres,name,' . $genre->id,
        ]);

        validated['slug']=Str::slug(validated['name']);

        genre->update(validated);

        return response()->json([
            'message' => 'Genre berhasil diperbarui',
            'data' => $genre
        ]);
    }

    public function destroy(Genre $genre)
    {
        $genre->delete();

        return response()->json([
            'message' => 'Genre berhasil dihapus'
        ]);
    }
}
