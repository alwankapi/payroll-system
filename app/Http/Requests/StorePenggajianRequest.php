<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePenggajianRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // BR-01: Hanya Admin yang dapat create data Penggajian
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
            'karyawan_id' => [
                'required',
                'integer',
                'exists:karyawans,id',
            ],
            'periode' => [
                'required',
                'date',
                'date_format:Y-m-d',
                // BR-05: Validasi unique kombinasi karyawan_id + periode
                Rule::unique('penggajians')->where(function ($query) {
                    return $query->where('karyawan_id', $this->karyawan_id);
                }),
            ],
            'gaji_pokok' => [
                'required',
                'numeric',
                'min:0',
                'max:999999999999.99',
            ],
            'tunjangan' => [
                'required',
                'numeric',
                'min:0',
                'max:999999999999.99',
            ],
            'total_potongan' => [
                'required',
                'numeric',
                'min:0',
                'max:999999999999.99',
            ],
            'gaji_bersih' => [
                'required',
                'numeric',
                'min:0',
                'max:999999999999.99',
            ],
            'status' => [
                'required',
                'in:draft,diproses,disetujui,dibayar,ditolak,dibatalkan',
            ],
            'tanggal_bayar' => [
                'nullable',
                'date',
                'date_format:Y-m-d',
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
            'karyawan_id' => 'Karyawan',
            'periode' => 'Periode',
            'gaji_pokok' => 'Gaji Pokok',
            'tunjangan' => 'Tunjangan',
            'total_potongan' => 'Total Potongan',
            'gaji_bersih' => 'Gaji Bersih',
            'status' => 'Status',
            'tanggal_bayar' => 'Tanggal Bayar',
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
            'karyawan_id.required' => ':attribute wajib dipilih.',
            'karyawan_id.exists' => ':attribute tidak valid.',
            'periode.required' => ':attribute wajib diisi.',
            'periode.date' => ':attribute harus berupa tanggal yang valid.',
            'periode.unique' => 'Data penggajian untuk karyawan dan periode ini sudah ada.',
            'gaji_pokok.required' => ':attribute wajib diisi.',
            'gaji_pokok.numeric' => ':attribute harus berupa angka.',
            'gaji_pokok.min' => ':attribute minimal :min.',
            'tunjangan.required' => ':attribute wajib diisi.',
            'tunjangan.numeric' => ':attribute harus berupa angka.',
            'tunjangan.min' => ':attribute minimal :min.',
            'total_potongan.required' => ':attribute wajib diisi.',
            'total_potongan.numeric' => ':attribute harus berupa angka.',
            'total_potongan.min' => ':attribute minimal :min.',
            'gaji_bersih.required' => ':attribute wajib diisi.',
            'gaji_bersih.numeric' => ':attribute harus berupa angka.',
            'gaji_bersih.min' => ':attribute minimal :min.',
            'status.required' => ':attribute wajib dipilih.',
            'status.in' => ':attribute harus draft, final, atau dibayar.',
            'tanggal_bayar.date' => ':attribute harus berupa tanggal yang valid.',
        ];
    }

    /**
     * Additional validation rules.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // BR-03: Validasi rumus gaji bersih
            $expectedGajiBersih = $this->gaji_pokok + $this->tunjangan - $this->total_potongan;
            
            if (abs($this->gaji_bersih - $expectedGajiBersih) > 0.01) {
                $validator->errors()->add(
                    'gaji_bersih',
                    'Gaji bersih tidak sesuai dengan rumus (Gaji Pokok + Tunjangan - Total Potongan).'
                );
            }

            // Validasi tanggal_bayar hanya boleh diisi jika status dibayar
            if ($this->status === 'dibayar' && empty($this->tanggal_bayar)) {
                $validator->errors()->add(
                    'tanggal_bayar',
                    'Tanggal bayar wajib diisi untuk status dibayar.'
                );
            }
        });
    }
}
