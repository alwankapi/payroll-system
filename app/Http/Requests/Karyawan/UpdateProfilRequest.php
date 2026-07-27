<?php

namespace App\Http\Requests\Karyawan;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfilRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'karyawan';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'no_telepon' => ['required', 'string', 'max:20'],
            'alamat' => ['required', 'string', 'max:500'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'], // max 2MB
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'no_telepon' => 'Nomor Telepon',
            'alamat' => 'Alamat',
            'foto' => 'Foto Profil',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'no_telepon.required' => ':attribute wajib diisi.',
            'no_telepon.max' => ':attribute maksimal :max karakter.',
            'alamat.required' => ':attribute wajib diisi.',
            'alamat.max' => ':attribute maksimal :max karakter.',
            'foto.image' => ':attribute harus berupa gambar.',
            'foto.mimes' => ':attribute harus berformat jpeg, png, atau jpg.',
            'foto.max' => ':attribute maksimal :max KB.',
        ];
    }
}
