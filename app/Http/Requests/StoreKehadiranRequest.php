<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreKehadiranRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'karyawan_id' => ['required', 'exists:karyawans,id'],
            'tanggal' => [
                'required',
                'date',
                'before_or_equal:today',
                Rule::unique('kehadirans')->where(function ($query) {
                    return $query->where('karyawan_id', $this->karyawan_id);
                }),
            ],
            'status' => ['required', Rule::in(['hadir', 'izin', 'sakit', 'alpha'])],
            'jam_masuk' => ['nullable', 'date_format:H:i', 'required_if:status,hadir'],
            'jam_keluar' => ['nullable', 'date_format:H:i', 'after:jam_masuk'],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'karyawan_id.required' => 'Karyawan harus dipilih.',
            'karyawan_id.exists' => 'Karyawan tidak ditemukan.',
            'tanggal.required' => 'Tanggal harus diisi.',
            'tanggal.date' => 'Format tanggal tidak valid.',
            'tanggal.before_or_equal' => 'Tanggal tidak boleh lebih dari hari ini.',
            'tanggal.unique' => 'Data kehadiran untuk karyawan ini pada tanggal tersebut sudah ada.',
            'status.required' => 'Status kehadiran harus dipilih.',
            'status.in' => 'Status kehadiran tidak valid.',
            'jam_masuk.required_if' => 'Jam masuk harus diisi untuk status hadir.',
            'jam_masuk.date_format' => 'Format jam masuk tidak valid (HH:MM).',
            'jam_keluar.date_format' => 'Format jam keluar tidak valid (HH:MM).',
            'jam_keluar.after' => 'Jam keluar harus setelah jam masuk.',
            'keterangan.max' => 'Keterangan maksimal 500 karakter.',
        ];
    }
}
