<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewChapterRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diotorisasi untuk membuat request ini.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi yang berlaku untuk request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'action' => ['required', 'in:approve,reject'],
            'review_note' => [
                'nullable',
                'string',
                'max:2000',
                'required_if:action,reject',
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
            'action.required' => 'Keputusan review (setujui atau tolak) wajib dipilih.',
            'action.in' => 'Keputusan review tidak valid.',
            'review_note.required_if' => 'Catatan review (review note) wajib diisi jika chapter ditolak.',
            'review_note.max' => 'Catatan review maksimal 2000 karakter.',
        ];
    }
}
