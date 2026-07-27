<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRekapAbsensiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        $rekapAbsensi = $this->route('rekap_absensi');
        
        return [
            'karyawan_id' => [
                'required',
                'exists:karyawans,id',
                Rule::unique('rekap_absensis')->where(function ($query) {
                    return $query->where('bulan', $this->bulan)
                                 ->where('tahun', $this->tahun);
                })->ignore($rekapAbsensi),
            ],
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:2020|max:' . (date('Y') + 1),
            'total_hari_kerja' => 'required|integer|min:1|max:31',
            'hadir' => 'required|integer|min:0|max:31',
            'izin' => 'required|integer|min:0|max:31',
            'sakit' => 'required|integer|min:0|max:31',
            'alpha' => 'required|integer|min:0|max:31',
            'terlambat' => 'required|integer|min:0|max:100',
            'lembur' => 'nullable|integer|min:0|max:500',
            'catatan' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'karyawan_id.required' => 'Karyawan harus dipilih',
            'karyawan_id.exists' => 'Karyawan tidak ditemukan',
            'karyawan_id.unique' => 'Rekap absensi untuk karyawan ini pada periode tersebut sudah ada',
            'bulan.required' => 'Bulan harus diisi',
            'bulan.between' => 'Bulan harus antara 1-12',
            'tahun.required' => 'Tahun harus diisi',
            'tahun.min' => 'Tahun minimal 2020',
            'total_hari_kerja.required' => 'Total hari kerja harus diisi',
            'total_hari_kerja.max' => 'Total hari kerja maksimal 31',
            'hadir.required' => 'Jumlah hadir harus diisi',
            'izin.required' => 'Jumlah izin harus diisi',
            'sakit.required' => 'Jumlah sakit harus diisi',
            'alpha.required' => 'Jumlah alpha harus diisi',
            'terlambat.required' => 'Jumlah terlambat harus diisi',
        ];
    }
}
