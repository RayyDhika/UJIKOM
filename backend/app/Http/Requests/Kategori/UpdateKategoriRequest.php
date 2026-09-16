<?php

namespace App\Http\Requests\Kategori;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateKategoriRequest extends FormRequest
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
        $kategori = $this->route('kategori');
        return [
            'nama_kategori' => [
                'required',
                'string',
                'max:255',

                Rule::unique('kategori', 'nama_kategori')->ignore($kategori),
            ],
        ];
    }
}
