<?php

namespace App\Http\Requests\Alat;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAlatRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'kategori_id' => ['required', 'integer', Rule::exists('kategori', 'id')],
            'nama_alat' => ['required', 'string', 'max:255'],
            'stok' => ['required', 'integer', 'min:0'],
            'status_kondisi' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'gambar' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'kategori_id.exists' => 'Kategori yang dipilih tidak valid atau tidak terdaftar.',
            'stok.min' => 'Stok tidak boleh kurang dari 0.',
            'gambar.max' => 'Ukuran gambar maksimal adalah 2 MB.',
            'gambar.image' => 'File yang diunggah harus berupa gambar.',
        ];
    }
}
