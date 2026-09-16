<?php

namespace App\Http\Requests\Pengembalian;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePengembalianRequest extends FormRequest
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
            'kondisi_kembali' => ['requuired', 'string', 'max:255'],
            'denda' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return [
            'kondisi_kembali' => 'Kondisi barang kembali',
            'denda' => 'Nilai denda',
        ];
    }
}
 