<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use Illuminate\Database\Seeder;

class JabatanSeeder extends Seeder
{
    public function run(): void
    {
        $jabatans = [
            ['nama_jabatan' => 'Staff Administrasi', 'gaji_pokok' => 4500000, 'tunjangan_jabatan' => 500000, 'keterangan' => 'Mengelola administrasi kantor sehari-hari.'],
            ['nama_jabatan' => 'Staff Keuangan', 'gaji_pokok' => 5000000, 'tunjangan_jabatan' => 600000, 'keterangan' => 'Mengelola pencatatan & pelaporan keuangan.'],
            ['nama_jabatan' => 'Supervisor Operasional', 'gaji_pokok' => 6500000, 'tunjangan_jabatan' => 1000000, 'keterangan' => 'Mengawasi tim operasional harian.'],
            ['nama_jabatan' => 'Manager', 'gaji_pokok' => 9000000, 'tunjangan_jabatan' => 1500000, 'keterangan' => 'Bertanggung jawab atas divisi terkait.'],
            ['nama_jabatan' => 'IT Support', 'gaji_pokok' => 5500000, 'tunjangan_jabatan' => 700000, 'keterangan' => 'Menangani infrastruktur & dukungan teknis.'],
        ];

        foreach ($jabatans as $jabatan) {
            Jabatan::firstOrCreate(['nama_jabatan' => $jabatan['nama_jabatan']], $jabatan);
        }
    }
}