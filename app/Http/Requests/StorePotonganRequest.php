<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePotonganRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // BR-01: Hanya Admin yang dapat create data Potongan
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
            'nama_potongan' => [
                'required',
                'string',
                'max:100',
            ],
            'jenis_potongan' => [
                'required',
                'in:persentase,nominal',
            ],
            'nilai' => [
                'required',
                'numeric',
                'min:0',
                'max:999999999999.99',
            ],
            'status_aktif' => [
                'required',
                'boolean',
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
            'nama_potongan' => 'Nama Potongan',
            'jenis_potongan' => 'Jenis Potongan',
            'nilai' => 'Nilai',
            'status_aktif' => 'Status Aktif',
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
            'nama_potongan.required' => ':attribute wajib diisi.',
            'nama_potongan.max' => ':attribute maksimal :max karakter.',
            'jenis_potongan.required' => ':attribute wajib dipilih.',
            'jenis_potongan.in' => ':attribute harus persentase atau nominal.',
            'nilai.required' => ':attribute wajib diisi.',
            'nilai.numeric' => ':attribute harus berupa angka.',
            'nilai.min' => ':attribute minimal :min.',
            'status_aktif.required' => ':attribute wajib dipilih.',
            'status_aktif.boolean' => ':attribute harus berupa true atau false.',
        ];
    }

    /**
     * Additional validation rules based on jenis_potongan.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // BR-04: Validasi nilai persentase tidak boleh lebih dari 100
            if ($this->jenis_potongan === 'persentase' && $this->nilai > 100) {
                $validator->errors()->add(
                    'nilai',
                    'Nilai persentase tidak boleh lebih dari 100.'
                );
            }
        });
    }
}
