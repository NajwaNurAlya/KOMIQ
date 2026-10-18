<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreChapterRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diotorisasi untuk membuat request ini.
     */
    public function authorize(): bool
    {
        return true; // Otorisasi detail ditangani di Controller/Policy
    }

    /**
     * Aturan validasi yang berlaku untuk request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $comicId = $this->route('comic')?->id ?? $this->route('comic') ?? $this->input('comic_id');

        return [
            'comic_id' => ['required', 'integer'],
            'chapter_number' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('chapters', 'chapter_number')->where(function ($query) use ($comicId) {
                    return $query->where('comic_id', $comicId);
                }),
            ],
            'title' => ['required', 'string', 'max:255'],
            'pages' => ['required', 'array', 'min:1'],
            'pages.*' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'], // Max 5MB per halaman
            'is_premium' => ['nullable', 'boolean'],
            'coin_price' => [
                'nullable',
                'integer',
                'min:0',
                Rule::requiredIf(fn () => (bool) $this->boolean('is_premium')),
            ],
        ];
    }

    /**
     * Pesan kesalahan khusus untuk validasi.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'comic_id.required' => 'Komik harus ditentukan.',
            'chapter_number.required' => 'Nomor chapter wajib diisi.',
            'chapter_number.integer' => 'Nomor chapter harus berupa angka.',
            'chapter_number.min' => 'Nomor chapter minimal adalah 1.',
            'chapter_number.unique' => 'Nomor chapter ini sudah ada untuk komik yang dipilih.',
            'title.required' => 'Judul chapter wajib diisi.',
            'title.max' => 'Judul chapter maksimal 255 karakter.',
            'pages.required' => 'Halaman komik wajib diunggah minimal 1 halaman.',
            'pages.min' => 'Minimal unggah 1 halaman gambar.',
            'pages.*.image' => 'Setiap berkas halaman harus berupa gambar.',
            'pages.*.mimes' => 'Format gambar yang didukung adalah JPEG, PNG, JPG, atau WEBP.',
            'pages.*.max' => 'Ukuran setiap gambar maksimal 5 MB.',
            'coin_price.required_if' => 'Harga koin wajib diisi jika chapter disetel sebagai premium.',
            'coin_price.min' => 'Harga koin tidak boleh negatif.',
        ];
    }

    /**
     * Persiapkan data sebelum divalidasi.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('is_premium')) {
            $this->merge([
                'is_premium' => filter_var($this->input('is_premium'), FILTER_VALIDATE_BOOLEAN),
            ]);
        } else {
            $this->merge(['is_premium' => false]);
        }

        if (! $this->boolean('is_premium') || empty($this->input('coin_price'))) {
            $this->merge(['coin_price' => 0]);
        }

        if ($this->route('comic')) {
            $comic = $this->route('comic');
            $this->merge([
                'comic_id' => is_object($comic) ? $comic->id : $comic,
            ]);
        }
    }
}
