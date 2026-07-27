<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKaryawanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // BR-01: Hanya Admin yang dapat update data Karyawan
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
            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
                Rule::unique('karyawans', 'user_id')->ignore($this->route('karyawan')),
            ],
            'jabatan_id' => [
                'required',
                'integer',
                'exists:jabatans,id',
            ],
            'nik' => [
                'required',
                'string',
                'max:20',
                Rule::unique('karyawans', 'nik')->ignore($this->route('karyawan')),
            ],
            'nama_lengkap' => [
                'required',
                'string',
                'max:100',
            ],
            'alamat' => [
                'nullable',
                'string',
            ],
            'no_telepon' => [
                'nullable',
                'string',
                'max:20',
            ],
            'tanggal_masuk' => [
                'required',
                'date',
                'before_or_equal:today',
            ],
            'no_rekening' => [
                'nullable',
                'string',
                'max:30',
            ],
            'status_karyawan' => [
                'required',
                'in:tetap,kontrak,magang',
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
            'user_id' => 'User',
            'jabatan_id' => 'Jabatan',
            'nik' => 'NIK',
            'nama_lengkap' => 'Nama Lengkap',
            'alamat' => 'Alamat',
            'no_telepon' => 'No. Telepon',
            'tanggal_masuk' => 'Tanggal Masuk',
            'no_rekening' => 'No. Rekening',
            'status_karyawan' => 'Status Karyawan',
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
            'user_id.required' => ':attribute wajib dipilih.',
            'user_id.exists' => ':attribute tidak valid.',
            'user_id.unique' => ':attribute sudah terhubung dengan karyawan lain.',
            'jabatan_id.required' => ':attribute wajib dipilih.',
            'jabatan_id.exists' => ':attribute tidak valid.',
            'nik.required' => ':attribute wajib diisi.',
            'nik.unique' => ':attribute sudah terdaftar.',
            'nik.max' => ':attribute maksimal :max karakter.',
            'nama_lengkap.required' => ':attribute wajib diisi.',
            'nama_lengkap.max' => ':attribute maksimal :max karakter.',
            'no_telepon.max' => ':attribute maksimal :max karakter.',
            'tanggal_masuk.required' => ':attribute wajib diisi.',
            'tanggal_masuk.date' => ':attribute harus berupa tanggal yang valid.',
            'tanggal_masuk.before_or_equal' => ':attribute tidak boleh lebih dari hari ini.',
            'no_rekening.max' => ':attribute maksimal :max karakter.',
            'status_karyawan.required' => ':attribute wajib dipilih.',
            'status_karyawan.in' => ':attribute harus Tetap, Kontrak, atau Magang.',
        ];
    }
}
