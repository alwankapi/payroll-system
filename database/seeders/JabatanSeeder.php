<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use Illuminate\Database\Seeder;

class JabatanSeeder extends Seeder
{
    public function run(): void
    {
        $jabatans = [
            ['nama_jabatan' => 'Direktur', 'gaji_pokok' => 15000000, 'tunjangan_jabatan' => 5000000, 'keterangan' => 'Pimpinan tertinggi perusahaan.'],
            ['nama_jabatan' => 'Manager', 'gaji_pokok' => 10000000, 'tunjangan_jabatan' => 2000000, 'keterangan' => 'Bertanggung jawab atas divisi terkait.'],
            ['nama_jabatan' => 'Supervisor', 'gaji_pokok' => 7000000, 'tunjangan_jabatan' => 1200000, 'keterangan' => 'Mengawasi tim operasional harian.'],
            ['nama_jabatan' => 'Staff HR', 'gaji_pokok' => 5000000, 'tunjangan_jabatan' => 800000, 'keterangan' => 'Mengelola sumber daya manusia.'],
            ['nama_jabatan' => 'Staff Keuangan', 'gaji_pokok' => 5500000, 'tunjangan_jabatan' => 900000, 'keterangan' => 'Mengelola pencatatan & pelaporan keuangan.'],
            ['nama_jabatan' => 'Staff IT', 'gaji_pokok' => 6000000, 'tunjangan_jabatan' => 1000000, 'keterangan' => 'Menangani infrastruktur & dukungan teknis.'],
            ['nama_jabatan' => 'Marketing', 'gaji_pokok' => 4500000, 'tunjangan_jabatan' => 700000, 'keterangan' => 'Mengelola pemasaran dan promosi.'],
            ['nama_jabatan' => 'Operator', 'gaji_pokok' => 4000000, 'tunjangan_jabatan' => 500000, 'keterangan' => 'Menjalankan operasional produksi.'],
            ['nama_jabatan' => 'Admin', 'gaji_pokok' => 3500000, 'tunjangan_jabatan' => 400000, 'keterangan' => 'Mengelola administrasi kantor.'],
            ['nama_jabatan' => 'Security', 'gaji_pokok' => 3500000, 'tunjangan_jabatan' => 300000, 'keterangan' => 'Menjaga keamanan perusahaan.'],
        ];

        foreach ($jabatans as $jabatan) {
            Jabatan::firstOrCreate(['nama_jabatan' => $jabatan['nama_jabatan']], $jabatan);
        }
    }
}
