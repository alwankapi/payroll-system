<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateJabatanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // BR-01: Hanya Admin yang dapat update data Jabatan
        return $this->user() && $this->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama_jabatan' => [
                'required',
                'string',
                'max:100',
                Rule::unique('jabatans', 'nama_jabatan')->ignore($this->route('jabatan')),
            ],
            'gaji_pokok' => [
                'required',
                'numeric',
                'min:0',
                'max:999999999999.99',
            ],
            'tunjangan_jabatan' => [
                'required',
                'numeric',
                'min:0',
                'max:999999999999.99',
            ],
            'keterangan' => [
                'nullable',
                'string',
            ],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nama_jabatan' => 'Nama Jabatan',
            'gaji_pokok' => 'Gaji Pokok',
            'tunjangan_jabatan' => 'Tunjangan Jabatan',
            'keterangan' => 'Keterangan',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama_jabatan.required' => ':attribute wajib diisi.',
            'nama_jabatan.unique' => ':attribute sudah terdaftar.',
            'nama_jabatan.max' => ':attribute maksimal :max karakter.',
            'gaji_pokok.required' => ':attribute wajib diisi.',
            'gaji_pokok.numeric' => ':attribute harus berupa angka.',
            'gaji_pokok.min' => ':attribute minimal :min.',
            'tunjangan_jabatan.required' => ':attribute wajib diisi.',
            'tunjangan_jabatan.numeric' => ':attribute harus berupa angka.',
            'tunjangan_jabatan.min' => ':attribute minimal :min.',
        ];
    }
}
