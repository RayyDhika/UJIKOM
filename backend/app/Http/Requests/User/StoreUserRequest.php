<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
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
        $user = $this->route('user');
        //memastikan kita mendapatkan id (mengantisipasi jika parameter route berupa objek)
        $userId = $user instanceof \App\Models\User ? $user->id : $user;

        return [
            'name' => ['requird', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255',
            //mengabaikan id user yang sedang di update agar tidak memicu error email sudah terdaftar
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password' => ['nullable', 'string',
                Password::min(8)->letters()numbers()
            ],
            'role' => ['required', 
                Rule::in(['admin', 'petugas', 'peminjam']) //input hanya boleh dari opsi ini
            ],
            'no_hp' => ['nullable', 'string', 'max:15'],
            'alamat' => ['nullable', 'string'],
            'foto_profile' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ];
    }
}
